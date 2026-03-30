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

?>