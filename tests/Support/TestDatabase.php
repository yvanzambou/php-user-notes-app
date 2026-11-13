<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Config;
use App\Database;
use PDO;

/**
 * Verbindung zu einer echten MariaDB für die Repository-Tests.
 *
 * Verwendet dieselben Variablen wie die App (DB_HOST, DB_USER, DB_PASS …), aber eine eigene
 * Datenbank, damit die Entwicklungsdaten nie angefasst werden: TEST_DB_NAME, sonst "<DB_NAME>_test".
 * Vor jedem Test wird das Schema aus database/schema.sql frisch angelegt.
 */
final class TestDatabase
{
    public static function create(): PDO
    {
        $name = Config::get('TEST_DB_NAME') ?? Config::getRequired('DB_NAME') . '_test';

        $pdo = Database::create(
            Config::get('TEST_DB_HOST') ?? Config::getRequired('DB_HOST'),
            Config::get('TEST_DB_PORT') ?? Config::get('DB_PORT', '3306'),
            $name,
            Config::get('TEST_DB_USER') ?? Config::getRequired('DB_USER'),
            Config::get('TEST_DB_PASS') ?? Config::getRequired('DB_PASS'),
        );

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $pdo->exec('DROP TABLE IF EXISTS notes, users');
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        foreach (self::schemaStatements() as $statement) {
            $pdo->exec($statement);
        }

        return $pdo;
    }

    /**
     * Einzelne Statements aus database/schema.sql (ohne Kommentare).
     * Einzeln ausgeführt, damit ein Fehler in einem Statement nicht still verschluckt wird.
     *
     * @return list<string>
     */
    private static function schemaStatements(): array
    {
        $sql = (string) file_get_contents(__DIR__ . '/../../database/schema.sql');
        $sql = (string) preg_replace('/^\s*--.*$/m', '', $sql);

        return array_values(array_filter(array_map('trim', explode(';', $sql)), static fn (string $s): bool => $s !== ''));
    }
}
