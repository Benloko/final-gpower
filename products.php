<?php
$page_title = 'Products';
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

<div class="container my-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <!-- Search -->
            <div class="mb-4">
                <h5 class="fw-bold mb-3"><?php echo t('search'); ?></h5>
                <form method="GET" action="" class="sidebar-search-form">
                    <div class="input-group">
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="<?php echo t('search'); ?>..."
                               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Categories (Removed as per request) -->
        </div>
        
        <!-- Products Grid -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0"><?php echo t('all_products'); ?></h2>
            </div>
            
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3" id="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="col">
                    <div class="card product-card h-100 border-0 shadow-sm">
                        <div class="position-relative bg-white p-3 text-center" style="height: 160px;">
                            
                            <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="d-block h-100 text-decoration-none">
                                <?php if ($product['main_image']): ?>
                                    <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                         class="img-fluid h-100" style="object-fit: contain;">
                                <?php else: ?>
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 bg-light rounded-3 text-muted">
                                        <i class="fas fa-camera fa-2x mb-2 opacity-50"></i>
                                        <span class="small" style="font-size: 0.7rem;">No Image</span>
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
                                <button class="btn btn-sm btn-light text-success rounded-circle p-1" 
                                        onclick="contactWhatsApp('<?php echo htmlspecialchars($product['name']); ?>', '<?php echo $settings['whatsapp_number']; ?>')"
                                        style="width: 28px; height: 28px;">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-search fa-3x text-muted opacity-25"></i>
                </div>
                <h5 class="text-muted"><?php echo t('no_products'); ?></h5>
                <p class="text-muted small">Try adjusting your search or filter to find what you're looking for.</p>
                <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-primary rounded-pill px-4 mt-2">Clear Filters</a>
            </div>
            <?php endif; ?>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link rounded-circle mx-1 border-0 <?php echo $i == $page ? 'bg-primary text-white' : 'text-muted'; ?>" 
                           href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>"
                           style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
