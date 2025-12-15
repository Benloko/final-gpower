<?php
require_once __DIR__ . '/config/config.php';

if (isset($_GET['lang'])) {
    $lang = sanitize($_GET['lang']);
    
    // Validate language
    if (in_array($lang, ['en', 'fr'])) {
        $_SESSION['language'] = $lang;
    }
}

// Redirect back to previous page or home
$redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL . '/';
redirect($redirect_url);
?>
