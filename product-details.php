<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
$pdo = getPDOConnection();

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
    header("Location: " . BASE_URL . "/products.php");
    exit;
}

$page_title = $product['name'] . ' - Details';
require_once __DIR__ . '/includes/header.php';

// No server-side product translations used — product content is authored in English only.

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

// Get suggested products (random active products excluding current)
$related_stmt = $pdo->prepare("SELECT p.* FROM products p WHERE p.status = 'active' AND p.id != ? ORDER BY RAND() LIMIT 4");
$related_stmt->execute([$product_id]);
$related_products = $related_stmt->fetchAll();
?>

<div class="container my-4">
    <!-- Top-Left Back Button -->
    <div class="mb-3">
        <?php if ($from_admin): ?>
            <a href="<?php echo BASE_URL; ?>/admin/product-edit.php?id=<?php echo $product_id; ?>" class="btn btn-sm btn-outline-secondary rounded-2 px-3 py-1.5 text-decoration-none d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fas fa-arrow-left small"></i>
                <span>Back to edit</span>
            </a>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-sm btn-outline-secondary rounded-2 px-3 py-1.5 text-decoration-none d-inline-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fas fa-chevron-left small"></i>
                <span>Back to Catalog</span>
            </a>
        <?php endif; ?>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <!-- Product Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Product Images -->
                <div class="product-images-container bg-white p-3">
                    <?php $main_img_url = product_image_url($product['main_image']); ?>
                    <?php if ($main_img_url): ?>
                        <div class="main-image-wrapper rounded-3 overflow-hidden mb-3" style="background: #f8f9fa;">
                            <img src="<?php echo htmlspecialchars($main_img_url); ?>" 
                                 class="w-100" 
                                 id="mainProductImage"
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="height: 400px; object-fit: contain; transition: transform 0.3s ease;">
                        </div>
                    <?php else: ?>
                        <div class="main-image-wrapper bg-light rounded-3 d-flex align-items-center justify-content-center mb-3" style="height: 400px;">
                            <?php echo product_image_placeholder('400px'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Product Gallery Thumbnails -->
                    <?php if (!empty($product_images) || $product['main_image']): ?>
                    <div class="product-gallery-thumbnails">
                        <div class="d-flex gap-2 overflow-auto pb-2" style="scrollbar-width: thin;">
                            <?php if ($main_img_url): ?>
                                <div class="thumbnail-wrapper">
                                    <img src="<?php echo htmlspecialchars($main_img_url); ?>" 
                                         alt="Main" 
                                         class="thumbnail active rounded-3 cursor-pointer"
                                         onclick="changeMainImage('<?php echo htmlspecialchars($main_img_url); ?>', this)">
                                </div>
                            <?php endif; ?>
                            
                            <?php foreach ($product_images as $image): ?>
                                <?php $gallery_url = product_image_url($image['image_path']); ?>
                                <?php if ($gallery_url): ?>
                                <div class="thumbnail-wrapper">
                                    <img src="<?php echo htmlspecialchars($gallery_url); ?>" 
                                         alt="Gallery image"
                                         class="thumbnail rounded-3 cursor-pointer"
                                         onclick="changeMainImage('<?php echo htmlspecialchars($gallery_url); ?>', this)">
                                </div>
                                <?php endif; ?>
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
                            <?php echo format_price($product['price']); ?>
                        </div>
                        <small class="text-muted"><?php echo t('unit_price'); ?></small>
                    </div>
                    
                    <!-- Units Available -->
                    <div class="d-flex align-items-center gap-2 mb-2 text-muted">
                        <i class="fas fa-boxes"></i>
                        <span><strong><?php echo number_format((int)$product['quantity']); ?></strong> <?php echo t('units_available'); ?></span>
                    </div>
                    
                    <!-- Total Stock Value -->
                    <?php 
                    $total_stock_value = (int)$product['quantity'] * (float)$product['price'];
                    if ($total_stock_value > 0): 
                    ?>
                    <div class="d-flex align-items-center gap-2 mb-3 text-muted">
                        <i class="fas fa-wallet"></i>
                        <span><strong><?php echo format_price($total_stock_value); ?></strong> <?php echo t('total_stock_value'); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Location -->
                    <div class="d-flex align-items-center gap-2 mb-4 text-muted">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($product['location'] ?? 'Not specified'); ?></span>
                    </div>

                    <!-- Product Identification Number -->
                    <?php if ($product['identification_number'] ?? false): ?>
                    <div class="d-flex align-items-center gap-2 mb-4 text-muted">
                        <i class="fas fa-barcode"></i>
                        <span><strong><?php echo htmlspecialchars($product['identification_number']); ?></strong></span>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Action Buttons -->
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <a href="https://wa.me/<?php echo $settings['whatsapp_number']; ?>?text=<?php echo urlencode('Hello, I am interested in ' . $product['name']); ?>" 
                               class="btn btn-dark w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 shadow-sm"
                               target="_blank"
                               style="background: #25D366; border: none; padding: 0.6rem 1rem; font-size: 0.9rem; height: 42px;">
                                <i class="fab fa-whatsapp"></i>
                                <span class="fw-bold">WhatsApp</span>
                            </a>
                        </div>
                        
                        <div class="col-6">
                            <a href="mailto:<?php echo $settings['site_email'] ?? 'contact@gpower.ci'; ?>?subject=<?php echo urlencode('Inquiry: ' . $product['name']); ?>&body=<?php echo urlencode('Hello, I would like more information about ' . $product['name'] . ' priced at ' . format_price($product['price']) . '.'); ?>" 
                               class="btn btn-dark w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 shadow-sm"
                               style="padding: 0.6rem 1rem; font-size: 0.9rem; height: 42px;">
                                <i class="fas fa-envelope"></i>
                                <span class="fw-bold">Email</span>
                            </a>
                        </div>
                    </div>

                    <!-- Stock Status & PDF Section (Slim & Same Height as Action Buttons) -->
                    <?php 
                    $quantity = (int)$product['quantity'];
                    if ($quantity > 10) {
                        $badge_style = 'background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46;';
                        $badge_text = 'In Stock (' . number_format($quantity) . ' units)';
                        $badge_icon = 'check-circle';
                    } elseif ($quantity > 0) {
                        $badge_style = 'background: #fffbeb; border: 1px solid #fde68a; color: #92400e;';
                        $badge_text = 'Limited Stock (' . number_format($quantity) . ' ' . ($quantity > 1 ? 'units' : 'unit') . ')';
                        $badge_icon = 'exclamation-circle';
                    } else {
                        $badge_style = 'background: #fef2f2; border: 1px solid #fecaca; color: #991b1b;';
                        $badge_text = 'Out of Stock';
                        $badge_icon = 'times-circle';
                    }
                    $pdf_name = $product['pdf_file'] ?? ($product['pdf_path'] ?? '');
                    ?>
                    <div class="row g-2 mb-4">
                        <div class="<?php echo $pdf_name ? 'col-6' : 'col-12'; ?>">
                            <div class="w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 fw-semibold"
                                 style="<?php echo $badge_style; ?> padding: 0.6rem 1rem; font-size: 0.88rem; height: 42px;">
                                <i class="fas fa-<?php echo $badge_icon; ?>"></i>
                                <span><?php echo $badge_text; ?></span>
                            </div>
                        </div>

                        <?php if ($pdf_name): ?>
                        <div class="col-6">
                            <a href="<?php echo BASE_URL; ?>/uploads/pdfs/<?php echo htmlspecialchars($pdf_name); ?>" 
                               class="btn btn-outline-danger w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 shadow-none"
                               style="padding: 0.6rem 1rem; font-size: 0.88rem; height: 42px;"
                               download>
                                <i class="fas fa-file-pdf"></i>
                                <span class="fw-semibold">Datasheet (PDF)</span>
                            </a>
                        </div>
                        <?php endif; ?>
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
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Suggested Equipment Suggestions -->
    <?php if (!empty($related_products)): ?>
    <div class="mt-5 pt-4 border-top border-light" id="suggested">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold mb-1 text-dark">Suggested Machinery</h5>
                <p class="text-muted small mb-0">Explore other certified industrial generators and turbines.</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-sm btn-outline-secondary rounded-2 px-3 py-1 text-decoration-none" style="font-size: 0.85rem;">
                Browse All <i class="fas fa-arrow-right ms-1 small"></i>
            </a>
        </div>
        
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            <?php foreach ($related_products as $rel): ?>
            <div class="col">
                <div class="card product-card product-card-modern h-100 border-0 bg-white shadow-sm rounded-3 position-relative overflow-hidden" data-href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $rel['id']; ?>">
                    
                    <!-- Image -->
                    <div class="product-image-container position-relative overflow-hidden" style="border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                        <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $rel['id']; ?>" class="d-block text-decoration-none">
                            <?php $rel_img = product_image_url($rel['main_image']); ?>
                            <?php if ($rel_img): ?>
                                <img src="<?php echo htmlspecialchars($rel_img); ?>" 
                                     alt="<?php echo htmlspecialchars($rel['name']); ?>" 
                                     class="w-100" style="height: 180px; object-fit: cover; transition: transform 0.4s ease;">
                            <?php else: ?>
                                <?php echo product_image_placeholder('180px', htmlspecialchars($rel['name'])); ?>
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="product-title fw-bold text-dark text-truncate mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                                <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $rel['id']; ?>" class="text-dark text-decoration-none hover-primary">
                                    <?php echo htmlspecialchars($rel['name']); ?>
                                </a>
                            </h6>
                            <div class="d-flex align-items-center gap-2 mb-3 text-muted small product-status-line">
                                <span class="text-danger fw-semibold" style="font-size: 0.78rem;">
                                    <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($rel['location'] ?? 'Global Depot'); ?>
                                </span>
                            </div>
                        </div>

                        <div class="pt-3 border-top border-light d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-uppercase text-muted" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px;">ESTIMATED PRICE</div>
                                <div class="product-price fw-extrabold text-primary" style="font-size: 1.1rem;">
                                    <?php echo format_price($rel['price']); ?>
                                </div>
                            </div>

                            <button type="button" 
                                    class="btn btn-success rounded-circle d-flex align-items-center justify-content-center shadow-sm product-whatsapp-pulse" 
                                    style="width: 36px; height: 36px; background: #25D366; border: 0;" 
                                    onclick="event.stopPropagation(); contactWhatsApp('<?php echo addslashes(htmlspecialchars($rel['name'])); ?>','<?php echo $settings['whatsapp_number'] ?? ''; ?>')" 
                                    title="Inquire via WhatsApp">
                                <i class="fab fa-whatsapp fs-6 text-white"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
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
