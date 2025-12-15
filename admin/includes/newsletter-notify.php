<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/email.php';

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
    $subject = "🎉 Nouveau produit disponible : " . $product['name'];
    
    $body = "Bonjour,\n\n";
    $body .= "Une nouvelle exclusivité vient d'arriver chez Gpower !\n\n";
    $body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $body .= "📦 " . strtoupper($product['name']) . "\n\n";
    
    if (!empty($product['description'])) {
        $body .= strip_tags($product['description']) . "\n\n";
    }
    
    $body .= "💰 Prix : " . number_format($product['price'], 0, '', ' ') . " FCFA\n";
    
    if (!empty($product['quantity']) && $product['quantity'] > 0) {
        $body .= "📊 Stock disponible : " . $product['quantity'] . " unités\n";
    }
    
    $body .= "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $body .= "👉 Voir le produit : " . BASE_URL . "/product-details.php?id=" . $product['id'] . "\n\n";
    $body .= "Ne manquez pas cette opportunité !\n\n";
    $body .= "───────────────────────────────────\n";
    $body .= "L'équipe Gpower\n";
    $body .= "Votre partenaire de confiance\n\n";
    $body .= "📧 Pour vous désabonner, connectez-vous à votre compte\n";
    
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
