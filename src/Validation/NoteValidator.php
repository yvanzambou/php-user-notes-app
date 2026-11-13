<?php

declare(strict_types=1);

namespace App\Validation;

final class NoteValidator
{
    public const MAX_LENGTH = 5000;

    /**
     * @param array<string, mixed> $input
     * @return array{content: string, error: ?string}
     */
    public static function validate(array $input): array
    {
        $raw = $input['note'] ?? '';
        $content = is_string($raw) ? trim(str_replace("\r\n", "\n", $raw)) : '';

        if ($content === '') {
            return ['content' => $content, 'error' => 'Eine Notiz darf nicht leer sein.'];
        }
        if (mb_strlen($content) > self::MAX_LENGTH) {
            return ['content' => $content, 'error' => 'Eine Notiz darf höchstens ' . self::MAX_LENGTH . ' Zeichen lang sein.'];
        }

        return ['content' => $content, 'error' => null];
    }
}
