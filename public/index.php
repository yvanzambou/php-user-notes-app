<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Auth;
use App\Http\Http;

Http::redirect(Auth::user() !== null ? '/notes.php' : '/login.php');
