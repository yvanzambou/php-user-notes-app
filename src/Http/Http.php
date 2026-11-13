<?php

declare(strict_types=1);

namespace App\Http;

use App\Session;
use App\View;

final class Http
{
    public static function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /** Redirect auf einen internen Pfad. */
    public static function redirect(string $path): never
    {
        header('Location: ' . $path);
        exit;
    }

    public static function notFound(): never
    {
        View::render('error', [
            'title'   => 'Nicht gefunden',
            'heading' => 'Seite nicht gefunden',
            'message' => 'Diese Seite oder Notiz gibt es nicht (mehr).',
        ], 404);
        exit;
    }

    public static function methodNotAllowed(): never
    {
        header('Allow: POST');
        View::render('error', [
            'title'   => 'Nicht erlaubt',
            'heading' => 'Methode nicht erlaubt',
            'message' => 'Diese Aktion ist nur über das Formular möglich.',
        ], 405);
        exit;
    }

    public static function sendSecurityHeaders(): void
    {
        header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; "
            . "base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

        if (Session::isHttps()) {
            header('Strict-Transport-Security: max-age=31536000');
        }
    }
}
