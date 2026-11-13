<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Auth;
use App\Csrf;
use App\Database;
use App\Flash;
use App\Http\Http;
use App\Repository\NoteRepository;
use App\Validation\NoteValidator;
use App\View;

$user = Auth::requireUser();
$notes = new NoteRepository(Database::connection());

$draft = '';
$error = null;

if (Http::isPost()) {
    Csrf::requireValid();

    $result = NoteValidator::validate($_POST);
    if ($result['error'] === null) {
        $notes->create((int) $user['id'], $result['content']);
        Flash::success('Notiz gespeichert.');
        Http::redirect('/notes.php'); // Post/Redirect/Get: kein doppeltes Speichern beim Neuladen
    }

    $draft = $result['content'];
    $error = $result['error'];
}

View::render('notes', [
    'title' => 'Meine Notizen',
    'notes' => $notes->forUser((int) $user['id']),
    'draft' => $draft,
    'error' => $error,
], $error ? 422 : 200);
