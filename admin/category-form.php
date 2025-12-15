<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$pdo = getPDOConnection();

$category = null;
$error = '';
$success = '';

// Get category for edit
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();
    
    if (!$category) {
        redirect('categories.php');
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $status = sanitize($_POST['status']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));
    
    if (empty($name)) {
        $error = 'Category name is required';
    } else {
        // Handle image upload
        $image_path = $category['image'] ?? '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            
            if (in_array($file_ext, ALLOWED_IMAGE_EXTENSIONS) && $_FILES['image']['size'] <= MAX_FILE_SIZE) {
                $new_filename = 'cat_' . time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = UPLOAD_PATH . $new_filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    // Delete old image if exists
                    if ($image_path && file_exists(UPLOAD_PATH . $image_path)) {
                        unlink(UPLOAD_PATH . $image_path);
                    }
                    $image_path = $new_filename;
                }
            }
        }
        
        if ($category) {
            // Update
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, image = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $image_path, $status, $category['id']]);
            $success = 'Category updated successfully';
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, image, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $image_path, $status]);
            $success = 'Category created successfully';
        }
        
        // Refresh category data
        if ($category) {
            $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$category['id']]);
            $category = $stmt->fetch();
        }
    }
}

$page_title = $category ? 'Edit Category' : 'Add New Category';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <?php if ($error): ?>
        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold text-primary">Category Details</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Category Name *</label>
                        <input type="text" 
                               name="name" 
                               class="form-control form-control-lg bg-light border-0" 
                               value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" 
                               placeholder="e.g. Solar Panels"
                               required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Description</label>
                        <textarea name="description" 
                                  class="form-control bg-light border-0" 
                                  rows="4"
                                  placeholder="Enter category description..."><?php echo htmlspecialchars($category['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Category Image</label>
                        <input type="file" 
                               name="image" 
                               class="form-control bg-light border-0" 
                               accept="image/*"
                               onchange="previewImage(this, 'imagePreview')">
                        <div class="form-text">Max size: 5MB. Formats: JPG, PNG, GIF, WebP</div>
                        
                        <?php if (isset($category['image']) && $category['image']): ?>
                        <div class="mt-3">
                            <img src="<?php echo BASE_URL; ?>/uploads/<?php echo htmlspecialchars($category['image']); ?>" 
                                 id="imagePreview"
                                 class="rounded-3 shadow-sm"
                                 style="max-height: 200px;">
                        </div>
                        <?php else: ?>
                        <div class="mt-3">
                            <img id="imagePreview" class="rounded-3 shadow-sm" style="display: none; max-height: 200px;">
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-bold small text-uppercase text-muted">Status</label>
                        <select name="status" class="form-select form-select-lg bg-light border-0">
                            <option value="active" <?php echo ($category['status'] ?? 'active') == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($category['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-3 mb-5">
                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold shadow-sm">
                    <i class="fas fa-save me-2"></i>Save Category
                </button>
                <a href="categories.php" class="btn btn-light btn-lg px-5 rounded-pill fw-bold text-muted">
                    Cancel
                </a>
            </div>
        </form>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 bg-primary text-white mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Help</h5>
                <p class="small opacity-75 mb-0">Categories help organize your products. Use clear names and descriptions to help customers find what they're looking for.</p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
