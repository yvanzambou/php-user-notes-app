<?php

declare(strict_types=1);

namespace App;

use PDO;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        return self::$connection ??= self::connect();
    }

    /** Erlaubt es Tests, eine eigene Verbindung einzusetzen. */
    public static function setConnection(?PDO $connection): void
    {
        self::$connection = $connection;
    }

    /** Öffnet eine MariaDB-Verbindung mit den Einstellungen, die die App überall verwendet. */
    public static function create(string $host, string $port, string $name, string $user, string $password): PDO
    {
        return new PDO(
            sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name),
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_FOUND_ROWS   => true, // rowCount() = gefundene statt geänderte Zeilen
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '+00:00'",
            ],
        );
    }

    private static function connect(): PDO
    {
        return self::create(
            Config::getRequired('DB_HOST'),
            Config::get('DB_PORT', '3306'),
            Config::getRequired('DB_NAME'),
            Config::getRequired('DB_USER'),
            Config::getRequired('DB_PASS'),
        );
    }
}
