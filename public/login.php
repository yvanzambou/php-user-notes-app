<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Auth;
use App\Csrf;
use App\Database;
use App\Http\Http;
use App\Repository\UserRepository;
use App\View;

Auth::requireGuest();

$login = '';
$error = null;

if (Http::isPost()) {
    Csrf::requireValid();

    $login = trim((string) ($_POST['login'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $users = new UserRepository(Database::connection());
    $user = $login !== '' ? $users->findByLogin($login) : null;

    if (Auth::verifyPassword($user, $password)) {
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $users->updatePasswordHash((int) $user['id'], password_hash($password, PASSWORD_DEFAULT));
        }
        Auth::login($user);
        Http::redirect('/notes.php');
    }

    $error = 'Benutzername oder Passwort ist falsch.';
}

View::render('login', ['title' => 'Anmelden', 'login' => $login, 'error' => $error], $error ? 401 : 200);
