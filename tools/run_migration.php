<?php
/**
 * Migration Runner
 * Exécute les migrations de schéma de base de données
 */

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getPDOConnection();
    
    $migrationFile = __DIR__ . '/005_add_pdf_and_stock.sql';
    
    if (!file_exists($migrationFile)) {
        die("❌ Migration file not found: $migrationFile\n");
    }
    
    $sql = file_get_contents($migrationFile);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            echo "Executing: " . substr($statement, 0, 60) . "...\n";
            $pdo->exec($statement);
        }
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "✓ Column 'pdf_path' added to products table\n";
    echo "✓ Column 'stock_number' added to products table\n";
    echo "✓ Index created for stock_number\n";
    
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
