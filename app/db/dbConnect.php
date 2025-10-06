<?php

    // $dsn = 'mysql:dbname=userdb;host=localhost';
    // $dbUsername ='root';
    // $password = '';
    // $con = new PDO($dsn, $dbUsername, $password);

    $host = 'localhost';
    $dbUsername = 'root';
    $password = '';
    $dbname = 'userdb';

    try {
        // Verbindung ohne Datenbank herstellen
        $pdo = new PDO("mysql:host=$host", $dbUsername, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Datenbank erstellen, falls sie noch nicht existiert
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Verbinfung zur Datenbank
        $con = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbUsername, $password);
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Tabelle 'users' erstellen
        $createUsersTableSQL = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ";
        $con->exec($createUsersTableSQL);

        // Tabelle 'notes' erstellen
        $createNotesTableSQL = "
            CREATE TABLE IF NOT EXISTS notes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
            );
        ";
        $con->exec($createNotesTableSQL);

    } catch (PDOException $e) {
        die('Verbindungsfehler: ' . $e->getMessage());
    }