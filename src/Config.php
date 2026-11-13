<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

/** Zugriff auf Umgebungsvariablen (werden von Docker Compose gesetzt). */
final class Config
{
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = getenv($key);

        return ($value === false || $value === '') ? $default : $value;
    }

    public static function getRequired(string $key): string
    {
        return self::get($key) ?? throw new RuntimeException("Umgebungsvariable {$key} fehlt.");
    }

    public static function isDevelopment(): bool
    {
        return self::get('APP_ENV', 'production') === 'development';
    }
}
