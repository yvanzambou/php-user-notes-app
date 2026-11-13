<?php

declare(strict_types=1);

namespace App;

final class Csrf
{
    public static function token(): string
    {
        Session::start();

        return $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
    }

    /** Hidden-Input für Formulare. */
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function isValid(): bool
    {
        $sent = $_POST['_csrf'] ?? '';

        return is_string($sent) && hash_equals(self::token(), $sent);
    }

    /** Bricht mit HTTP 419 ab, wenn das Token fehlt oder falsch ist. */
    public static function requireValid(): void
    {
        if (self::isValid()) {
            return;
        }

        View::render('error', [
            'title'   => 'Anfrage abgelaufen',
            'heading' => 'Anfrage abgelaufen',
            'message' => 'Das Formular ist nicht mehr gültig. Lade die Seite neu und versuche es noch einmal.',
        ], 419);
        exit;
    }

    public static function rotate(): void
    {
        Session::start();
        unset($_SESSION['_csrf']);
    }
}
