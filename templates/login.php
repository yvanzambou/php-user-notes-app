<?php /** @var string $login */ /** @var ?string $error */ ?>
<section class="card card--narrow">
    <h1>Anmelden</h1>

    <?php if ($error): ?>
        <p class="alert" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <form action="/login.php" method="post" class="form" novalidate>
        <?= App\Csrf::field() ?>

        <div class="field">
            <label for="login">Benutzername oder E-Mail</label>
            <input id="login" name="login" type="text" value="<?= e($login) ?>"
                   autocomplete="username" autocapitalize="none" spellcheck="false" required autofocus>
        </div>

        <div class="field">
            <label for="password">Passwort</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required>
        </div>

        <button type="submit" class="btn btn--primary">Anmelden</button>
    </form>

    <p class="card__footer">Noch kein Konto? <a href="/register.php">Jetzt registrieren</a></p>
</section>
