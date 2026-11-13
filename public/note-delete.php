<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Auth;
use App\Csrf;
use App\Database;
use App\Flash;
use App\Http\Http;
use App\Repository\NoteRepository;

$user = Auth::requireUser();

if (!Http::isPost()) {
    Http::methodNotAllowed();
}
Csrf::requireValid();

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$deleted = $id && (new NoteRepository(Database::connection()))->delete($id, (int) $user['id']);

if ($deleted) {
    Flash::success('Notiz gelöscht.');
} else {
    Flash::error('Diese Notiz wurde nicht gefunden.');
}
Http::redirect('/notes.php');
