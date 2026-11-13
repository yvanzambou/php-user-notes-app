<?php

declare(strict_types=1);

namespace Tests\Support;

use PDO;
use PHPUnit\Framework\TestCase;
use Throwable;

/** Basisklasse für Tests, die eine echte Datenbank brauchen. Ohne erreichbare DB werden sie übersprungen. */
abstract class DatabaseTestCase extends TestCase
{
    protected PDO $db;

    protected function setUp(): void
    {
        try {
            $this->db = TestDatabase::create();
        } catch (Throwable $e) {
            self::markTestSkipped(
                'Keine Test-Datenbank erreichbar (' . $e->getMessage() . '). '
                . 'Tests im Container ausführen: docker compose exec app composer test'
            );
        }
    }
}
