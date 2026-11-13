<?php

declare(strict_types=1);

use App\Config;
use App\Http\Http;
use App\View;

require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set(Config::get('APP_TIMEZONE', 'Europe/Berlin'));

ini_set('display_errors', '0');
ini_set('log_errors', '1');

set_exception_handler(static function (Throwable $e): void {
    error_log((string) $e);

    if (headers_sent()) {
        return;
    }

    try {
        View::render('error', [
            'title'   => 'Serverfehler',
            'heading' => 'Da ist etwas schiefgelaufen',
            'message' => Config::isDevelopment()
                ? $e->getMessage()
                : 'Die Seite konnte gerade nicht geladen werden. Bitte versuche es später noch einmal.',
        ], 500);
    } catch (Throwable) {
        http_response_code(500);
        echo 'Serverfehler';
    }
});

Http::sendSecurityHeaders();
