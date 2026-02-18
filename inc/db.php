<?php
$host = 'localhost';
$dbname = 'perfume_site';
$username = 'root';
$password = ''; // set your DB password if any

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start session only if none is active
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
