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
    $hasQuantity = false;
}
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $location = sanitize($_POST['location'] ?? '');
    // Collect structured specification fields (if provided) and append freeform specs
    $spec_manufacturer = sanitize($_POST['spec_manufacturer'] ?? '');
    $spec_model = sanitize($_POST['spec_model'] ?? '');
    $spec_year = sanitize($_POST['spec_year'] ?? '');
    $spec_condition = sanitize($_POST['spec_condition'] ?? '');
    $spec_wattage = sanitize($_POST['spec_wattage'] ?? '');
    $spec_hours = sanitize($_POST['spec_hours'] ?? '');
    $spec_frequency = sanitize($_POST['spec_frequency'] ?? '');
    $spec_fuel_type = sanitize($_POST['spec_fuel_type'] ?? '');
    $spec_voltage = sanitize($_POST['spec_voltage'] ?? '');

    $spec_lines = [];
    if ($spec_manufacturer !== '') { $spec_lines[] = 'Manufacturer: ' . $spec_manufacturer; }
    if ($spec_model !== '') { $spec_lines[] = 'Model: ' . $spec_model; }
    if ($spec_year !== '') { $spec_lines[] = 'Year of manufacturing: ' . $spec_year; }
    if ($spec_condition !== '') { $spec_lines[] = 'Condition: ' . $spec_condition; }
    // Units available is stored in $quantity (handled below)
    if ($spec_wattage !== '') { $spec_lines[] = 'Wattage: ' . $spec_wattage; }
    if ($spec_hours !== '') { $spec_lines[] = 'Hours: ' . $spec_hours; }
    if ($spec_frequency !== '') { $spec_lines[] = 'Frequency: ' . $spec_frequency; }
    if ($spec_fuel_type !== '') { $spec_lines[] = 'Fuel type: ' . $spec_fuel_type; }
    if ($spec_voltage !== '') { $spec_lines[] = 'Voltage: ' . $spec_voltage; }

    $specifications = implode("\n", $spec_lines);
    $price = floatval($_POST['price']);
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $status = sanitize($_POST['status']);
    $featured = isset($_POST['featured']) ? 1 : 0;
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));
    
    if (empty($name) || $price <= 0) {
        $error = 'Please fill in all required fields';
    } else {
        // Handle main image upload
        $main_image = '';
        
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
            $file_ext = strtolower(pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION));
            
            if (in_array($file_ext, ALLOWED_IMAGE_EXTENSIONS) && $_FILES['main_image']['size'] <= MAX_FILE_SIZE) {
                $new_filename = 'prod_' . time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = PRODUCT_IMAGE_PATH . $new_filename;
                
                if (move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_path)) {
                    $main_image = $new_filename;
                }
            }
        }
        
        // Insert product (include quantity if the column exists)
        if ($hasQuantity) {
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, location, specifications, price, quantity, main_image, status, featured) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $location, $specifications, $price, $quantity, $main_image, $status, $featured]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, location, specifications, price, main_image, status, featured) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $location, $specifications, $price, $main_image, $status, $featured]);
        }
        $product_id = $pdo->lastInsertId();
        
        // Handle gallery images
        if (isset($_FILES['gallery_images'])) {
            $display_order = 1;
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['gallery_images']['error'][$key] == 0) {
                    $file_ext = strtolower(pathinfo($_FILES['gallery_images']['name'][$key], PATHINFO_EXTENSION));
                    
                    if (in_array($file_ext, ALLOWED_IMAGE_EXTENSIONS) && $_FILES['gallery_images']['size'][$key] <= MAX_FILE_SIZE) {
                        $new_filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file_ext;
                        $upload_path = PRODUCT_IMAGE_PATH . $new_filename;
                        
                        if (move_uploaded_file($tmp_name, $upload_path)) {
                            $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, display_order) VALUES (?, ?, ?)");
                            $stmt->execute([$product_id, $new_filename, $display_order]);
                            $display_order++;
                        }
                    }
                }
            }
        }
        
        // Send notification to newsletter subscribers if product is published
        if ($status === 'published') {
            require_once __DIR__ . '/includes/newsletter-notify.php';
            
            $product_data = [
                'id' => $product_id,
                'name' => $name,
                'description' => $specifications,
                'price' => $price,
                'quantity' => $quantity
            ];
            
            $notification_result = notifyNewProduct($product_data);
            
            if ($notification_result && $notification_result['success'] > 0) {
                $success = 'Produit créé et ' . $notification_result['success'] . ' abonné(s) notifié(s) par email !';
            } else {
                $success = 'Produit créé avec succès !';
            }
        } else {
            $success = 'Produit créé avec succès !';
        }
        
        redirect('product-edit.php?id=' . $product_id);
    }
}

$page_title = 'Add New Product';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Add New Product</h4>
            <p class="text-muted small mb-0">Create a new product listing</p>
        </div>
        <a href="products.php" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fas fa-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <div><?php echo htmlspecialchars($error); ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div><?php echo htmlspecialchars($success); ?></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-light border-0 py-2 px-3">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><i class="fas fa-info-circle me-2 text-primary"></i>Basic Information</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted mb-1">Product Name *</label>
                        <input type="text" 
                               name="name" 
                               class="form-control border-1" 
                               placeholder="e.g. Solar Panel 500W"
                               required>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-muted mb-1">Location</label>
                        <input type="text" 
                               name="location" 
                               class="form-control border-1" 
                               placeholder="e.g. Abidjan, Côte d'Ivoire">
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-light border-0 py-2 px-3">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><i class="fas fa-list-ul me-2 text-primary"></i>Specifications</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">Manufacturer</label>
                            <input type="text" name="spec_manufacturer" class="form-control form-control-sm border-1" placeholder="e.g. Jenbacher">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">Model</label>
                            <input type="text" name="spec_model" class="form-control form-control-sm border-1" placeholder="e.g. J320">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Year</label>
                            <input type="text" name="spec_year" class="form-control form-control-sm border-1" placeholder="e.g. 2005">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Condition</label>
                            <input type="text" name="spec_condition" class="form-control form-control-sm border-1" placeholder="New / Used">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Wattage</label>
                            <input type="text" name="spec_wattage" class="form-control form-control-sm border-1" placeholder="e.g. 1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Hours</label>
                            <input type="text" name="spec_hours" class="form-control form-control-sm border-1" placeholder="e.g. 30000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Frequency</label>
                            <input type="text" name="spec_frequency" class="form-control form-control-sm border-1" placeholder="e.g. 50 Hz">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Fuel type</label>
                            <input type="text" name="spec_fuel_type" class="form-control form-control-sm border-1" placeholder="e.g. Diesel">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">Voltage</label>
                            <input type="text" name="spec_voltage" class="form-control form-control-sm border-1" placeholder="e.g. 400">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-light border-0 py-2 px-3">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><i class="fas fa-dollar-sign me-2 text-primary"></i>Pricing & Status</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold small text-muted mb-1">Price *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-1">$</span>
                                <input type="number" 
                                       name="price" 
                                       class="form-control border-1" 
                                       step="0.01" 
                                       min="0" 
                                       placeholder="0.00"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold small text-muted mb-1">Quantity</label>
                            <input type="number" 
                                   name="quantity" 
                                   class="form-control border-1" 
                                   min="0" 
                                   value="0">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold small text-muted mb-1">Status</label>
                            <select name="status" class="form-select border-1">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" 
                               name="featured" 
                               class="form-check-input" 
                               id="featured"
                               role="switch">
                        <label class="form-check-label fw-semibold small" for="featured">
                            <i class="fas fa-star text-warning me-1"></i>Featured Product
                        </label>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-light border-0 py-2 px-3">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><i class="fas fa-images me-2 text-primary"></i>Images</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted mb-2">Main Product Image</label>
                        <div class="position-relative">
                            <div class="border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative" style="min-height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                <div class="mb-2">
                                    <i class="fas fa-camera text-primary" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                </div>
                                <p class="text-muted small mb-1 fw-semibold">Click to upload main image</p>
                                <p class="text-muted" style="font-size: 0.7rem;">Max 5MB • JPG, PNG, GIF, WebP</p>
                                <label for="mainImageInput" class="position-absolute w-100 h-100 top-0 start-0" style="cursor: pointer;"></label>
                            </div>
                            <img id="mainImagePreview" class="rounded-3 border mt-2" style="display: none; max-height: 180px; max-width: 100%;">
                            <input type="file" 
                                   id="mainImageInput"
                                   name="main_image" 
                                   class="d-none" 
                                   accept="image/*"
                                   onchange="previewImage(this, 'mainImagePreview')">
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-muted mb-2">Add Gallery Images</label>
                        <div class="border border-2 border-dashed rounded-3 p-3 text-center bg-light position-relative" style="cursor: pointer;">
                            <i class="fas fa-images text-primary mb-2" style="font-size: 1.5rem; opacity: 0.4;"></i>
                            <p class="text-muted small mb-0 fw-semibold">Click to select multiple images</p>
                            <p class="text-muted mb-0" style="font-size: 0.65rem;">JPG, PNG, GIF, WebP</p>
                            <input type="file" 
                                   id="galleryImageInput"
                                   name="gallery_images[]" 
                                   class="position-absolute w-100 h-100 top-0 start-0 opacity-0" 
                                   style="cursor: pointer;"
                                   accept="image/*"
                                   multiple>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="fas fa-save me-1" style="font-size: 0.75rem;"></i><span style="font-size: 0.85rem;">Create Product</span>
                </button>
                <a href="products.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-times me-1" style="font-size: 0.75rem;"></i><span style="font-size: 0.85rem;">Cancel</span>
                </a>
            </div>
        </form>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 mb-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body p-3">
                <h6 class="mb-3 fw-bold text-white d-flex align-items-center" style="font-size: 0.85rem;">
                    <i class="fas fa-info-circle me-2"></i>Quick Guide
                </h6>
                <p class="text-white opacity-90 mb-3" style="font-size: 0.75rem;">Follow these tips for effective listings</p>
                
                <div class="mb-3">
                    <div class="d-flex align-items-start text-white mb-2">
                        <i class="fas fa-check-circle me-2 mt-1 opacity-75" style="font-size: 0.7rem;"></i>
                        <div>
                            <h6 class="fw-bold mb-1" style="font-size: 0.75rem;">Product Name</h6>
                            <p class="opacity-90 mb-0" style="font-size: 0.7rem;">Use clear, descriptive names with key features</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex align-items-start text-white mb-2">
                        <i class="fas fa-check-circle me-2 mt-1 opacity-75" style="font-size: 0.7rem;"></i>
                        <div>
                            <h6 class="fw-bold mb-1" style="font-size: 0.75rem;">Images</h6>
                            <p class="opacity-90 mb-0" style="font-size: 0.7rem;">High-quality photos with good lighting</p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-0">
                    <div class="d-flex align-items-start text-white">
                        <i class="fas fa-check-circle me-2 mt-1 opacity-75" style="font-size: 0.7rem;"></i>
                        <div>
                            <h6 class="fw-bold mb-1" style="font-size: 0.75rem;">Pricing</h6>
                            <p class="opacity-90 mb-0" style="font-size: 0.7rem;">Set competitive prices (can be updated later)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body p-3">
                <h6 class="fw-bold text-white mb-3 d-flex align-items-center" style="font-size: 0.85rem;">
                    <i class="fas fa-lightbulb me-2"></i>Pro Tips
                </h6>
                <div class="d-flex flex-column gap-2" style="font-size: 0.75rem;">
                    <div class="d-flex align-items-start text-white">
                        <i class="fas fa-star me-2 mt-1 opacity-75" style="font-size: 0.7rem;"></i>
                        <span class="opacity-90">Featured products get more visibility</span>
                    </div>
                    <div class="d-flex align-items-start text-white">
                        <i class="fas fa-star me-2 mt-1 opacity-75" style="font-size: 0.7rem;"></i>
                        <span class="opacity-90">Complete all specifications for better SEO</span>
                    </div>
                    <div class="d-flex align-items-start text-white">
                        <i class="fas fa-star me-2 mt-1 opacity-75" style="font-size: 0.7rem;"></i>
                        <span class="opacity-90">Upload multiple gallery images</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
