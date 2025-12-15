<?php
require_once 'config/config.php';

header('Content-Type: application/json');

$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = getPDOConnection();
    
    // Search products
    $stmt = $pdo->prepare("
        SELECT id, name, price, main_image 
        FROM products 
        WHERE status = 'active' 
        AND name LIKE ? 
        ORDER BY name ASC 
        LIMIT 5
    ");
    
    $stmt->execute(['%' . $query . '%']);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format image paths
    foreach ($products as &$product) {
        if ($product['main_image']) {
            $product['image_url'] = BASE_URL . '/uploads/products/' . $product['main_image'];
        } else {
            $product['image_url'] = null;
        }
        $product['url'] = BASE_URL . '/product-details.php?id=' . $product['id'];
        $product['formatted_price'] = number_format($product['price'], 0, ',', ' ') . ' FCFA';
    }
    
    echo json_encode($products);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
