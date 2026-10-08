<?php
$page_title = 'Power Equipment Catalog';
require_once __DIR__ . '/includes/header.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * ITEMS_PER_PAGE;

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
$total_pages = ceil($total_products / ITEMS_PER_PAGE);

// Get products
$query = "SELECT p.*
          FROM products p
          WHERE p.status = 'active'" . $search_filter . "
          ORDER BY p.created_at DESC 
          LIMIT ? OFFSET ?";
$params[] = ITEMS_PER_PAGE;
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
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm rounded-3 p-3.5 sticky-lg-top" style="top: 100px; z-index: 10;">
                <h6 class="fw-bold mb-3 text-dark text-uppercase" style="font-size: 0.82rem; letter-spacing: 0.5px;">Search Machinery</h6>
                <form method="GET" action="" class="sidebar-search-form mb-4">
                    <div class="input-group bg-light rounded-pill p-1 border">
                        <input type="text" 
                               name="search" 
                               class="form-control border-0 shadow-none bg-transparent ps-3 fs-6" 
                               placeholder="Search brand, model..."
                               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                               style="height: 36px; font-size: 0.85rem;">
                        <button class="btn btn-primary rounded-pill px-3" type="submit" style="height: 36px;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <div class="p-3 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-25 text-center">
                    <i class="fab fa-whatsapp fa-2x text-success mb-2"></i>
                    <h6 class="fw-bold text-dark mb-1">Need Technical Specs?</h6>
                    <p class="text-muted small mb-2" style="font-size: 0.75rem;">Get full datasheets & inspection reports via WhatsApp.</p>
                    <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>" target="_blank" class="btn btn-success btn-sm w-100 rounded-pill fw-bold">Contact Engineer</a>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light">
                <h5 class="fw-bold mb-0 text-dark">
                    All Listings <span class="badge bg-primary bg-opacity-10 text-primary ms-2 rounded-pill"><?php echo $total_products; ?> Items</span>
                </h5>
                <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-times me-1"></i> Reset Filter
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 gx-3.5 gy-4.5" id="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="col">
                    <div class="card product-card-modern h-100 border-0 bg-white shadow-sm rounded-3 position-relative overflow-hidden" data-href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>">
                        
                        <!-- Top Status Badge (Shield Icon Only) -->
                        <div class="position-absolute top-0 start-0 m-2.5 z-2">
                            <span class="badge bg-dark bg-opacity-75 text-warning backdrop-blur border border-white border-opacity-20 rounded-circle p-1.5 shadow-sm" title="Verified Machinery">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                        </div>

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
                                        class="btn btn-success rounded-circle d-flex align-items-center justify-content-center shadow product-whatsapp-pulse" 
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
                <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-primary rounded-pill px-4">Reset Search</a>
            </div>
            <?php endif; ?>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-center align-items-center gap-2 mt-5">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                       class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold">
                        <i class="fas fa-arrow-left me-2"></i> Previous
                    </a>
                <?php endif; ?>

                <span class="text-muted px-3 small fw-semibold">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>" 
                       class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                        Next Page <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
