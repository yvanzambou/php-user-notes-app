<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

/** Schützt vor Rückfällen im Datenbankschema (z. B. Spalte "password" statt "password_hash"). */
final class SchemaTest extends TestCase
{
    private string $schema;

    protected function setUp(): void
    {
        $this->schema = (string) file_get_contents(__DIR__ . '/../database/schema.sql');
    }

    public function testUsersTableStoresPasswordHash(): void
    {
        self::assertMatchesRegularExpression('/\bpassword_hash\s+VARCHAR\(255\)\s+NOT NULL/i', $this->schema);
        self::assertDoesNotMatchRegularExpression('/^\s*password\s+VARCHAR/im', $this->schema);
    }

    public function testUsersTableHasRegistrationFields(): void
    {
        foreach (['name', 'username', 'email'] as $column) {
            self::assertMatchesRegularExpression('/^\s*' . $column . '\s+VARCHAR/im', $this->schema, $column);
        }
        self::assertStringContainsString('UNIQUE (email)', $this->schema);
        self::assertStringContainsString('UNIQUE (username)', $this->schema);
    }

    public function testNotesReferenceUsersById(): void
    {
        self::assertStringContainsString('FOREIGN KEY (user_id)', $this->schema);
        self::assertStringContainsString('ON DELETE CASCADE', $this->schema);
    }
}
