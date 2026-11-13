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

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$note = $id ? $notes->find($id, (int) $user['id']) : null;
if ($note === null) {
    Http::notFound(); // auch für fremde Notizen – so verrät die App nicht, welche IDs existieren
}

$draft = (string) $note['content'];
$error = null;

if (Http::isPost()) {
    Csrf::requireValid();

    $result = NoteValidator::validate($_POST);
    if ($result['error'] === null) {
        $notes->update($id, (int) $user['id'], $result['content']);
        Flash::success('Änderungen gespeichert.');
        Http::redirect('/notes.php');
    }

    $draft = $result['content'];
    $error = $result['error'];
}

View::render('note_edit', [
    'title' => 'Notiz bearbeiten',
    'note'  => $note,
    'draft' => $draft,
    'error' => $error,
], $error ? 422 : 200);
