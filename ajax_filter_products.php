<?php
require_once 'config/config.php';
require_once __DIR__ . '/includes/helpers.php';

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
    $sql = "SELECT * FROM products WHERE status = 'active'";
    $params = [];
    
    if (!empty($query)) {
        $sql .= " AND (name LIKE ? OR specifications LIKE ?)";
        $params[] = '%' . $query . '%';
        $params[] = '%' . $query . '%';
    }
    
    $sql .= " ORDER BY created_at DESC LIMIT 30";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($products)) {
        echo '<div class="col-12 text-center py-5 bg-white rounded-3 shadow-sm border border-light my-2">
                <div class="mb-2 text-muted opacity-50"><i class="fas fa-search-minus fa-2x"></i></div>
                <h6 class="fw-bold text-dark mb-1">No Matching Machinery Found</h6>
                <p class="text-muted small mb-0">Try searching for another model or brand.</p>
              </div>';
        exit;
    }
    
    foreach ($products as $product): ?>
        <div class="col">
            <div class="card product-card-modern h-100 border-0 bg-white shadow-sm rounded-3 position-relative overflow-hidden" data-href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>">
                
                <div class="position-absolute top-0 start-0 m-2.5 z-2">
                    <span class="badge bg-dark bg-opacity-75 text-warning backdrop-blur border border-white border-opacity-20 rounded-circle p-1.5 shadow-sm" title="Verified Machinery">
                        <i class="fas fa-shield-alt"></i>
                    </span>
                </div>

                <div class="product-image-container position-relative overflow-hidden" style="border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                    <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="d-block text-decoration-none">
                        <?php $img_url = product_image_url($product['main_image']); ?>
                        <?php if ($img_url): ?>
                            <img src="<?php echo htmlspecialchars($img_url); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="w-100" style="height: 175px; object-fit: cover; transition: transform 0.4s ease;">
                        <?php else: ?>
                            <?php echo product_image_placeholder('175px', htmlspecialchars($product['name'])); ?>
                        <?php endif; ?>
                    </a>
                </div>

                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="product-title fw-bold text-dark text-truncate mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="text-dark text-decoration-none hover-primary">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h6>

                        <div class="d-flex align-items-center gap-2 mb-2 text-muted small product-status-line">
                            <span class="text-danger fw-semibold" style="font-size: 0.78rem;">
                                <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($product['location'] ?? 'Global Depot'); ?>
                            </span>
                            <span class="text-muted">•</span>
                            <span class="text-success fw-semibold" style="font-size: 0.78rem;">
                                <i class="fas fa-check-circle me-1"></i>Ready to Export
                            </span>
                        </div>
                    </div>

                    <div class="pt-2.5 border-top border-light d-flex align-items-center justify-content-between mt-2">
                        <div>
                            <div class="text-uppercase text-muted" style="font-size: 0.62rem; font-weight: 700; letter-spacing: 0.5px;">ESTIMATED PRICE</div>
                            <div class="product-price fw-extrabold text-primary" style="font-size: 1.05rem;">
                                <?php echo format_price($product['price']); ?>
                            </div>
                        </div>

                        <button type="button" 
                                class="btn btn-success rounded-circle d-flex align-items-center justify-content-center shadow product-whatsapp-pulse" 
                                style="width: 36px; height: 36px; background: #25D366; border: 0;" 
                                onclick="event.stopPropagation(); contactWhatsApp('<?php echo addslashes(htmlspecialchars($product['name'])); ?>','<?php echo $whatsapp_number; ?>')" 
                                title="Inquire via WhatsApp">
                            <i class="fab fa-whatsapp fs-6 text-white"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;

} catch (Exception $e) {
    echo '<div class="col-12 text-center text-danger">An error occurred.</div>';
}
