<?php

$host = getenv("DB_HOST") ?: "db";
$dbname = getenv("DB_NAME") ?: "userdb";
$dbUsername = getenv("DB_USER") ?: "myuser";
$password = getenv("DB_PASS") ?: "mypassword";

try {
    $con = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbUsername,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die("Verbindungsfehler: " . $e->getMessage());
}