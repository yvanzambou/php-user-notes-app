<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Auth;
use App\Csrf;
use App\Flash;
use App\Http\Http;

if (!Http::isPost()) {
    Http::methodNotAllowed();
}
Csrf::requireValid();

Auth::logout();
Flash::success('Du wurdest abgemeldet.');
Http::redirect('/login.php');
