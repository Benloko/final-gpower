<?php
$page_title = 'Power Equipment Catalog';
require_once __DIR__ . '/includes/header.php';

// Pagination (12 items per page for clean 4-column and 3-column rows)
$per_page = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

// Search filter
$search_filter = '';
$params = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_filter = ' AND (p.name LIKE ? OR p.specifications LIKE ?)';
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
}

// Count total products
$count_query = "SELECT COUNT(*) as total FROM products p WHERE p.status = 'active'" . $search_filter;
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_products = $stmt->fetch()['total'];
$total_pages = ceil($total_products / $per_page);

// Get products
$query = "SELECT p.*
          FROM products p
          WHERE p.status = 'active'" . $search_filter . "
          ORDER BY p.created_at DESC 
          LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!-- Header Banner -->
<div class="py-4 bg-dark text-white border-bottom border-secondary border-opacity-25" style="background: linear-gradient(135deg, #0b132b 0%, #1c2541 100%) !important;">
    <div class="container py-2">
        <h2 class="fw-extrabold text-white mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif;">Heavy Equipment Catalog</h2>
        <p class="text-white-50 mb-0 small">Browse our full inventory of diesel generators, gas turbines, heavy engines, and power plant components.</p>
    </div>
</div>

<div class="container my-5">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-2 border-bottom border-light">
        <!-- Left: Title -->
        <h5 class="fw-bold mb-0 text-dark">All Listings</h5>
        
        <!-- Right: Search Bar & Reset -->
        <div class="d-flex align-items-center gap-2">
            <form method="GET" action="<?php echo BASE_URL; ?>/products.php" class="d-flex align-items-center m-0">
                <div class="input-group input-group-sm bg-white rounded-2 border shadow-none" style="width: 290px;">
                    <span class="input-group-text bg-transparent border-0 text-muted ps-2.5 pe-1">
                        <i class="fas fa-search small"></i>
                    </span>
                    <input type="text" 
                           name="search" 
                           class="form-control border-0 bg-transparent shadow-none ps-1 pe-2" 
                           placeholder="Search brand, model..."
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                           style="font-size: 0.85rem; height: 34px;">
                    <?php if (!empty($_GET['search'])): ?>
                        <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-sm btn-link text-muted text-decoration-none px-2 d-flex align-items-center" title="Clear search">
                            <i class="fas fa-times small"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>

            <?php if (!empty($_GET['search'])): ?>
                <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-sm btn-outline-secondary rounded-2 px-3 py-1 text-decoration-none" style="font-size: 0.85rem; height: 34px; line-height: 24px;">
                    <i class="fas fa-times me-1"></i> Reset
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="products-grid">
        <?php foreach ($products as $product): ?>
        <div class="col">
            <div class="card product-card product-card-modern h-100 border-0 bg-white shadow-sm rounded-3 position-relative overflow-hidden" data-href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>">
                
                <!-- Image / Placeholder -->
                <div class="product-image-container position-relative overflow-hidden" style="border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                    <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="d-block text-decoration-none">
                        <?php $img_url = product_image_url($product['main_image']); ?>
                        <?php if ($img_url): ?>
                            <img src="<?php echo htmlspecialchars($img_url); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 class="w-100" style="height: 180px; object-fit: cover; transition: transform 0.4s ease;">
                        <?php else: ?>
                            <?php echo product_image_placeholder('180px', htmlspecialchars($product['name'])); ?>
                        <?php endif; ?>
                    </a>
                </div>
                
                <!-- Body -->
                <div class="card-body p-3.5 d-flex flex-column justify-content-between">
                    <div>
                        <h6 class="product-title fw-bold text-dark text-truncate mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="text-dark text-decoration-none hover-primary">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h6>
                        <div class="d-flex align-items-center gap-2 mb-3 text-muted small product-status-line">
                            <span class="text-danger fw-semibold" style="font-size: 0.78rem;">
                                <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($product['location'] ?? 'Global Depot'); ?>
                            </span>
                        </div>
                    </div>

                    <div class="pt-3 border-top border-light d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-muted" style="font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px;">ESTIMATED PRICE</div>
                            <div class="product-price fw-extrabold text-primary" style="font-size: 1.1rem;">
                                <?php echo format_price($product['price']); ?>
                            </div>
                        </div>

                        <button type="button" 
                                class="btn btn-success rounded-circle d-flex align-items-center justify-content-center shadow-sm product-whatsapp-pulse" 
                                style="width: 36px; height: 36px; background: #25D366; border: 0;" 
                                onclick="event.stopPropagation(); contactWhatsApp('<?php echo addslashes(htmlspecialchars($product['name'])); ?>','<?php echo $settings['whatsapp_number'] ?? ''; ?>')" 
                                title="Inquire via WhatsApp">
                            <i class="fab fa-whatsapp fs-6 text-white"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <?php if (empty($products)): ?>
    <div class="text-center py-5 bg-white rounded-3 shadow-sm border border-light my-4">
        <div class="mb-3 text-muted opacity-50">
            <i class="fas fa-search-minus fa-3x"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">No Equipment Found</h5>
        <p class="text-muted small mb-3">Adjust your search parameters to locate specific equipment models.</p>
        <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-sm btn-outline-secondary rounded-2 px-4 py-2">Reset Search</a>
    </div>
    <?php endif; ?>
    
    <!-- Simple & Clean Pagination -->
    <?php if ($total_pages > 1): ?>
    <?php $search_param = (!empty($_GET['search'])) ? '&search=' . urlencode($_GET['search']) : ''; ?>
    <div class="d-flex justify-content-center align-items-center gap-3 mt-5 pt-2">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?><?php echo $search_param; ?>" 
               class="btn btn-sm btn-outline-secondary rounded-2 px-3 py-1.5"
               style="font-size: 0.85rem;">
                <i class="fas fa-chevron-left me-1 small"></i> Previous
            </a>
        <?php else: ?>
            <span class="btn btn-sm btn-light border text-muted rounded-2 px-3 py-1.5 disabled" 
                  style="font-size: 0.85rem; opacity: 0.5;">
                <i class="fas fa-chevron-left me-1 small"></i> Previous
            </span>
        <?php endif; ?>

        <span class="text-muted small px-1" style="font-size: 0.85rem;">
            Page <span class="fw-semibold text-dark"><?php echo $page; ?></span> / <?php echo $total_pages; ?>
        </span>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?><?php echo $search_param; ?>" 
               class="btn btn-sm btn-outline-secondary rounded-2 px-3 py-1.5"
               style="font-size: 0.85rem;">
                Next <i class="fas fa-chevron-right ms-1 small"></i>
            </a>
        <?php else: ?>
            <span class="btn btn-sm btn-light border text-muted rounded-2 px-3 py-1.5 disabled" 
                  style="font-size: 0.85rem; opacity: 0.5;">
                Next <i class="fas fa-chevron-right ms-1 small"></i>
            </span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
