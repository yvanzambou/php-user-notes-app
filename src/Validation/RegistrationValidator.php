<?php

declare(strict_types=1);

namespace App\Validation;

final class RegistrationValidator
{
    public const PASSWORD_MIN = 8;
    public const PASSWORD_MAX = 72; // bcrypt verarbeitet nur die ersten 72 Bytes

    /**
     * @param array<string, mixed> $input
     * @return array{values: array{name: string, username: string, email: string, password: string}, errors: array<string, string>}
     */
    public static function validate(array $input): array
    {
        $values = [
            'name'     => self::normalizeName(self::string($input, 'name')),
            'username' => trim(self::string($input, 'username')),
            'email'    => self::normalizeEmail(self::string($input, 'email')),
            'password' => self::string($input, 'password'), // bewusst nicht getrimmt
        ];
        $errors = [];

        $nameLength = mb_strlen($values['name']);
        if ($values['name'] === '') {
            $errors['name'] = 'Bitte gib deinen Namen an.';
        } elseif ($nameLength < 2 || $nameLength > 100 || preg_match('/[\p{C}]/u', $values['name'])) {
            $errors['name'] = 'Der Name muss 2 bis 100 Zeichen lang sein und darf keine Steuerzeichen enthalten.';
        }

        if (!preg_match('/^[A-Za-z0-9._-]{3,30}$/', $values['username'])) {
            $errors['username'] = 'Der Benutzername braucht 3 bis 30 Zeichen: Buchstaben, Ziffern, Punkt, Unterstrich oder Bindestrich.';
        }

        if (!self::isValidEmail($values['email'])) {
            $errors['email'] = 'Bitte gib eine gültige E-Mail-Adresse ein, z. B. name@beispiel.de.';
        }

        $passwordBytes = strlen($values['password']);
        if ($passwordBytes < self::PASSWORD_MIN) {
            $errors['password'] = 'Das Passwort muss mindestens ' . self::PASSWORD_MIN . ' Zeichen lang sein.';
        } elseif ($passwordBytes > self::PASSWORD_MAX) {
            $errors['password'] = 'Das Passwort darf höchstens ' . self::PASSWORD_MAX . ' Zeichen lang sein.';
        }

        return ['values' => $values, 'errors' => $errors];
    }

    public static function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    /**
     * Syntaxprüfung nach RFC (filter_var) plus Mindestanforderungen, die filter_var allein
     * nicht abdeckt: Länge, Domain mit Punkt und gültiger TLD (also kein "max@localhost").
     */
    public static function isValidEmail(string $email): bool
    {
        if ($email === '' || strlen($email) > 254) {
            return false;
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        $at = strrpos($email, '@');
        $local = substr($email, 0, $at);
        $domain = substr($email, $at + 1);

        return strlen($local) <= 64
            && preg_match('/\.(?=[a-z0-9-]*[a-z])[a-z0-9-]{2,}$/i', $domain) === 1;
    }

    private static function normalizeName(string $name): string
    {
        return trim(preg_replace('/\s+/u', ' ', $name) ?? $name);
    }

    /** @param array<string, mixed> $input */
    private static function string(array $input, string $key): string
    {
        $value = $input[$key] ?? '';

        return is_string($value) ? $value : '';
    }
}
