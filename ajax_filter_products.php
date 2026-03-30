<?php
require_once 'config/config.php';
require_once __DIR__ . '/includes/helpers.php';

// Get settings for WhatsApp number
$pdo = getPDOConnection();
$whatsapp_number = '2250707070707';
try {
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_number'");
    $whatsapp_number = $stmt->fetchColumn() ?: $whatsapp_number;
} catch (PDOException $e) {
    // Table doesn't exist yet
}

$query = $_GET['search'] ?? '';

try {
    // Search products
    $sql = "SELECT * FROM products WHERE status = 'active'";
    $params = [];
    
    if (!empty($query)) {
        $sql .= " AND name LIKE ?";
        $params[] = '%' . $query . '%';
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT 20"; // Limit to keep it fast
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($products)) {
        echo '<div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
                    <p>No products found for "' . htmlspecialchars($query) . '"</p>
                </div>
              </div>';
        exit;
    }
    
    foreach ($products as $product): ?>
        <div class="col">
            <div class="card product-card h-100 border-0 shadow-sm">
                <div class="position-relative bg-white p-3 text-center" style="height: 160px;">
                    
                    <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="d-block h-100 text-decoration-none">
                        <?php if ($product['main_image']): ?>
                            <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 class="img-fluid h-100" style="object-fit: contain;">
                        <?php else: ?>
                            <div class="no-image-placeholder">
                                <i class="fas fa-camera fa-2x mb-2 opacity-50"></i>
                                <span class="small fw-medium" style="font-size: 0.75rem;">No Image</span>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>
                
                <div class="card-body p-2">
                    <h6 class="card-title fw-bold mb-2 text-truncate" style="font-size: 0.9rem;">
                        <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="text-dark text-decoration-none">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </a>
                    </h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary small">
                            <?php echo format_price($product['price']); ?>
                        </span>
                        <button class="btn btn-sm btn-whatsapp-card rounded-circle p-0 d-flex align-items-center justify-content-center" 
                                onclick="contactWhatsApp('<?php echo htmlspecialchars($product['name']); ?>', '<?php echo $whatsapp_number; ?>')">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;

} catch (Exception $e) {
    echo '<div class="col-12 text-center text-danger">An error occurred.</div>';
}
