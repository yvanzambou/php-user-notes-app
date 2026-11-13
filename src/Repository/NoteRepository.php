<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

/**
 * Alle Zugriffe sind auf user_id eingeschränkt: Ein Benutzer kann fremde Notizen
 * weder lesen noch ändern oder löschen, selbst wenn er deren ID kennt.
 */
final class NoteRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /** @return list<array<string, mixed>> */
    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, content, created_at, updated_at FROM notes
             WHERE user_id = :user_id ORDER BY created_at DESC, id DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function find(int $id, int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, content, created_at, updated_at FROM notes WHERE id = :id AND user_id = :user_id'
        );
        $stmt->execute(['id' => $id, 'user_id' => $userId]);

        return $stmt->fetch() ?: null;
    }

    public function create(int $userId, string $content): int
    {
        $stmt = $this->db->prepare('INSERT INTO notes (user_id, content) VALUES (:user_id, :content)');
        $stmt->execute(['user_id' => $userId, 'content' => $content]);

        return (int) $this->db->lastInsertId();
    }

    /** @return bool true, wenn die Notiz dem Benutzer gehört und aktualisiert wurde */
    public function update(int $id, int $userId, string $content): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE notes SET content = :content, updated_at = CURRENT_TIMESTAMP
             WHERE id = :id AND user_id = :user_id'
        );
        $stmt->execute(['content' => $content, 'id' => $id, 'user_id' => $userId]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM notes WHERE id = :id AND user_id = :user_id');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);

        return $stmt->rowCount() > 0;
    }
}
