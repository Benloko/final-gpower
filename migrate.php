<?php
require_once __DIR__ . '/config/config.php';

try {
    $pdo = getPDOConnection();

    // Check if quantity column already exists
    $colStmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'products' AND COLUMN_NAME = 'quantity'");
    $colStmt->execute([DB_NAME]);
    $has = (int)$colStmt->fetch()['cnt'];

    if ($has > 0) {
        echo "Migration skipped: column `quantity` already exists.\n";
        exit(0);
    }

    $sqlFile = __DIR__ . '/migrations/001_add_quantity.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception('Migration file not found: ' . $sqlFile);
    }

    $sql = file_get_contents($sqlFile);
    if (!$sql) {
        throw new Exception('Failed to read migration file.');
    }

    $pdo->beginTransaction();
    $pdo->exec($sql);
    $pdo->commit();

    echo "Migration applied: `quantity` column added to `products`.\n";
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
