<?php

declare(strict_types=1);

namespace Tests\Validation;

use App\Validation\RegistrationValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RegistrationValidatorTest extends TestCase
{
    /** @return array<string, string> */
    private static function validInput(): array
    {
        return [
            'name'     => 'Yvan Zambou',
            'username' => 'yvan.z',
            'email'    => 'yvan@example.de',
            'password' => 'correct horse battery',
        ];
    }

    public function testAcceptsValidInput(): void
    {
        $result = RegistrationValidator::validate(self::validInput());

        self::assertSame([], $result['errors']);
        self::assertSame('Yvan Zambou', $result['values']['name']);
    }

    public function testNormalizesNameAndEmail(): void
    {
        $result = RegistrationValidator::validate([
            'name'  => "  Yvan   \t Zambou ",
            'email' => '  Yvan@Example.DE ',
        ] + self::validInput());

        self::assertSame('Yvan Zambou', $result['values']['name']);
        self::assertSame('yvan@example.de', $result['values']['email']);
        self::assertSame([], $result['errors']);
    }

    /** @return iterable<string, array{string}> */
    public static function validEmails(): iterable
    {
        yield 'einfach'         => ['name@beispiel.de'];
        yield 'mit Plus'        => ['name+notizen@beispiel.de'];
        yield 'mit Punkt'       => ['vor.nach@sub.beispiel.co.uk'];
        yield 'Bindestrich'     => ['a-b@my-domain.io'];
    }

    #[DataProvider('validEmails')]
    public function testAcceptsValidEmails(string $email): void
    {
        self::assertTrue(RegistrationValidator::isValidEmail($email));
    }

    /** @return iterable<string, array{string}> */
    public static function invalidEmails(): iterable
    {
        yield 'leer'               => [''];
        yield 'ohne @'             => ['name.beispiel.de'];
        yield 'ohne Domain'        => ['name@'];
        yield 'ohne Local-Part'    => ['@beispiel.de'];
        yield 'ohne TLD'           => ['max@localhost'];
        yield 'TLD nur Ziffern'    => ['max@beispiel.123'];
        yield 'TLD zu kurz'        => ['max@beispiel.d'];
        yield 'zwei @'             => ['a@b@beispiel.de'];
        yield 'Leerzeichen'        => ['na me@beispiel.de'];
        yield 'doppelter Punkt'    => ['na..me@beispiel.de'];
        yield 'Local-Part zu lang' => [str_repeat('a', 65) . '@beispiel.de'];
        yield 'insgesamt zu lang'  => [str_repeat('a', 60) . '@' . str_repeat('b', 190) . '.de'];
    }

    #[DataProvider('invalidEmails')]
    public function testRejectsInvalidEmails(string $email): void
    {
        self::assertFalse(RegistrationValidator::isValidEmail($email));
    }

    public function testReportsInvalidEmailAsFieldError(): void
    {
        $result = RegistrationValidator::validate(['email' => 'kaputt'] + self::validInput());

        self::assertSame(['email'], array_keys($result['errors']));
    }

    /** @return iterable<string, array{string}> */
    public static function invalidUsernames(): iterable
    {
        yield 'zu kurz'        => ['ab'];
        yield 'zu lang'        => [str_repeat('a', 31)];
        yield 'Leerzeichen'    => ['mein name'];
        yield 'Umlaut'         => ['jürgen'];
        yield 'Sonderzeichen'  => ['name!'];
        yield 'leer'           => [''];
    }

    #[DataProvider('invalidUsernames')]
    public function testRejectsInvalidUsernames(string $username): void
    {
        $result = RegistrationValidator::validate(['username' => $username] + self::validInput());

        self::assertArrayHasKey('username', $result['errors']);
    }

    public function testRejectsMissingName(): void
    {
        $result = RegistrationValidator::validate(['name' => '   '] + self::validInput());

        self::assertArrayHasKey('name', $result['errors']);
    }

    public function testRejectsControlCharactersInName(): void
    {
        $result = RegistrationValidator::validate(['name' => "Yvan\x00Zambou"] + self::validInput());

        self::assertArrayHasKey('name', $result['errors']);
    }

    public function testEnforcesPasswordLength(): void
    {
        $short = RegistrationValidator::validate(['password' => '1234567'] + self::validInput());
        $long  = RegistrationValidator::validate(['password' => str_repeat('a', 73)] + self::validInput());
        $edge  = RegistrationValidator::validate(['password' => str_repeat('a', 72)] + self::validInput());

        self::assertArrayHasKey('password', $short['errors']);
        self::assertArrayHasKey('password', $long['errors']);
        self::assertSame([], $edge['errors']);
    }

    public function testDoesNotTrimPassword(): void
    {
        $result = RegistrationValidator::validate(['password' => '  abcdef  '] + self::validInput());

        self::assertSame('  abcdef  ', $result['values']['password']);
    }

    public function testIgnoresNonStringInput(): void
    {
        $result = RegistrationValidator::validate(['name' => ['x'], 'email' => ['x']] + self::validInput());

        self::assertArrayHasKey('name', $result['errors']);
        self::assertArrayHasKey('email', $result['errors']);
    }
}
