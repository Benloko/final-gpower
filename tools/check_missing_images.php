<?php
require_once __DIR__ . '/../config/config.php';

$pdo = getPDOConnection();

$uploadDir = realpath(__DIR__ . '/../uploads/products');
if ($uploadDir === false) {
    echo "uploads/products directory not found.\n";
    exit(1);
}

function file_exists_in_uploads(string $uploadDir, ?string $filename): bool {
    if ($filename === null) return true;
    $filename = trim($filename);
    if ($filename === '') return true;
    if (str_contains($filename, '/') || str_contains($filename, '\\')) return false;
    return file_exists($uploadDir . DIRECTORY_SEPARATOR . $filename);
}

// Main images
$main = $pdo->query("SELECT id, name, main_image FROM products WHERE main_image IS NOT NULL AND main_image <> ''")->fetchAll();
$missingMain = [];
foreach ($main as $row) {
    if (!file_exists_in_uploads($uploadDir, $row['main_image'])) {
        $missingMain[] = $row;
    }
}

// Gallery images
$missingGallery = [];
try {
    $gallery = $pdo->query("SELECT id, product_id, image_path FROM product_images WHERE image_path IS NOT NULL AND image_path <> ''")->fetchAll();
    foreach ($gallery as $row) {
        if (!file_exists_in_uploads($uploadDir, $row['image_path'])) {
            $missingGallery[] = $row;
        }
    }
} catch (Exception $e) {
    // product_images table might not exist in some installs
}

echo "Uploads dir: {$uploadDir}\n\n";

echo "Missing main images: " . count($missingMain) . "\n";
foreach ($missingMain as $row) {
    echo "- product_id={$row['id']} main_image={$row['main_image']} name=" . $row['name'] . "\n";
}

echo "\nMissing gallery images: " . count($missingGallery) . "\n";
foreach ($missingGallery as $row) {
    echo "- product_images.id={$row['id']} product_id={$row['product_id']} image_path={$row['image_path']}\n";
}

if (count($missingMain) === 0 && count($missingGallery) === 0) {
    echo "\nOK: no missing images detected.\n";
}
