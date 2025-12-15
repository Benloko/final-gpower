<?php
// Language handler
require_once __DIR__ . '/../config/config.php';

// Get current language from session or default
$current_lang = isset($_SESSION['language']) ? $_SESSION['language'] : DEFAULT_LANG;

// Load language file
$lang_file = __DIR__ . '/../lang/' . $current_lang . '.php';
if (file_exists($lang_file)) {
    require_once $lang_file;
} else {
    require_once __DIR__ . '/../lang/en.php';
}

// Function to get translation
function t($key) {
    global $lang;
    return isset($lang[$key]) ? $lang[$key] : $key;
}

// Function to change language
function changeLanguage($new_lang) {
    $_SESSION['language'] = $new_lang;
}
?>
