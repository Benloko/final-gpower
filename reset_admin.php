<?php
require_once __DIR__ . '/config/config.php';

echo "Resetting admin user...\n";

try {
    $pdo = getPDOConnection();
    
    // Check if admins table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Delete existing admin user to be sure
    $stmt = $pdo->prepare("DELETE FROM admins WHERE username = ?");
    $stmt->execute(['admin']);
    
    // Create new admin user
    $username = 'admin';
    $password = 'admin123';
    $email = 'admin@gpower.com';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$username, $email, $hashed_password]);
    
    echo "Admin user reset successfully!\n";
    echo "Username: " . $username . "\n";
    echo "Password: " . $password . "\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
