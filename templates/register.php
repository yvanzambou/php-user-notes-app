<?php /** @var array<string,string> $values */ /** @var array<string,string> $errors */

/** Gemeinsame Attribute für Felder mit Fehleranzeige. */
$aria = static fn (string $field): string => isset($errors[$field])
    ? ' aria-invalid="true" aria-describedby="' . $field . '-error"'
    : ($field === 'password' || $field === 'username' ? ' aria-describedby="' . $field . '-hint"' : '');
?>
<section class="card card--narrow">
    <h1>Konto erstellen</h1>

    <form action="/register.php" method="post" class="form" novalidate>
        <?= App\Csrf::field() ?>

        <div class="field">
            <label for="name">Name</label>
            <input id="name" name="name" type="text" value="<?= e($values['name']) ?>"
                   autocomplete="name" maxlength="100" required<?= $aria('name') ?>>
            <?php if (isset($errors['name'])): ?><p class="field__error" id="name-error"><?= e($errors['name']) ?></p><?php endif; ?>
        </div>

        <div class="field">
            <label for="username">Benutzername</label>
            <input id="username" name="username" type="text" value="<?= e($values['username']) ?>"
                   autocomplete="username" autocapitalize="none" spellcheck="false"
                   minlength="3" maxlength="30" pattern="[A-Za-z0-9._\-]{3,30}" required<?= $aria('username') ?>>
            <?php if (isset($errors['username'])): ?>
                <p class="field__error" id="username-error"><?= e($errors['username']) ?></p>
            <?php else: ?>
                <p class="field__hint" id="username-hint">3–30 Zeichen: Buchstaben, Ziffern, Punkt, Unterstrich, Bindestrich.</p>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="email">E-Mail-Adresse</label>
            <input id="email" name="email" type="email" value="<?= e($values['email']) ?>"
                   autocomplete="email" autocapitalize="none" spellcheck="false" maxlength="254" required<?= $aria('email') ?>>
            <?php if (isset($errors['email'])): ?><p class="field__error" id="email-error"><?= e($errors['email']) ?></p><?php endif; ?>
        </div>

        <div class="field">
            <label for="password">Passwort</label>
            <input id="password" name="password" type="password" autocomplete="new-password"
                   minlength="8" maxlength="72" required<?= $aria('password') ?>>
            <?php if (isset($errors['password'])): ?>
                <p class="field__error" id="password-error"><?= e($errors['password']) ?></p>
            <?php else: ?>
                <p class="field__hint" id="password-hint">Mindestens 8 Zeichen.</p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn--primary">Registrieren</button>
    </form>

    <p class="card__footer">Schon registriert? <a href="/login.php">Anmelden</a></p>
</section>
