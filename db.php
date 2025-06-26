<?php
// backend/config/db.php

define('DB_HOST', 'localhost');      // or your DB host
define('DB_NAME', 'fusst_booking');  // database name
define('DB_USER', 'root');           // your MySQL username
define('DB_PASS', '');               // your MySQL password


try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}
