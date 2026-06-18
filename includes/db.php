<?php

$databaseConfig = require __DIR__ . '/../config/database.php';

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $databaseConfig['host'],
    $databaseConfig['database'],
    $databaseConfig['charset']
);

try {
    $pdo = new PDO(
        $dsn,
        $databaseConfig['username'],
        $databaseConfig['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $exception) {
    exit('Database connection failed.');
}
