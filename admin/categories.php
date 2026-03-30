<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$pdo = getPDOConnection();

//Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    redirect('categories.php');
}

// Get all categories
$stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count 
                     FROM categories c 
                     LEFT JOIN products p ON c.id = p.category_id 
                     GROUP BY c.id 
                     ORDER BY c.created_at DESC");
$categories = $stmt->fetchAll();

$page_title = 'Categories';
require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold text-primary">Categories Management</h4>
    <a href="category-form.php" class="btn btn-primary rounded-pill px-4 shadow-sm">
        <i class="fas fa-plus me-2"></i>Add New Category
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 text-muted small text-uppercase fw-bold">Category</th>
                        <th class="px-4 py-3 text-muted small text-uppercase fw-bold">Description</th>
                        <th class="px-4 py-3 text-muted small text-uppercase fw-bold text-center">Products</th>
                        <th class="px-4 py-3 text-muted small text-uppercase fw-bold">Status</th>
                        <th class="px-4 py-3 text-muted small text-uppercase fw-bold text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td class="px-4 py-3">
                            <div class="d-flex align-items-center">
                                <?php if ($category['image']): ?>
                                    <img src="<?php echo BASE_URL; ?>/uploads/<?php echo htmlspecialchars($category['image']); ?>" 
                                         class="rounded-3 me-3 shadow-sm" style="width: 48px; height: 48px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded-3 me-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                                        <i class="fas fa-tags text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($category['name']); ?></div>
                                    <div class="small text-muted">ID: #<?php echo $category['id']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-muted small"><?php echo htmlspecialchars(substr($category['description'] ?? '', 0, 50)) . (strlen($category['description'] ?? '') > 50 ? '...' : ''); ?></span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge bg-info-subtle text-info rounded-pill px-3"><?php echo $category['product_count']; ?> items</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge bg-<?php echo $category['status'] == 'active' ? 'success' : 'secondary'; ?>-subtle text-<?php echo $category['status'] == 'active' ? 'success' : 'secondary'; ?> rounded-pill px-3">
                                <?php echo ucfirst($category['status']); ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-end">
                            <div class="btn-group">
                                <a href="category-form.php?id=<?php echo $category['id']; ?>" 
                                   class="btn btn-sm btn-light text-primary" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?delete=<?php echo $category['id']; ?>" 
                                   class="btn btn-sm btn-light text-danger" 
                                   onclick="return confirmDelete('Delete this category?')"
                                   title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-tags fa-3x text-muted opacity-50"></i>
                    </div>
                </div>
                <h5 class="text-muted fw-bold">No categories found</h5>
                <p class="text-muted small mb-4">Create categories to organize your products.</p>
                <a href="category-form.php" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-plus me-2"></i>Create Category
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
