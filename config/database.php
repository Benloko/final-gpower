<?php

// Improved detection of local environment
$server = $_SERVER['SERVER_NAME'] ?? '';
if (
    $server === 'localhost' ||
    strpos($server, '127.') === 0 ||
    strpos($server, '0.') === 0 ||
    PHP_SAPI === 'cli-server' ||
    PHP_SAPI === 'cli'
) {
    $db_config = require __DIR__ . '/config.local.php';
} else {
    $db_config = require __DIR__ . '/config.prod.php';
}

define('DB_HOST', $db_config['DB_HOST']);
define('DB_USER', $db_config['DB_USER']);
define('DB_PASS', $db_config['DB_PASS']);
define('DB_NAME', $db_config['DB_NAME']);

// Create database connection
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

// Get PDO connection (for prepared statements)
function getPDOConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        die("Database connection error: " . $e->getMessage());
    }
}
?>