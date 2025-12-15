<?php
$page_title = 'Product Details';
require_once __DIR__ . '/includes/header.php';

// Get product ID
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$from_admin = isset($_GET['from']) && $_GET['from'] === 'admin';

// Get product details
$hasQuantity = false;
try {
    $colStmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'products' AND COLUMN_NAME = 'quantity'");
    $colStmt->execute([DB_NAME]);
    $hasQuantity = $colStmt->fetch()['cnt'] > 0;
} catch (Exception $e) {
    $hasQuantity = false;
}

if ($hasQuantity) {
    $stmt = $pdo->prepare("SELECT p.*, COALESCE(p.quantity,0) AS quantity FROM products p WHERE p.id = ? AND p.status = 'active'");
} else {
    $stmt = $pdo->prepare("SELECT p.*, 0 AS quantity FROM products p WHERE p.id = ? AND p.status = 'active'");
}
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    redirect(BASE_URL . '/');
}

// Parse specifications text into labeled pairs when possible.
$spec_text = trim((string)($product['specifications'] ?? ''));
$parsed_specs = []; // associative label => value
$free_notes = '';
if ($spec_text !== '') {
    $lines = preg_split('/\r?\n/', $spec_text);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        // If the line contains a colon, treat it as Label: Value
        if (strpos($line, ':') !== false) {
            [$label, $val] = array_map('trim', explode(':', $line, 2));
            if ($label !== '') {
                // Normalize common label variants (case-insensitive)
                $normalized = preg_replace('/\s+/', ' ', trim($label));
                $parsed_specs[$normalized] = $val;
                continue;
            }
        }
        // Otherwise accumulate as freeform notes
        $free_notes .= ($free_notes === '' ? '' : "\n") . $line;
    }
}

// Get product images
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order");
$stmt->execute([$product_id]);
$product_images = $stmt->fetchAll();

?>

<div class="container my-5">
    <?php if ($from_admin): ?>
    <div class="mb-3">
        <a href="<?php echo BASE_URL; ?>/admin/product-edit.php?id=<?php echo $product_id; ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fas fa-arrow-left me-1"></i>Retour à l'édition
        </a>
    </div>
    <?php endif; ?>
    
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <!-- Product Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Product Images -->
                <div class="product-images-container bg-white p-3">
                    <?php if ($product['main_image']): ?>
                        <div class="main-image-wrapper rounded-3 overflow-hidden mb-3" style="background: #f8f9fa;">
                            <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                                 class="w-100" 
                                 id="mainProductImage"
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="height: 400px; object-fit: contain; transition: transform 0.3s ease;">
                        </div>
                    <?php else: ?>
                        <div class="main-image-wrapper bg-light rounded-3 d-flex align-items-center justify-content-center mb-3" style="height: 400px;">
                            <i class="fas fa-camera fa-5x text-muted opacity-25"></i>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Product Gallery Thumbnails -->
                    <?php if (!empty($product_images) || $product['main_image']): ?>
                    <div class="product-gallery-thumbnails">
                        <div class="d-flex gap-2 overflow-auto pb-2" style="scrollbar-width: thin;">
                            <?php if ($product['main_image']): ?>
                                <div class="thumbnail-wrapper">
                                    <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                                         alt="Main" 
                                         class="thumbnail active rounded-3 cursor-pointer"
                                         onclick="changeMainImage('<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>', this)">
                                </div>
                            <?php endif; ?>
                            
                            <?php foreach ($product_images as $image): ?>
                                <div class="thumbnail-wrapper">
                                    <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                         alt="Gallery image"
                                         class="thumbnail rounded-3 cursor-pointer"
                                         onclick="changeMainImage('<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($image['image_path']); ?>', this)">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Product Info -->
                <div class="card-body p-4">
                    <!-- Product Name -->
                    <h1 class="fw-bold mb-3" style="font-size: 1.5rem; color: #1a1a1a;">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h1>
                    
                    <!-- Price -->
                    <div class="mb-4">
                        <div class="fw-bold mb-1" style="font-size: 2rem; color: #000;">
                            <?php echo number_format($product['price'], 0, ',', ' '); ?> FCFA
                        </div>
                        <small class="text-muted">Prix unitaire</small>
                    </div>
                    
                    <!-- Units Available -->
                    <div class="d-flex align-items-center gap-2 mb-2 text-muted">
                        <i class="fas fa-boxes"></i>
                        <span><strong><?php echo number_format((int)$product['quantity']); ?></strong> unité(s) disponible(s)</span>
                    </div>
                    
                    <!-- Total Stock Value -->
                    <?php 
                    $total_stock_value = (int)$product['quantity'] * (float)$product['price'];
                    if ($total_stock_value > 0): 
                    ?>
                    <div class="d-flex align-items-center gap-2 mb-3 text-muted">
                        <i class="fas fa-wallet"></i>
                        <span><strong><?php echo number_format($total_stock_value, 0, ',', ' '); ?> FCFA</strong> valeur totale du stock</span>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Location -->
                    <div class="d-flex align-items-center gap-2 mb-4 text-muted">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($product['location'] ?? 'Non spécifié'); ?></span>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <a href="https://wa.me/<?php echo $settings['whatsapp_number']; ?>?text=<?php echo urlencode('Bonjour, je suis intéressé par ' . $product['name']); ?>" 
                               class="btn btn-dark w-100 rounded-4 d-flex align-items-center justify-content-center gap-2 shadow-sm"
                               target="_blank"
                               style="background: #25D366; border: none; padding: 0.6rem 1rem; font-size: 0.9rem;">
                                <i class="fab fa-whatsapp"></i>
                                <span class="fw-bold">WhatsApp</span>
                            </a>
                        </div>
                        
                        <div class="col-6">
                            <a href="mailto:<?php echo $settings['site_email'] ?? 'contact@gpower.ci'; ?>?subject=<?php echo urlencode('Demande: ' . $product['name']); ?>&body=<?php echo urlencode('Bonjour, je souhaite obtenir plus d\'informations sur ' . $product['name'] . ' au prix de ' . number_format($product['price'], 0, ',', ' ') . ' FCFA.'); ?>" 
                               class="btn btn-dark w-100 rounded-4 d-flex align-items-center justify-content-center gap-2 shadow-sm"
                               style="padding: 0.6rem 1rem; font-size: 0.9rem;">
                                <i class="fas fa-envelope"></i>
                                <span class="fw-bold">Email</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Specifications -->
                    <?php if (!empty($parsed_specs)): ?>
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-bold mb-3" style="font-size: 1.25rem; color: #1a1a1a;">
                            Specifications
                        </h5>
                        <div>
                            <?php foreach ($parsed_specs as $label => $val): ?>
                                <?php if (!empty($val) || $val === '0' || $val === 0): ?>
                                <div class="d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid #e9ecef;">
                                    <div class="text-muted" style="font-size: 0.95rem;">
                                        <?php echo htmlspecialchars($label); ?>
                                    </div>
                                    <div class="fw-semibold" style="font-size: 0.95rem; color: #1a1a1a;">
                                        <?php echo htmlspecialchars($val); ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Freeform Notes (if no structured specs) -->
                    <?php if (empty($parsed_specs) && !empty($free_notes)): ?>
                    <div class="mt-4 pt-4 border-top">
                        <h5 class="fw-bold mb-3" style="font-size: 1.25rem; color: #1a1a1a;">
                            Specifications
                        </h5>
                        <div class="text-muted" style="font-size:0.95rem; line-height:1.8; white-space: pre-line;">
                            <?php echo htmlspecialchars($free_notes); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Back to Home Button -->
                    <?php if (!$from_admin): ?>
                    <div class="mt-4 pt-4 border-top">
                        <a href="<?php echo BASE_URL; ?>/" 
                           class="btn btn-outline-dark w-100 rounded-4 d-flex align-items-center justify-content-center gap-2"
                           style="padding: 0.75rem 1rem; font-size: 0.95rem;">
                            <i class="fas fa-arrow-left"></i>
                            <span class="fw-semibold">Retour à l'accueil</span>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="height: 60px;"></div>

<script>
function changeMainImage(imageSrc, thumbnail) {
    // Update main image
    const mainImage = document.getElementById('mainProductImage');
    mainImage.src = imageSrc;
    
    // Remove active class from all thumbnails
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
    });
    
    // Add active class to clicked thumbnail
    thumbnail.classList.add('active');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
