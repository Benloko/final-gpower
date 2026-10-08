<?php
// Helper functions for formatting prices and other utilities
require_once __DIR__ . '/../config/config.php';

/**
 * Format a price stored in FCFA to the display currency (USD by default).
 * Assumes $amount is a numeric value in FCFA.
 */
function format_price($amount) {
    $currency = defined('DISPLAY_CURRENCY') ? DISPLAY_CURRENCY : 'USD';
    $stored_in = defined('PRICES_STORED_IN') ? PRICES_STORED_IN : 'FCFA';

    // If prices are stored in the same currency as the display currency, show them directly
    if ($stored_in === $currency) {
        if (!is_numeric($amount)) return number_format(0, 2, '.', ',') . ' ' . $currency;
        if ($currency === 'USD') return '$' . number_format($amount, 2, '.', ',');
        return number_format($amount, 2, '.', ',') . ' ' . $currency;
    }

    // Otherwise assume stored in FCFA and convert to display currency (USD)
    $rate = defined('EXCHANGE_RATE_FCFA_TO_USD') ? EXCHANGE_RATE_FCFA_TO_USD : 600;
    if (!$rate || !is_numeric($amount)) {
        return number_format($amount, 2, '.', ',') . ' ' . $currency;
    }

    $converted = $amount / $rate;
    if ($currency === 'USD') {
        return '$' . number_format($converted, 2, '.', ',');
    }
    return number_format($converted, 2, '.', ',') . ' ' . $currency;
}

/**
 * Returns the full URL to a product image ONLY if the file actually exists on disk.
 * - On the production server (Hostinger): all images exist → always returns the real URL.
 * - Locally: images uploaded on the server after the last sync will return null,
 *   so the caller can show a clean placeholder instead of a broken broken icon.
 *
 * @param string|null $filename  The filename stored in the DB (e.g. "prod_xxx.jpg")
 * @param string      $subdir    Sub-folder inside uploads/ (default: 'products')
 * @return string|null           Full URL if the file exists on disk, null otherwise.
 */
function product_image_url($filename, $subdir = 'products') {
    if (empty($filename)) {
        return null;
    }
    // Build the absolute disk path to the uploads directory.
    $uploads_root = defined('UPLOADS_PATH')
        ? rtrim(UPLOADS_PATH, '/')
        : rtrim(__DIR__ . '/../uploads', '/');
    $full_path = $uploads_root . '/' . $subdir . '/' . $filename;

    if (!file_exists($full_path)) {
        return null; // File is missing locally; caller should show a placeholder.
    }
    return rtrim(BASE_URL, '/') . '/uploads/' . $subdir . '/' . rawurlencode($filename);
}

/**
 * Renders a clean, consistent placeholder block when a product image is unavailable.
 * Used for products with no image AND for images missing from the local filesystem.
 *
 * @param string $height  CSS height value (default '160px')
 * @param string $label   Optional title or category label
 * @return string         HTML string (safe to echo directly)
 */
function product_image_placeholder($height = '160px', $label = 'GPOWER INDUSTRIAL') {
    return '<div class="product-placeholder-box d-flex flex-column align-items-center justify-content-center w-100 position-relative overflow-hidden" style="height:' . htmlspecialchars($height) . '; background: linear-gradient(135deg, #0b132b 0%, #1c2541 60%, #3a506b 100%); border-radius: 12px;">
        <div class="position-absolute" style="top: -20px; right: -20px; width: 80px; height: 80px; background: rgba(0,245,212,0.08); border-radius: 50%;"></div>
        <div class="position-absolute" style="bottom: -20px; left: -20px; width: 90px; height: 90px; background: rgba(37,99,235,0.12); border-radius: 50%;"></div>
        <div class="placeholder-icon-wrap d-flex align-items-center justify-content-center mb-2 shadow-sm" style="width: 44px; height: 44px; border-radius: 50%; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15);">
            <i class="fas fa-bolt text-warning" style="font-size: 1.25rem;"></i>
        </div>
        <span class="text-white-50 fw-bold tracking-wide text-uppercase" style="font-size: 0.68rem; letter-spacing: 1px;">' . htmlspecialchars($label) . '</span>
    </div>';
}
?>