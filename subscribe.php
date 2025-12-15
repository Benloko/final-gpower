<?php
require_once __DIR__ . '/config/config.php';

$pdo = getPDOConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe'])) {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?newsletter=empty');
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?newsletter=invalid');
        exit;
    }
    
    try {
        // Créer la table si elle n'existe pas
        $pdo->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('active', 'unsubscribed') DEFAULT 'active',
            ip_address VARCHAR(45) NULL,
            INDEX idx_status (status),
            INDEX idx_subscribed (subscribed_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT * FROM newsletter_subscribers WHERE email = ?");
        $stmt->execute([$email]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            if ($existing['status'] === 'unsubscribed') {
                // Réactiver l'abonnement
                $stmt = $pdo->prepare("UPDATE newsletter_subscribers SET status = 'active', subscribed_at = NOW() WHERE email = ?");
                $stmt->execute([$email]);
                
                // Set cookie
                setcookie('visitor_email', $email, time() + (365 * 24 * 60 * 60), '/');
                
                header('Location: ' . $_SERVER['HTTP_REFERER'] . '?newsletter=resubscribed');
            } else {
                // Set cookie even if already exists
                setcookie('visitor_email', $email, time() + (365 * 24 * 60 * 60), '/');
                
                header('Location: ' . $_SERVER['HTTP_REFERER'] . '?newsletter=exists');
            }
        } else {
            // Nouvel abonnement
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, ip_address) VALUES (?, ?)");
            $stmt->execute([$email, $ip]);
            
            // Set cookie to remember subscription
            setcookie('visitor_email', $email, time() + (365 * 24 * 60 * 60), '/'); // 1 year
            
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?newsletter=success');
        }
    } catch (Exception $e) {
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?newsletter=error');
    }
} else {
    header('Location: ' . BASE_URL);
}
exit;
