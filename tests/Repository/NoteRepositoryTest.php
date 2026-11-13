<?php

declare(strict_types=1);

namespace Tests\Repository;

use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Tests\Support\DatabaseTestCase;

final class NoteRepositoryTest extends DatabaseTestCase
{
    private NoteRepository $notes;
    private int $alice;
    private int $bob;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notes = new NoteRepository($this->db);

        $users = new UserRepository($this->db);
        $this->alice = $users->create('Alice', 'alice', 'alice@example.com', 'hash');
        $this->bob = $users->create('Bob', 'bob', 'bob@example.com', 'hash');
    }

    public function testCreateAndFind(): void
    {
        $id = $this->notes->create($this->alice, 'Erste Notiz');

        self::assertSame('Erste Notiz', $this->notes->find($id, $this->alice)['content'] ?? null);
    }

    public function testListsOnlyOwnNotes(): void
    {
        $this->notes->create($this->alice, 'von Alice');
        $this->notes->create($this->bob, 'von Bob');

        $contents = array_column($this->notes->forUser($this->alice), 'content');

        self::assertSame(['von Alice'], $contents);
    }

    public function testNewestNoteComesFirst(): void
    {
        $this->notes->create($this->alice, 'alt');
        $this->notes->create($this->alice, 'neu');

        self::assertSame(['neu', 'alt'], array_column($this->notes->forUser($this->alice), 'content'));
    }

    public function testUpdateChangesContent(): void
    {
        $id = $this->notes->create($this->alice, 'vorher');

        self::assertTrue($this->notes->update($id, $this->alice, 'nachher'));
        self::assertSame('nachher', $this->notes->find($id, $this->alice)['content']);
    }

    public function testCannotReadUpdateOrDeleteForeignNote(): void
    {
        $id = $this->notes->create($this->alice, 'privat');

        self::assertNull($this->notes->find($id, $this->bob));
        self::assertFalse($this->notes->update($id, $this->bob, 'gehackt'));
        self::assertFalse($this->notes->delete($id, $this->bob));
        self::assertSame('privat', $this->notes->find($id, $this->alice)['content']);
    }

    public function testDeleteRemovesNote(): void
    {
        $id = $this->notes->create($this->alice, 'weg damit');

        self::assertTrue($this->notes->delete($id, $this->alice));
        self::assertNull($this->notes->find($id, $this->alice));
        self::assertFalse($this->notes->delete($id, $this->alice));
    }

    public function testDeletingUserRemovesTheirNotes(): void
    {
        $this->notes->create($this->alice, 'wird mitgelöscht');
        $this->db->exec('DELETE FROM users WHERE id = ' . $this->alice);

        self::assertSame([], $this->notes->forUser($this->alice));
    }
}
