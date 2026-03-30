<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * ITEMS_PER_PAGE;

// Search filter
$search_filter = '';
$params = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_filter = ' AND p.name LIKE ?';
    $params[] = '%' . $_GET['search'] . '%';
}

// Count total products
$count_query = "SELECT COUNT(*) as total FROM products p WHERE p.status = 'active'" . $search_filter;
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_products = $stmt->fetch()['total'];
$total_pages = ceil($total_products / ITEMS_PER_PAGE);

// Get products (All active products, not just featured)
$query = "SELECT p.* FROM products p WHERE p.status = 'active'" . $search_filter . " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$params[] = ITEMS_PER_PAGE;
$params[] = $offset;
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!-- Hero Section (Compact & Centered) -->
<section class="hero-section py-5 bg-transparent">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="fw-bold mb-3 text-dark" style="font-size: 2.5rem;">Welcome to Gpower</h1>
                <p class="text-muted mb-4 lead">Premium Power Solutions.<br>Quality & Reliability Guaranteed.</p>
                
                <!-- Compact Search -->
                <form action="" method="GET" class="hero-search-form mb-4 mx-auto position-relative">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Search for a product..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- All Products (Dense Grid) -->
<section class="py-4 bg-transparent" id="products">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold mb-0">Our Products</h4>
        </div>
        
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="products-grid">
            <?php foreach ($products as $product): ?>
            <div class="col">
                <div class="card product-card h-100 border-0 shadow-sm position-relative" data-href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>">
                    <div class="product-image-wrap">
                        <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="d-block">
                            <?php if ($product['main_image']): ?>
                                <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <div class="no-image-placeholder d-flex align-items-center justify-content-center" style="height:160px;">
                                    <i class="fas fa-camera fa-2x mb-2 opacity-50"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                    </div>

                    <div class="card-body">
                        <h6 class="product-title mb-2">
                            <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="text-dark text-decoration-none">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h6>
                        <div class="d-flex justify-content-between align-items-center mt-2 info-row">
                            <div>
                                <div class="product-price"><?php echo format_price($product['price']); ?></div>
                                <div class="product-location"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($product['location'] ?? ''); ?></div>
                            </div>
                            <div class="product-contact">
                                <button type="button" class="whatsapp-inline" onclick="contactWhatsApp('<?php echo addslashes(htmlspecialchars($product['name'])); ?>','<?php echo $settings['whatsapp_number']; ?>')" aria-label="Contact via WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($products)): ?>
        <div class="text-center py-5">
            <p class="text-muted small">No products found.</p>
            <?php if(isset($_GET['search'])): ?>
                <a href="<?php echo BASE_URL; ?>/" class="btn btn-sm btn-outline-primary rounded-pill">Clear Search</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Pagination / See More -->
        <?php if ($page < $total_pages): ?>
        <div class="text-center mt-5">
            <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>#products" 
               class="btn btn-outline-primary rounded-pill px-5 py-2 fw-bold">
                See More <i class="fas fa-arrow-down ms-2"></i>
            </a>
        </div>
        <?php endif; ?>
        
        <?php if ($page > 1): ?>
        <div class="text-center mt-3">
            <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>#products" 
               class="text-muted small text-decoration-none">
                <i class="fas fa-arrow-up me-1"></i> Back
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
