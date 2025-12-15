<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$pdo = getPDOConnection();

// Get statistics
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE status = 'active'");
$total_products = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE featured = 1 AND status = 'active'");
$featured_products = $stmt->fetch()['total'];

// Get recent products
$stmt = $pdo->query("SELECT p.* 
                     FROM products p 
                     ORDER BY p.created_at DESC 
                     LIMIT 5");
$recent_products = $stmt->fetchAll();

$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row g-2 mb-4">
    <!-- Statistics Cards -->
    <div class="col-12">
        <a href="products.php" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3" style="transition: transform 0.2s, box-shadow 0.2s;">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-primary bg-opacity-10 rounded-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-box text-primary" style="font-size: 0.85rem;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.2px;">Total Products</p>
                            <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.35rem;"><?php echo $total_products; ?></h5>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right text-muted" style="font-size: 0.7rem;"></i>
                </div>
            </div>
        </a>
    </div>
    
    <div class="col-12">
        <a href="products.php?featured=1" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-3" style="transition: transform 0.2s, box-shadow 0.2s;">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-warning bg-opacity-10 rounded-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-star text-warning" style="font-size: 0.85rem;"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0 text-uppercase fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.2px;">Featured Items</p>
                            <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.35rem;"><?php echo $featured_products; ?></h5>
                        </div>
                    </div>
                    <i class="fas fa-arrow-right text-muted" style="font-size: 0.7rem;"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1) !important;
}
</style>

<!-- Recent Products -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-2 py-md-3 px-3 px-md-4">
                <h5 class="mb-0 fw-bold fs-6 fs-md-5">Recent Products</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light d-none d-md-table-header-group">
                            <tr>
                                <th class="px-4 py-3 text-muted small text-uppercase fw-bold">Product</th>
                                <th class="px-4 py-3 text-muted small text-uppercase fw-bold">Price</th>
                                <th class="px-4 py-3 text-muted small text-uppercase fw-bold">Status</th>
                                <th class="px-4 py-3 text-muted small text-uppercase fw-bold text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_products as $product): ?>
                            <tr>
                                <td class="px-2 px-md-4 py-2 py-md-3">
                                    <div class="d-flex align-items-center">
                                        <?php if ($product['main_image']): ?>
                                            <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                                                 class="rounded-3 me-2 me-md-3" style="width: 32px; height: 32px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded-3 me-2 me-md-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                <i class="fas fa-bolt text-muted small"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold text-dark small"><?php echo htmlspecialchars($product['name']); ?></span>
                                            <?php if ($product['featured']): ?>
                                                <i class="fas fa-star text-warning" style="font-size: 0.85rem;" title="Featured"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 px-md-4 py-2 py-md-3 fw-bold text-primary small">$<?php echo number_format($product['price'], 2); ?></td>
                                <td class="px-2 px-md-4 py-2 py-md-3">
                                    <span class="badge bg-<?php echo $product['status'] == 'active' ? 'success' : 'secondary'; ?>-subtle text-<?php echo $product['status'] == 'active' ? 'success' : 'secondary'; ?> rounded-pill px-2 small">
                                        <?php echo ucfirst($product['status']); ?>
                                    </span>
                                </td>
                                <td class="px-2 px-md-4 py-2 py-md-3 text-end">
                                    <a href="product-edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-light text-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
