<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Auth;
use App\Csrf;
use App\Database;
use App\Flash;
use App\Http\Http;
use App\Repository\UserRepository;
use App\Validation\RegistrationValidator;
use App\View;

Auth::requireGuest();

$values = ['name' => '', 'username' => '', 'email' => '', 'password' => ''];
$errors = [];

if (Http::isPost()) {
    Csrf::requireValid();

    ['values' => $values, 'errors' => $errors] = RegistrationValidator::validate($_POST);
    $users = new UserRepository(Database::connection());

    if ($errors === []) {
        if ($users->usernameExists($values['username'])) {
            $errors['username'] = 'Dieser Benutzername ist bereits vergeben.';
        }
        if ($users->emailExists($values['email'])) {
            $errors['email'] = 'Mit dieser E-Mail-Adresse gibt es schon ein Konto.';
        }
    }

    if ($errors === []) {
        try {
            $users->create(
                $values['name'],
                $values['username'],
                $values['email'],
                password_hash($values['password'], PASSWORD_DEFAULT),
            );
            Flash::success('Dein Konto wurde erstellt. Du kannst dich jetzt anmelden.');
            Http::redirect('/login.php');
        } catch (PDOException $e) {
            // SQLSTATE 23000: Ein paralleler Request war schneller (UNIQUE-Constraint der Datenbank).
            if ($e->getCode() !== '23000') {
                throw $e;
            }
            $errors['username'] = 'Benutzername oder E-Mail-Adresse sind bereits vergeben.';
        }
    }

    $values['password'] = ''; // Passwörter werden nie wieder ins Formular geschrieben
}

View::render('register', ['title' => 'Konto erstellen', 'values' => $values, 'errors' => $errors], $errors ? 422 : 200);
