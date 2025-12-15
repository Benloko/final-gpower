<?php
// Site configuration
session_start();

// Base URL configuration
// Auto-detect host and protocol
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . "://" . $host);

define('ADMIN_URL', BASE_URL . '/admin');

// Upload paths
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('PRODUCT_IMAGE_PATH', UPLOAD_PATH . 'products/');

// Allowed image extensions
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('MAX_FILE_SIZE', 5242880); // 5MB

// Pagination
define('ITEMS_PER_PAGE', 10);

// Default language
define('DEFAULT_LANG', 'en');

// Timezone
date_default_timezone_set('UTC');

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database
require_once __DIR__ . '/database.php';

// Helper function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Helper function to redirect
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Helper function to require login
function requireLogin() {
    if (!isLoggedIn()) {
        redirect(ADMIN_URL . '/login.php');
    }
}
?>
