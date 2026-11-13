<?php

declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Database;

// Liveness/Readiness: antwortet nur, wenn die Datenbank erreichbar ist.
Database::connection()->query('SELECT 1');

header('Content-Type: text/plain; charset=utf-8');
echo 'ok';
