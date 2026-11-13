<?php

declare(strict_types=1);

namespace Tests\Repository;

use App\Repository\UserRepository;
use PDOException;
use Tests\Support\DatabaseTestCase;

final class UserRepositoryTest extends DatabaseTestCase
{
    private UserRepository $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->users = new UserRepository($this->db);
    }

    public function testStoresPasswordHashColumn(): void
    {
        $id = $this->users->create('Yvan Zambou', 'yvan', 'yvan@example.de', 'hash-value');
        $user = $this->users->findById($id);

        self::assertSame('hash-value', $user['password_hash']);
        self::assertSame('Yvan Zambou', $user['name']);
        self::assertArrayNotHasKey('password', $user);
    }

    public function testFindsByUsernameOrEmailCaseInsensitive(): void
    {
        $id = $this->users->create('Yvan', 'Yvan.Z', 'yvan@example.de', 'h');

        self::assertSame($id, (int) $this->users->findByLogin('yvan.z')['id']);
        self::assertSame($id, (int) $this->users->findByLogin('YVAN@EXAMPLE.DE')['id']);
        self::assertNull($this->users->findByLogin('unbekannt'));
    }

    public function testExistenceChecksIgnoreCase(): void
    {
        $this->users->create('Yvan', 'Yvan.Z', 'yvan@example.de', 'h');

        self::assertTrue($this->users->usernameExists('YVAN.z'));
        self::assertTrue($this->users->emailExists('Yvan@Example.de'));
        self::assertFalse($this->users->usernameExists('anderer'));
        self::assertFalse($this->users->emailExists('anderer@example.de'));
    }

    public function testDatabaseRejectsDuplicateEmail(): void
    {
        $this->users->create('A', 'a-user', 'same@example.de', 'h');

        $this->expectException(PDOException::class);
        $this->users->create('B', 'b-user', 'same@example.de', 'h');
    }

    public function testUpdatePasswordHash(): void
    {
        $id = $this->users->create('A', 'a-user', 'a@example.de', 'alt');
        $this->users->updatePasswordHash($id, 'neu');

        self::assertSame('neu', $this->users->findById($id)['password_hash']);
    }
}
