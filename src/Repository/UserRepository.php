<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class UserRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function create(string $name, string $username, string $email, string $passwordHash): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, username, email, password_hash)
             VALUES (:name, :username, :email, :password_hash)'
        );
        $stmt->execute([
            'name'          => $name,
            'username'      => $username,
            'email'         => $email,
            'password_hash' => $passwordHash,
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** @return array<string, mixed>|null */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Sucht per Benutzername oder E-Mail-Adresse (Groß-/Kleinschreibung egal).
     *
     * @return array<string, mixed>|null
     */
    public function findByLogin(string $login): ?array
    {
        $login = mb_strtolower($login);
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE LOWER(username) = :username OR LOWER(email) = :email LIMIT 1'
        );
        $stmt->execute(['username' => $login, 'email' => $login]);

        return $stmt->fetch() ?: null;
    }

    public function usernameExists(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE LOWER(username) = :username');
        $stmt->execute(['username' => mb_strtolower($username)]);

        return $stmt->fetchColumn() !== false;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE LOWER(email) = :email');
        $stmt->execute(['email' => mb_strtolower($email)]);

        return $stmt->fetchColumn() !== false;
    }

    public function updatePasswordHash(int $id, string $hash): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
        $stmt->execute(['hash' => $hash, 'id' => $id]);
    }
}
