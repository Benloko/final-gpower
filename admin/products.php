<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$pdo = getPDOConnection();

// Check if products table has a 'quantity' column
$hasQuantity = false;
try {
    $colStmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'products' AND COLUMN_NAME = 'quantity'");
    $colStmt->execute([DB_NAME]);
    $hasQuantity = $colStmt->fetch()['cnt'] > 0;
} catch (Exception $e) {
    // If information_schema query fails, keep hasQuantity = false
    $hasQuantity = false;
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Get product to delete images
    $stmt = $pdo->prepare("SELECT main_image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if ($product && $product['main_image']) {
        $image_path = PRODUCT_IMAGE_PATH . $product['main_image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete product images from gallery
    $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
    $stmt->execute([$id]);
    while ($img = $stmt->fetch()) {
        $image_path = PRODUCT_IMAGE_PATH . $img['image_path'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete product
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    redirect('products.php');
}

// Check if filtering by featured products
$featuredFilter = isset($_GET['featured']) && $_GET['featured'] == '1';

// Get all products (include quantity if present)
if ($hasQuantity) {
    if ($featuredFilter) {
        $sql = "SELECT p.*, COALESCE(p.quantity, 0) AS quantity FROM products p WHERE p.featured = 1 ORDER BY p.created_at DESC";
    } else {
        $sql = "SELECT p.*, COALESCE(p.quantity, 0) AS quantity FROM products p ORDER BY p.created_at DESC";
    }
} else {
    if ($featuredFilter) {
        $sql = "SELECT p.*, 0 AS quantity FROM products p WHERE p.featured = 1 ORDER BY p.created_at DESC";
    } else {
        $sql = "SELECT p.*, 0 AS quantity FROM products p ORDER BY p.created_at DESC";
    }
}
$stmt = $pdo->query($sql);
$products = $stmt->fetchAll();

$page_title = 'Products';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Header with Search and Add Button -->
<div class="row g-3 mb-4">    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h4 class="mb-1 fw-bold text-dark">
                    <i class="fas fa-box-open text-primary me-2"></i>Gestion des Produits
                </h4>
                <p class="text-muted small mb-0">Gérer votre catalogue de produits</p>
            </div>
            <a href="product-form.php" class="btn btn-primary rounded-pill d-flex align-items-center gap-2 shadow-sm px-4 flex-shrink-0">
                <i class="fas fa-plus-circle"></i>
                <span>Ajouter un Produit</span>
            </a>
        </div>
    </div>
    
    <!-- Enhanced Search Bar -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-2">
                <div class="position-relative">
                    <div class="position-absolute" style="left: 1.25rem; top: 50%; transform: translateY(-50%); z-index: 10;">
                        <i class="fas fa-search text-muted"></i>
                    </div>
                    <input type="text" 
                           class="form-control form-control-lg ps-5 pe-5 border-0 shadow-sm" 
                           id="productSearch" 
                           placeholder="Rechercher un produit par nom, catégorie, prix..."
                           style="border-radius: 12px; background-color: #ffffff; font-size: 0.95rem; height: 50px;">
                    <div class="position-absolute" style="right: 0.5rem; top: 50%; transform: translateY(-50%); z-index: 10;">
                        <button class="btn btn-sm btn-light rounded-pill px-3 d-none" id="clearSearch">
                            <i class="fas fa-times me-1"></i>Effacer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light d-none d-md-table-header-group">
                    <tr>
                        <th class="px-3 px-md-4 py-3 text-muted small text-uppercase fw-bold">Product</th>
                        <th class="px-3 px-md-4 py-3 text-muted small text-uppercase fw-bold text-center">Unit</th>
                        <th class="px-3 px-md-4 py-3 text-muted small text-uppercase fw-bold">Unit Price</th>
                        <th class="px-3 px-md-4 py-3 text-muted small text-uppercase fw-bold">Total</th>
                        <th class="px-3 px-md-4 py-3 text-muted small text-uppercase fw-bold text-center">Featured</th>
                        <th class="px-3 px-md-4 py-3 text-muted small text-uppercase fw-bold">Status</th>
                        <th class="px-3 px-md-4 py-3 text-muted small text-uppercase fw-bold text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <?php 
                    $displayLimit = 10; // Show only 5 products initially
                    $productCount = 0;
                    foreach ($products as $product): 
                        $productCount++;
                        $hideClass = $productCount > $displayLimit ? 'product-row-hidden' : '';
                    ?>
                    <tr class="product-row <?php echo $hideClass; ?>" style="<?php echo $hideClass ? 'display: none;' : ''; ?>"">
                        <td class="px-2 px-md-4 py-2 py-md-3">
                            <div class="d-flex align-items-center">
                                <?php if ($product['main_image']): ?>
                                    <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                                         class="rounded-3 me-2 me-md-3 shadow-sm" style="width: 36px; height: 36px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light rounded-3 me-2 me-md-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px;">
                                        <i class="fas fa-bolt text-muted small"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold text-dark small"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div class="small text-muted d-none d-md-block">ID: #<?php echo $product['id']; ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-2 px-md-4 py-2 py-md-3 text-center small">
                            <?php echo number_format((int)$product['quantity']); ?>
                        </td>
                        <td class="px-2 px-md-4 py-2 py-md-3 fw-bold text-primary small">$<?php echo number_format($product['price'], 2); ?></td>
                        <td class="px-2 px-md-4 py-2 py-md-3 fw-bold text-primary small">$<?php echo number_format($product['price'] * (int)$product['quantity'], 2); ?></td>
                        <td class="px-2 px-md-4 py-2 py-md-3 text-center">
                            <?php if ($product['featured']): ?>
                                <i class="fas fa-star text-warning"></i>
                            <?php else: ?>
                                <i class="far fa-star text-muted opacity-25"></i>
                            <?php endif; ?>
                        </td>
                        <td class="px-2 px-md-4 py-2 py-md-3">
                            <span class="badge bg-<?php echo $product['status'] == 'active' ? 'success' : 'secondary'; ?>-subtle text-<?php echo $product['status'] == 'active' ? 'success' : 'secondary'; ?> rounded-pill px-2 small">
                                <?php echo ucfirst($product['status']); ?>
                            </span>
                        </td>
                        <td class="px-2 px-md-4 py-2 py-md-3 text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="product-edit.php?id=<?php echo $product['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary rounded-circle" 
                                   style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                          <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>&from=admin" 
                                   class="btn btn-sm btn-outline-info rounded-circle" 
                                   style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                              title="View on site (opens in new tab)">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="#" 
                                   class="btn btn-sm btn-outline-danger rounded-circle delete-product-btn" 
                                   style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;"
                                   data-product-id="<?php echo $product['id']; ?>"
                                   data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                   title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-box-open fa-3x text-muted opacity-50"></i>
                    </div>
                </div>
                <h5 class="text-muted fw-bold">No products found</h5>
                <p class="text-muted small mb-4">Get started by creating your first product listing.</p>
                <a href="product-form.php" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-plus me-2"></i>Create Product
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (count($products) > $displayLimit): ?>
    <div class="card-footer bg-white border-0 text-center py-3">
        <button id="loadMoreBtn" class="btn btn-outline-primary rounded-pill px-4" onclick="loadMoreProducts()">
            <i class="fas fa-chevron-down me-2"></i>Voir plus
        </button>
        <button id="showLessBtn" class="btn btn-outline-secondary rounded-pill px-4" style="display: none;" onclick="showLessProducts()">
            <i class="fas fa-chevron-up me-2"></i>Voir moins
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-trash-alt text-danger" style="font-size: 1.5rem;"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-2">Supprimer ce produit ?</h5>
                <p class="text-muted mb-0" id="deleteProductName"></p>
                <p class="text-muted small mt-2">Cette action supprimera le produit et toutes ses images.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger rounded-pill px-4">
                    <i class="fas fa-trash me-1"></i>Supprimer
                </a>
            </div>
        </div>
    </div>
</div>

<script>
let showingAll = false;

// Delete product modal - wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    const deleteModalEl = document.getElementById('deleteModal');
    if (deleteModalEl) {
        const deleteModal = new bootstrap.Modal(deleteModalEl);
        const deleteButtons = document.querySelectorAll('.delete-product-btn');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                
                document.getElementById('deleteProductName').textContent = productName;
                document.getElementById('confirmDeleteBtn').href = '?delete=' + productId;
                
                deleteModal.show();
            });
        });
    }
});

function loadMoreProducts() {
    const hiddenRows = document.querySelectorAll('.product-row-hidden');
    hiddenRows.forEach(row => {
        row.style.display = '';
    });
    document.getElementById('loadMoreBtn').style.display = 'none';
    document.getElementById('showLessBtn').style.display = 'inline-block';
    showingAll = true;
}

function showLessProducts() {
    const hiddenRows = document.querySelectorAll('.product-row-hidden');
    hiddenRows.forEach(row => {
        row.style.display = 'none';
    });
    document.getElementById('loadMoreBtn').style.display = 'inline-block';
    document.getElementById('showLessBtn').style.display = 'none';
    showingAll = false;
    // Scroll to top of table
    document.querySelector('.table-responsive').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Real-time product search
const searchInput = document.getElementById('productSearch');
const clearBtn = document.getElementById('clearSearch');

searchInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('.product-row');
    let visibleCount = 0;
    
    // Show/hide clear button
    if (searchTerm) {
        clearBtn.classList.remove('d-none');
    } else {
        clearBtn.classList.add('d-none');
    }
    
    rows.forEach((row, index) => {
        const productName = row.querySelector('.fw-bold').textContent.toLowerCase();
        if (productName.includes(searchTerm)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Hide load more button when searching
    if (searchTerm) {
        document.getElementById('loadMoreBtn')?.style.setProperty('display', 'none', 'important');
        document.getElementById('showLessBtn')?.style.setProperty('display', 'none', 'important');
    } else {
        // Restore load more button state when search is cleared
        if (!showingAll && visibleCount > <?php echo $displayLimit; ?>) {
            document.getElementById('loadMoreBtn').style.display = 'inline-block';
        }
    }
});

// Clear search button
clearBtn.addEventListener('click', function() {
    searchInput.value = '';
    searchInput.dispatchEvent(new Event('input'));
    searchInput.focus();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
