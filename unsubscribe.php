<?php
require_once __DIR__ . '/config/config.php';

$pdo = getPDOConnection();

// Check if user has subscription cookie
if (!isset($_COOKIE['visitor_email'])) {
    header('Location: ' . BASE_URL . '?newsletter=no_subscription');
    exit;
}

$email = $_COOKIE['visitor_email'];

try {
    // Check if email exists in database
    $stmt = $pdo->prepare("SELECT * FROM newsletter_subscribers WHERE email = ?");
    $stmt->execute([$email]);
    $subscriber = $stmt->fetch();
    
    if (!$subscriber) {
        // Clear cookie and redirect
        setcookie('visitor_email', '', time() - 3600, '/');
        header('Location: ' . BASE_URL . '?newsletter=no_subscription');
        exit;
    }
    
    // Update status to unsubscribed
    $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'unsubscribed' WHERE email = ?");
    $stmt->execute([$email]);
    
    // Clear cookie
    setcookie('visitor_email', '', time() - 3600, '/');
    
    // Redirect with success message
    header('Location: ' . BASE_URL . '?newsletter=unsubscribed');
    exit;
    
} catch (Exception $e) {
    header('Location: ' . BASE_URL . '?newsletter=error');
    exit;
}
