<?php /** @var string $title */ /** @var string $content */ /** @var ?array $user */ /** @var array $flashes */ ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark">
    <title><?= e($title) ?> · Notizen</title>
    <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
    <link rel="preload" href="/assets/fonts/newsreader-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="/assets/js/app.js" defer></script>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="/">Notizen</a>
        <?php if ($user): ?>
            <div class="topbar__user">
                <span class="topbar__name"><?= e($user['name']) ?></span>
                <form action="/logout.php" method="post">
                    <?= App\Csrf::field() ?>
                    <button type="submit" class="btn btn--ghost">Abmelden</button>
                </form>
            </div>
        <?php endif; ?>
    </header>

    <main class="page">
        <?php if ($flashes): ?>
            <div class="flashes" role="status">
                <?php foreach ($flashes as $flash): ?>
                    <p class="flash flash--<?= e($flash['type']) ?>" data-flash><?= e($flash['message']) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>
</body>
</html>
