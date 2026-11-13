<?php /** @var list<array<string,mixed>> $notes */ /** @var string $draft */ /** @var ?string $error */ ?>
<section class="composer">
    <h1>Meine Notizen</h1>
    <form action="/notes.php" method="post" class="form">
        <?= App\Csrf::field() ?>
        <div class="field">
            <label for="note" class="visually-hidden">Neue Notiz</label>
            <textarea id="note" name="note" rows="4" maxlength="<?= App\Validation\NoteValidator::MAX_LENGTH ?>"
                      placeholder="Was möchtest du festhalten?" required
                      <?= $error ? 'aria-invalid="true" aria-describedby="note-error"' : '' ?>><?= e($draft) ?></textarea>
            <?php if ($error): ?><p class="field__error" id="note-error"><?= e($error) ?></p><?php endif; ?>
        </div>
        <div class="composer__actions">
            <span class="field__hint">Strg + Enter speichert</span>
            <button type="submit" class="btn btn--primary">Notiz speichern</button>
        </div>
    </form>
</section>

<section aria-label="Gespeicherte Notizen">
    <?php if ($notes === []): ?>
        <div class="empty">
            <p>Noch keine Notizen.</p>
            <p class="field__hint">Schreib oben deine erste Notiz und speichere sie.</p>
        </div>
    <?php else: ?>
        <ul class="notes">
            <?php foreach ($notes as $note): ?>
                <li class="note">
                    <p class="note__content"><?= nl2br(e($note['content'])) ?></p>
                    <div class="note__footer">
                        <p class="note__meta">
                            <time datetime="<?= e(str_replace(' ', 'T', $note['created_at'])) ?>Z"><?= e(format_datetime($note['created_at'])) ?></time>
                            <?php if ($note['updated_at'] > $note['created_at']): ?>
                                <span>· bearbeitet <?= e(format_datetime($note['updated_at'])) ?></span>
                            <?php endif; ?>
                        </p>
                        <div class="note__actions">
                            <a class="btn btn--ghost" href="/note-edit.php?id=<?= (int) $note['id'] ?>">Bearbeiten</a>
                            <form action="/note-delete.php" method="post" data-confirm="Diese Notiz endgültig löschen?">
                                <?= App\Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= (int) $note['id'] ?>">
                                <button type="submit" class="btn btn--danger">Löschen</button>
                            </form>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
