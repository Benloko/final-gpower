<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function usage(int $exitCode = 0): void
{
    $script = basename(__FILE__);
    fwrite(STDERR, "Usage:\n");
    fwrite(STDERR, "  php tools/{$script} --file=products.sql [--clear-dependents] [--disable-fk-checks] [--dry-run]\n\n");
    fwrite(STDERR, "Options:\n");
    fwrite(STDERR, "  --file=PATH            Path to phpMyAdmin SQL dump (default: products.sql)\n");
    fwrite(STDERR, "  --clear-dependents     Also clear tables that have FK references to products (e.g. product_images)\n");
    fwrite(STDERR, "  --disable-fk-checks    Temporarily disable FK checks during deletes/inserts (use with care)\n");
    fwrite(STDERR, "  --dry-run              Do not write, just show what would be executed\n");
    exit($exitCode);
}

function parseArgs(array $argv): array
{
    $args = [
        'file' => 'products.sql',
        'clear_dependents' => false,
        'disable_fk_checks' => false,
        'dry_run' => false,
    ];

    foreach ($argv as $i => $arg) {
        if ($i === 0) {
            continue;
        }

        if ($arg === '--help' || $arg === '-h') {
            usage(0);
        }

        if (str_starts_with($arg, '--file=')) {
            $args['file'] = substr($arg, strlen('--file='));
            continue;
        }

        if ($arg === '--clear-dependents') {
            $args['clear_dependents'] = true;
            continue;
        }

        if ($arg === '--disable-fk-checks') {
            $args['disable_fk_checks'] = true;
            continue;
        }

        if ($arg === '--dry-run') {
            $args['dry_run'] = true;
            continue;
        }

        fwrite(STDERR, "Unknown arg: {$arg}\n\n");
        usage(2);
    }

    return $args;
}

function extractInsertStatements(string $sql, string $tableName): array
{
    // Match INSERT blocks for a specific table in a phpMyAdmin dump.
    // Non-greedy up to the terminating semicolon.
    $pattern = sprintf('~\bINSERT\s+INTO\s+`%s`\s.*?;\s*~is', preg_quote($tableName, '~'));
    if (!preg_match_all($pattern, $sql, $matches)) {
        return [];
    }

    return array_values(array_filter(array_map('trim', $matches[0])));
}

function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :db AND table_name = :t');
    $stmt->execute([':db' => DB_NAME, ':t' => $table]);
    return ((int)$stmt->fetchColumn()) > 0;
}

function getReferencingTables(PDO $pdo, string $referencedTable): array
{
    $sql = <<<SQL
SELECT DISTINCT table_name
FROM information_schema.key_column_usage
WHERE
  referenced_table_schema = :db
  AND referenced_table_name = :ref
ORDER BY table_name
SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':db' => DB_NAME, ':ref' => $referencedTable]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tables = [];
    foreach ($rows as $row) {
        $name = $row['table_name'] ?? $row['TABLE_NAME'] ?? null;
        if (is_string($name) && $name !== '') {
            $tables[] = $name;
        }
    }
    return $tables;
}

$args = parseArgs($argv);
$filePath = $args['file'];
if (!str_starts_with($filePath, '/')) {
    $filePath = __DIR__ . '/../' . ltrim($filePath, '/');
}

if (!is_file($filePath)) {
    fwrite(STDERR, "SQL file not found: {$filePath}\n");
    exit(1);
}

$pdo = getPDOConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = file_get_contents($filePath);
if ($sql === false) {
    fwrite(STDERR, "Failed to read: {$filePath}\n");
    exit(1);
}

$insertStatements = extractInsertStatements($sql, 'products');
if (count($insertStatements) === 0) {
    fwrite(STDERR, "No INSERT INTO `products` statements found in dump.\n");
    exit(1);
}

$referencingTables = getReferencingTables($pdo, 'products');

fwrite(STDOUT, "DB: " . DB_NAME . "\n");
fwrite(STDOUT, "Dump: {$filePath}\n");
fwrite(STDOUT, "Found INSERT statements for products: " . count($insertStatements) . "\n");
if (count($referencingTables) > 0) {
    fwrite(STDOUT, "Tables referencing products (FK): " . implode(', ', $referencingTables) . "\n");
} else {
    fwrite(STDOUT, "No FK-referencing tables detected for products.\n");
}

if (!tableExists($pdo, 'products')) {
    fwrite(STDERR, "Table `products` does not exist in " . DB_NAME . ". Create schema first (e.g. import database.sql), then re-run.\n");
    exit(1);
}

if ($args['dry_run']) {
    fwrite(STDOUT, "\nDry run: would clear `products`" . ($args['clear_dependents'] ? " and dependents" : "") . " then run INSERT(s).\n");
    exit(0);
}

try {
    $pdo->beginTransaction();

    if ($args['disable_fk_checks']) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    }

    if ($args['clear_dependents'] && count($referencingTables) > 0) {
        foreach ($referencingTables as $t) {
            // Safety: only clear if table exists.
            if (tableExists($pdo, $t)) {
                $pdo->exec('DELETE FROM `' . str_replace('`', '``', $t) . '`');
            }
        }
    }

    $pdo->exec('DELETE FROM `products`');

    foreach ($insertStatements as $stmt) {
        $pdo->exec($stmt);
    }

    if ($args['disable_fk_checks']) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    $pdo->commit();

    $count = (int)$pdo->query('SELECT COUNT(*) FROM `products`')->fetchColumn();
    fwrite(STDOUT, "\nOK: products rows now = {$count}\n");
    exit(0);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, "\nImport failed: " . $e->getMessage() . "\n");

    if (!$args['clear_dependents'] && count($referencingTables) > 0) {
        fwrite(STDERR, "Hint: re-run with --clear-dependents (and optionally --disable-fk-checks) if FK constraints block deletes.\n");
    }

    exit(1);
}
