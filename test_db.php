<?php
require_once __DIR__ . '/config/database.php';

echo "Testing database connection...\n\n";

try {
    $pdo = getPDOConnection();
    echo "✓ PDO Connection successful!\n";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins");
    $result = $stmt->fetch();
    echo "✓ Query successful! Found {$result['count']} admin(s)\n";
    
    echo "\n✓ Database is working correctly!\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>
