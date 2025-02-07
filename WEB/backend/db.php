<?php
$host = '127.0.0.1';
$db = 'task_manager';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';  // Define charset
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Default fetch mode
    PDO::ATTR_EMULATE_PREPARES => false, // Disable emulation for better security
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Log the error for debugging
    error_log("Database connection failed: " . $e->getMessage());
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>