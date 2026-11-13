<?php /** @var array<string,mixed> $note */ /** @var string $draft */ /** @var ?string $error */ ?>
<section class="card">
    <h1>Notiz bearbeiten</h1>
    <p class="note__meta">Erstellt am <?= e(format_datetime($note['created_at'])) ?></p>

    <form action="/note-edit.php?id=<?= (int) $note['id'] ?>" method="post" class="form">
        <?= App\Csrf::field() ?>
        <div class="field">
            <label for="note" class="visually-hidden">Text der Notiz</label>
            <textarea id="note" name="note" rows="10" maxlength="<?= App\Validation\NoteValidator::MAX_LENGTH ?>" required autofocus
                      <?= $error ? 'aria-invalid="true" aria-describedby="note-error"' : '' ?>><?= e($draft) ?></textarea>
            <?php if ($error): ?><p class="field__error" id="note-error"><?= e($error) ?></p><?php endif; ?>
        </div>
        <div class="composer__actions">
            <a class="btn btn--ghost" href="/notes.php">Abbrechen</a>
            <button type="submit" class="btn btn--primary">Änderungen speichern</button>
        </div>
    </form>
</section>
