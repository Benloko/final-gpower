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

?>

<div class="container my-5">
    <?php if ($from_admin): ?>
    <div class="mb-3">
        <a href="<?php echo BASE_URL; ?>/admin/product-edit.php?id=<?php echo $product_id; ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fas fa-arrow-left me-1"></i>Back to edit
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
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                                     <a href="https://wa.me/<?php echo $settings['whatsapp_number']; ?>?text=<?php echo urlencode('Hello, I am interested in ' . $product['name']); ?>" 
                               class="btn btn-dark w-100 rounded-4 d-flex align-items-center justify-content-center gap-2 shadow-sm"
                               target="_blank"
                               style="background: #25D366; border: none; padding: 0.6rem 1rem; font-size: 0.9rem;">
                                <i class="fab fa-whatsapp"></i>
                                <span class="fw-bold">WhatsApp</span>
                            </a>
                        </div>
                        
                        <div class="col-6">
                                     <a href="mailto:<?php echo $settings['site_email'] ?? 'contact@gpower.ci'; ?>?subject=<?php echo urlencode('Inquiry: ' . $product['name']); ?>&body=<?php echo urlencode('Hello, I would like more information about ' . $product['name'] . ' priced at ' . format_price($product['price']) . '.'); ?>" 
                               class="btn btn-dark w-100 rounded-4 d-flex align-items-center justify-content-center gap-2 shadow-sm"
                               style="padding: 0.6rem 1rem; font-size: 0.9rem;">
                                <i class="fas fa-envelope"></i>
                                <span class="fw-bold">Email</span>
                            </a>
                        </div>
                    </div>

                    <!-- Stock Status & PDF Section -->
                    <div class="row g-2 mb-4">
                        <!-- Stock Status Badge -->
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
                                <div style="position: absolute; top: -50%; right: -50%; width: 200px; height: 200px; background: rgba(255, 255, 255, 0.1); border-radius: 50%;"></div>
                                <div style="position: relative; z-index: 1;">
                                    <div class="mb-2">
                                        <?php 
                                        $quantity = (int)$product['quantity'];
                                        if ($quantity > 10) {
                                            $badge_color = '#10b981'; // green
                                            $badge_text = 'In Stock';
                                            $badge_icon = 'check-circle';
                                        } elseif ($quantity > 0) {
                                            $badge_color = '#f59e0b'; // amber
                                            $badge_text = 'Limited Stock';
                                            $badge_icon = 'exclamation-circle';
                                        } else {
                                            $badge_color = '#ef4444'; // red
                                            $badge_text = 'Out of Stock';
                                            $badge_icon = 'times-circle';
                                        }
                                        ?>
                                        <i class="fas fa-<?php echo $badge_icon; ?>" style="font-size: 1.5rem; color: white;"></i>
                                    </div>
                                    <h6 class="fw-bold text-white mb-1" style="font-size: 0.9rem;"><?php echo $badge_text; ?></h6>
                                    <div class="fw-bold text-white" style="font-size: 1.5rem;">
                                        <?php echo number_format($quantity); ?>
                                        <span style="font-size: 0.85rem; opacity: 0.9;">units</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Download Section -->
                        <?php $pdf_name = $product['pdf_file'] ?? ($product['pdf_path'] ?? ''); ?>
                        <?php if ($pdf_name): ?>
                        <div class="col-md-6">
                            <a href="<?php echo BASE_URL; ?>/uploads/pdfs/<?php echo htmlspecialchars($pdf_name); ?>" 
                               class="btn w-100 h-100 rounded-3 d-flex align-items-center justify-content-center flex-column gap-2 shadow-sm"
                               style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none; padding: 1rem; text-decoration: none; transition: transform 0.2s ease;"
                               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 16px rgba(245, 87, 108, 0.3)';"
                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='';"
                               download>
                                <i class="fas fa-file-pdf" style="font-size: 1.5rem;"></i>
                                <div>
                                    <div class="fw-bold" style="font-size: 0.9rem;">Product Details</div>
                                    <small style="opacity: 0.9; font-size: 0.75rem;">Download PDF & Specs</small>
                                </div>
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
                    
                    <!-- Back to Home Button -->
                    <?php if (!$from_admin): ?>
                    <div class="mt-4 pt-4 border-top">
                        <a href="<?php echo BASE_URL; ?>/" 
                           class="btn btn-outline-dark w-100 rounded-4 d-flex align-items-center justify-content-center gap-2"
                           style="padding: 0.75rem 1rem; font-size: 0.95rem;">
                            <i class="fas fa-arrow-left"></i>
                            <span class="fw-semibold"><?php echo t('back_home'); ?></span>
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
