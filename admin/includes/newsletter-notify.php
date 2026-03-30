<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/email.php';
require_once __DIR__ . '/../includes/helpers.php';

/**
 * Send new product notification to all active subscribers
 */
function notifyNewProduct($product) {
    $pdo = getPDOConnection();
    
    // Get all active subscribers
    $stmt = $pdo->query("SELECT email FROM newsletter_subscribers WHERE status = 'active'");
    $subscribers = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($subscribers)) {
        return false;
    }
    
    // Prepare email content
    $subject = "🎉 New product available: " . $product['name'];

    $body = "Hello,\n\n";
    $body .= "A new exclusive product has arrived at Gpower!\n\n";
    $body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $body .= "📦 " . strtoupper($product['name']) . "\n\n";
    
    if (!empty($product['description'])) {
        $body .= strip_tags($product['description']) . "\n\n";
    }
    
    $body .= "💰 Price: " . format_price($product['price']) . "\n";
    
    if (!empty($product['quantity']) && $product['quantity'] > 0) {
        $body .= "📊 Available stock: " . $product['quantity'] . " units\n";
    }
    
    $body .= "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $body .= "👉 View product: " . BASE_URL . "/product-details.php?id=" . $product['id'] . "\n\n";
    $body .= "Don't miss this opportunity!\n\n";
    $body .= "───────────────────────────────────\n";
    $body .= "The Gpower Team\n";
    $body .= "Your trusted partner\n\n";
    $body .= "📧 To unsubscribe, log into your account\n";
    
    // Send to all subscribers
    $success_count = 0;
    $failed_count = 0;
    
    foreach ($subscribers as $email) {
        try {
            if (sendEmail($email, '', $subject, $body, false)) {
                $success_count++;
            } else {
                $failed_count++;
            }
            
            // Small delay to avoid spam filters
            usleep(100000); // 0.1 second
        } catch (Exception $e) {
            $failed_count++;
            error_log("Failed to send to $email: " . $e->getMessage());
        }
    }
    
    return [
        'total' => count($subscribers),
        'success' => $success_count,
        'failed' => $failed_count
    ];
}
