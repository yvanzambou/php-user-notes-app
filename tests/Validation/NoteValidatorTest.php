<?php

declare(strict_types=1);

namespace Tests\Validation;

use App\Validation\NoteValidator;
use PHPUnit\Framework\TestCase;

final class NoteValidatorTest extends TestCase
{
    public function testAcceptsAndTrimsContent(): void
    {
        $result = NoteValidator::validate(['note' => "  Milch kaufen\r\nBrot  "]);

        self::assertNull($result['error']);
        self::assertSame("Milch kaufen\nBrot", $result['content']);
    }

    public function testRejectsEmptyNote(): void
    {
        self::assertNotNull(NoteValidator::validate(['note' => " \n "])['error']);
        self::assertNotNull(NoteValidator::validate([])['error']);
    }

    public function testRejectsNonStringInput(): void
    {
        self::assertNotNull(NoteValidator::validate(['note' => ['a']])['error']);
    }

    public function testEnforcesMaximumLengthInCharacters(): void
    {
        $atLimit = str_repeat('ä', NoteValidator::MAX_LENGTH); // Zeichen, nicht Bytes
        $tooLong = $atLimit . 'x';

        self::assertNull(NoteValidator::validate(['note' => $atLimit])['error']);
        self::assertNotNull(NoteValidator::validate(['note' => $tooLong])['error']);
    }
}
