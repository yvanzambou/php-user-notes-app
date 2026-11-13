<?php

declare(strict_types=1);

namespace App;

/** Einmalige Statusmeldungen, die nach einem Redirect angezeigt werden. */
final class Flash
{
    public static function success(string $message): void
    {
        self::add('success', $message);
    }

    public static function error(string $message): void
    {
        self::add('error', $message);
    }

    /** @return list<array{type: string, message: string}> */
    public static function pull(): array
    {
        Session::start();
        $messages = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);

        return $messages;
    }

    private static function add(string $type, string $message): void
    {
        Session::start();
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }
}
