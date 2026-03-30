<?php
// Simplified language handler: force English-only per project configuration.
require_once __DIR__ . '/../config/config.php';

// Force English
$current_lang = 'en';

// Load English language file
$lang_file = __DIR__ . '/../lang/en.php';
if (file_exists($lang_file)) {
    require_once $lang_file;
} else {
    // Minimal fallback translations
    $lang = [];
}

function t($key) {
    global $lang;
    return isset($lang[$key]) ? $lang[$key] : $key;
}

?>
