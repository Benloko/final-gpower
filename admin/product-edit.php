<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$pdo = getPDOConnection();

$product = null;
$error = '';
$success = '';

// Get product for edit
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
                                                                                                                                                // Check if products table has a 'quantity' column
    $hasQuantity = false;
    try {
        $colStmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'products' AND COLUMN_NAME = 'quantity'");
        $colStmt->execute([DB_NAME]);
        $hasQuantity = $colStmt->fetch()['cnt'] > 0;
    } catch (Exception $e) {
        $hasQuantity = false;
    }

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        redirect('products.php');
    }

    // Parse existing specifications into structured fields for pre-filling
    $spec_text = $product['specifications'] ?? '';
    $spec_parsed = [
        'manufacturer' => '', 'model' => '', 'year' => '', 'condition' => '',
        'wattage' => '', 'hours' => '', 'frequency' => '', 'fuel_type' => '', 'voltage' => ''
    ];
    if (!empty($spec_text)) {
        foreach (explode("\n", $spec_text) as $line) {
            $line = trim($line);
            if (stripos($line, 'Manufacturer:') === 0) {
                $spec_parsed['manufacturer'] = trim(substr($line, strlen('Manufacturer:')));
            } elseif (stripos($line, 'Model:') === 0) {
                $spec_parsed['model'] = trim(substr($line, strlen('Model:')));
            } elseif (stripos($line, 'Year of manufacturing:') === 0) {
                $spec_parsed['year'] = trim(substr($line, strlen('Year of manufacturing:')));
            } elseif (stripos($line, 'Condition:') === 0) {
                $spec_parsed['condition'] = trim(substr($line, strlen('Condition:')));
            } elseif (stripos($line, 'Wattage:') === 0) {
                $spec_parsed['wattage'] = trim(substr($line, strlen('Wattage:')));
            } elseif (stripos($line, 'Hours:') === 0) {
                $spec_parsed['hours'] = trim(substr($line, strlen('Hours:')));
            } elseif (stripos($line, 'Frequency:') === 0) {
                $spec_parsed['frequency'] = trim(substr($line, strlen('Frequency:')));
            } elseif (stripos($line, 'Fuel type:') === 0) {
                $spec_parsed['fuel_type'] = trim(substr($line, strlen('Fuel type:')));
            } elseif (stripos($line, 'Voltage:') === 0) {
                $spec_parsed['voltage'] = trim(substr($line, strlen('Voltage:')));
            }
        }
    }
    
    // Get product images
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order");
    $stmt->execute([$id]);
    $product_images = $stmt->fetchAll();

    // No server-side translations are loaded here — admin manages content in English only.

} else {
    redirect('products.php');
}

// Handle delete gallery image
if (isset($_GET['delete_image'])) {
    $image_id = (int)$_GET['delete_image'];
    $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE id = ? AND product_id = ?");
    $stmt->execute([$image_id, $product['id']]);
    $img = $stmt->fetch();
    
    if ($img) {
        $image_path = PRODUCT_IMAGE_PATH . $img['image_path'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        $stmt = $pdo->prepare("DELETE FROM product_images WHERE id = ?");
        $stmt->execute([$image_id]);
        redirect('product-edit.php?id=' . $product['id']);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $identification_number = sanitize($_POST['identification_number'] ?? '');
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
        // Ensure PDF upload directory exists
        if (!is_dir(PRODUCT_PDF_PATH)) {
            @mkdir(PRODUCT_PDF_PATH, 0755, true);
        }

        // Handle main image upload
        $main_image = $product['main_image'];
        
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
            $file_ext = strtolower(pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION));
            
            if (in_array($file_ext, ALLOWED_IMAGE_EXTENSIONS) && $_FILES['main_image']['size'] <= MAX_FILE_SIZE) {
                $new_filename = 'prod_' . time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = PRODUCT_IMAGE_PATH . $new_filename;
                
                if (move_uploaded_file($_FILES['main_image']['tmp_name'], $upload_path)) {
                    // Delete old image
                    if ($main_image && file_exists(PRODUCT_IMAGE_PATH . $main_image)) {
                        unlink(PRODUCT_IMAGE_PATH . $main_image);
                    }
                    $main_image = $new_filename;
                }
            }
        }
        
        // Handle PDF upload
        $pdf_file = $product['pdf_file'] ?? '';
        
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
            $file_ext = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
            
            if (in_array($file_ext, ALLOWED_PDF_EXTENSIONS) && $_FILES['pdf_file']['size'] <= MAX_PDF_FILE_SIZE) {
                $new_filename = 'prod_' . time() . '_' . uniqid() . '.' . $file_ext;
                $upload_path = PRODUCT_PDF_PATH . $new_filename;
                
                if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $upload_path)) {
                    // Delete old PDF
                    if ($pdf_file && file_exists(PRODUCT_PDF_PATH . $pdf_file)) {
                        unlink(PRODUCT_PDF_PATH . $pdf_file);
                    }
                    $pdf_file = $new_filename;
                }
            }
        }
        
        // Update product (include quantity and pdf_file if present in schema)
        if (!empty($hasQuantity)) {
            $stmt = $pdo->prepare("UPDATE products 
                                   SET name = ?, identification_number = ?, slug = ?, location = ?, specifications = ?, 
                                       price = ?, quantity = ?, main_image = ?, pdf_file = ?, status = ?, featured = ? 
                                   WHERE id = ?");
            $stmt->execute([$name, $identification_number, $slug, $location, $specifications, $price, $quantity, $main_image, $pdf_file, $status, $featured, $product['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE products 
                                   SET name = ?, identification_number = ?, slug = ?, location = ?, specifications = ?, 
                                       price = ?, main_image = ?, pdf_file = ?, status = ?, featured = ? 
                                   WHERE id = ?");
            $stmt->execute([$name, $identification_number, $slug, $location, $specifications, $price, $main_image, $pdf_file, $status, $featured, $product['id']]);
        }
        
        // Handle new gallery images
        if (isset($_FILES['gallery_images'])) {
            // Get max display order
            $stmt = $pdo->prepare("SELECT MAX(display_order) as max_order FROM product_images WHERE product_id = ?");
            $stmt->execute([$product['id']]);
            $display_order = ($stmt->fetch()['max_order'] ?? 0) + 1;
            
            foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['gallery_images']['error'][$key] == 0) {
                    $file_ext = strtolower(pathinfo($_FILES['gallery_images']['name'][$key], PATHINFO_EXTENSION));
                    
                    if (in_array($file_ext, ALLOWED_IMAGE_EXTENSIONS) && $_FILES['gallery_images']['size'][$key] <= MAX_FILE_SIZE) {
                        $new_filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file_ext;
                        $upload_path = PRODUCT_IMAGE_PATH . $new_filename;
                        
                        if (move_uploaded_file($tmp_name, $upload_path)) {
                            $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, display_order) VALUES (?, ?, ?)");
                            $stmt->execute([$product['id'], $new_filename, $display_order]);
                            $display_order++;
                        }
                    }
                }
            }
        }
        
        // Send notification if product status changed to published and was not published before
        $was_published = $product['status'] === 'published';
        $now_published = $status === 'published';
        
        if ($now_published && !$was_published) {
            require_once __DIR__ . '/includes/newsletter-notify.php';
            
            $product_data = [
                'id' => $product['id'],
                'name' => $name,
                'description' => $specifications,
                'price' => $price,
                'quantity' => $quantity
            ];
            
            $notification_result = notifyNewProduct($product_data);
            
            if ($notification_result && $notification_result['success'] > 0) {
                $success = 'Product published and ' . $notification_result['success'] . ' subscriber(s) notified by email!';
            } else {
                $success = 'Product updated successfully!';
            }
        } else {
            $success = 'Product updated successfully!';
        }
        
        // Refresh product data
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product['id']]);
        $product = $stmt->fetch();
        
        $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order");
        $stmt->execute([$product['id']]);
        $product_images = $stmt->fetchAll();
    }
}

// Handle saving translations from admin form (if present)
    // Translations are not saved server-side — admin provides English content only.

$page_title = 'Edit Product';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1 fw-bold text-dark">Edit Product</h4>
            <p class="text-muted small mb-0">Update product information and details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" 
               class="btn btn-outline-info btn-sm rounded-pill"
               target="_blank">
                <i class="fas fa-eye me-1"></i>Preview
            </a>
            <a href="products.php" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>
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
                               value="<?php echo htmlspecialchars($product['name']); ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted mb-1">
                            <i class="fas fa-barcode text-secondary me-1"></i>Product Identification Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-1">
                                <i class="fas fa-hashtag text-secondary"></i>
                            </span>
                            <input type="text" 
                                   name="identification_number" 
                                   class="form-control border-1" 
                                   value="<?php echo htmlspecialchars($product['identification_number'] ?? ''); ?>"
                                   placeholder="e.g. SN-2025-001, MODEL-XYZ-789">
                        </div>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>Unique serial, model code, or product ID
                        </small>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-muted mb-1">Location</label>
                        <input type="text" 
                               name="location" 
                               class="form-control border-1" 
                               value="<?php echo htmlspecialchars($product['location'] ?? ''); ?>"
                                   placeholder="e.g. Abidjan, Ivory Coast">
                    </div>
                </div>
            </div>

            <!-- Translations removed — admin edits are in English only -->

            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-light border-0 py-2 px-3">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><i class="fas fa-list-ul me-2 text-primary"></i>Specifications</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">Manufacturer</label>
                            <input type="text" name="spec_manufacturer" class="form-control form-control-sm border-1" value="<?php echo htmlspecialchars($spec_parsed['manufacturer']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">Model</label>
                            <input type="text" name="spec_model" class="form-control form-control-sm border-1" value="<?php echo htmlspecialchars($spec_parsed['model']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Year</label>
                            <input type="text" name="spec_year" class="form-control form-control-sm border-1" value="<?php echo htmlspecialchars($spec_parsed['year']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Condition</label>
                            <input type="text" name="spec_condition" class="form-control form-control-sm border-1" value="<?php echo htmlspecialchars($spec_parsed['condition']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Wattage</label>
                            <input type="text" name="spec_wattage" class="form-control form-control-sm border-1" value="<?php echo htmlspecialchars($spec_parsed['wattage']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Hours</label>
                            <input type="text" name="spec_hours" class="form-control form-control-sm border-1" value="<?php echo htmlspecialchars($spec_parsed['hours']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Frequency</label>
                            <input type="text" name="spec_frequency" class="form-control form-control-sm border-1" value="<?php echo htmlspecialchars($spec_parsed['frequency']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small text-muted mb-1">Fuel type</label>
                            <input type="text" name="spec_fuel_type" class="form-control form-control-sm border-1" value="<?php echo htmlspecialchars($spec_parsed['fuel_type']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small text-muted mb-1">Voltage</label>
                            <input type="text" name="spec_voltage" class="form-control form-control-sm border-1" value="<?php echo htmlspecialchars($spec_parsed['voltage']); ?>">
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
                                       value="<?php echo $product['price']; ?>"
                                       required>
                            </div>
                        </div>

                        <?php if (!empty($hasQuantity)): ?>
                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold small text-muted mb-1">Quantity</label>
                            <input type="number" 
                                   name="quantity" 
                                   class="form-control border-1" 
                                   min="0"
                                   value="<?php echo isset($product['quantity']) ? (int)$product['quantity'] : 0; ?>"
                                   placeholder="0">
                        </div>
                        <?php endif; ?>

                        <div class="col-md-4 mb-2">
                            <label class="form-label fw-semibold small text-muted mb-1">Status</label>
                            <select name="status" class="form-select border-1">
                                <option value="active" <?php echo $product['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $product['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" 
                               name="featured" 
                               class="form-check-input" 
                               id="featured"
                               role="switch"
                               <?php echo $product['featured'] ? 'checked' : ''; ?>>
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
                            <?php if ($product['main_image']): ?>
                            <div class="position-relative d-inline-block">
                                <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['main_image']); ?>" 
                                     id="mainImagePreview"
                                     class="rounded-3 border"
                                     style="max-height: 180px; max-width: 100%;">
                                <label for="mainImageInput" class="position-absolute bottom-0 end-0 m-2 btn btn-primary btn-sm rounded-circle shadow" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                    <i class="fas fa-camera" style="font-size: 0.85rem;"></i>
                                </label>
                            </div>
                            <?php else: ?>
                            <div class="border border-2 border-dashed rounded-3 p-4 text-center bg-light position-relative" style="min-height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                <div class="mb-2">
                                    <i class="fas fa-camera text-primary" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                </div>
                                <p class="text-muted small mb-1 fw-semibold">Click to upload main image</p>
                                <p class="text-muted" style="font-size: 0.7rem;">Max 5MB • JPG, PNG, GIF, WebP</p>
                                <label for="mainImageInput" class="position-absolute w-100 h-100 top-0 start-0" style="cursor: pointer;"></label>
                            </div>
                            <img id="mainImagePreview" class="rounded-3 border mt-2" style="display: none; max-height: 180px; max-width: 100%;">
                            <?php endif; ?>
                            <input type="file" 
                                   id="mainImageInput"
                                   name="main_image" 
                                   class="d-none" 
                                   accept="image/*"
                                   onchange="previewImage(this, 'mainImagePreview')">
                        </div>
                    </div>
                    
                    <div class="mb-3">
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
                    
                    <?php if (!empty($product_images)): ?>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-muted mb-2">Current Gallery (<?php echo count($product_images); ?>)</label>
                        <div class="row g-2">
                            <?php foreach ($product_images as $img): ?>
                            <div class="col-4 col-md-3">
                                <div class="position-relative">
                                    <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo htmlspecialchars($img['image_path']); ?>" 
                                         class="w-100 rounded-2 border"
                                         style="height: 100px; object-fit: cover;">
                                    <a href="#" 
                                       class="btn btn-danger position-absolute top-0 end-0 m-1 rounded-circle shadow-sm delete-image-btn"
                                       style="width: 24px; height: 24px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 0.65rem;"
                                       data-image-id="<?php echo $img['id']; ?>"
                                       data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-light border-0 py-2 px-3">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;"><i class="fas fa-file-pdf me-2 text-danger"></i>Documents & Inventory</h6>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted mb-2">
                            <i class="fas fa-file-pdf text-danger me-1"></i>Product Details PDF (Datasheet, Specs, Manual)
                        </label>
                        <?php if ($product['pdf_file'] ?? false): ?>
                        <div class="mb-2 p-2 bg-light rounded-2 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <span class="fw-semibold small"><?php echo htmlspecialchars($product['pdf_file']); ?></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" onclick="document.getElementById('pdfDeleteBtn').click();" style="width: 28px; height: 28px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-trash" style="font-size: 0.6rem;"></i>
                            </button>
                            <input type="hidden" id="pdfDeleteBtn" name="delete_pdf" value="0">
                        </div>
                        <small class="text-muted d-block mb-2">
                            <i class="fas fa-check-circle text-success me-1"></i>Current PDF attached
                        </small>
                        <?php endif; ?>
                        <div class="border border-2 border-dashed rounded-3 p-3 text-center bg-light position-relative <?php echo ($product['pdf_file'] ?? false) ? 'opacity-75' : ''; ?>" style="cursor: pointer;">
                            <i class="fas fa-file-pdf text-danger mb-2" style="font-size: 1.5rem; opacity: 0.4;"></i>
                            <p class="text-muted small mb-0 fw-semibold">Click to upload/replace PDF</p>
                            <p class="text-muted mb-0" style="font-size: 0.65rem;">Max 10MB • PDF only</p>
                            <input type="file" 
                                   id="pdfInput"
                                   name="pdf_file" 
                                   class="position-absolute w-100 h-100 top-0 start-0 opacity-0" 
                                   style="cursor: pointer;"
                                   accept=".pdf">
                            <div id="pdfFileName" class="text-success fw-semibold mt-2" style="font-size: 0.85rem; display: none;"></div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold small text-muted mb-1">
                            <i class="fas fa-cubes text-info me-1"></i>Quantity in Stock
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-1">
                                <i class="fas fa-layer-group text-info"></i>
                            </span>
                            <input type="number" 
                                   name="quantity" 
                                   class="form-control border-1" 
                                   min="0" 
                                   value="<?php echo intval($product['quantity'] ?? 0); ?>"
                                   placeholder="Units available">
                        </div>
                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-info-circle me-1"></i>Display stock status on product page
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="fas fa-save me-1" style="font-size: 0.75rem;"></i><span style="font-size: 0.85rem;">Update Product</span>
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
                    <i class="fas fa-info-circle me-2"></i>Product Info
                </h6>
                <div class="d-flex flex-column gap-2" style="font-size: 0.8rem;">
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span class="text-white opacity-75"><i class="fas fa-calendar-plus me-2" style="font-size: 0.7rem;"></i>Created</span>
                        <span class="fw-semibold text-white"><?php echo date('M d, Y', strtotime($product['created_at'])); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span class="text-white opacity-75"><i class="fas fa-clock me-2" style="font-size: 0.7rem;"></i>Updated</span>
                        <span class="fw-semibold text-white"><?php echo date('M d, Y', strtotime($product['updated_at'])); ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span class="text-white opacity-75"><i class="fas fa-hashtag me-2" style="font-size: 0.7rem;"></i>ID</span>
                        <span class="fw-semibold text-white"><?php echo $product['id']; ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <span class="text-white opacity-75"><i class="fas fa-link me-2" style="font-size: 0.7rem;"></i>Slug</span>
                        <span class="fw-semibold text-white text-truncate" style="max-width: 130px;" title="<?php echo htmlspecialchars($product['slug']); ?>"><?php echo htmlspecialchars($product['slug']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm rounded-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div class="card-body p-3">
                <h6 class="fw-bold text-white mb-3 d-flex align-items-center" style="font-size: 0.85rem;">
                    <i class="fas fa-lightbulb me-2"></i>Quick Tips
                </h6>
                <div class="d-flex flex-column gap-2" style="font-size: 0.75rem;">
                    <div class="d-flex align-items-start text-white">
                        <i class="fas fa-check-circle me-2 mt-1 opacity-75" style="font-size: 0.7rem;"></i>
                        <span class="opacity-90">Use high-quality images for best results</span>
                    </div>
                    <div class="d-flex align-items-start text-white">
                        <i class="fas fa-check-circle me-2 mt-1 opacity-75" style="font-size: 0.7rem;"></i>
                        <span class="opacity-90">Complete all specifications accurately</span>
                    </div>
                    <div class="d-flex align-items-start text-white">
                        <i class="fas fa-check-circle me-2 mt-1 opacity-75" style="font-size: 0.7rem;"></i>
                        <span class="opacity-90">Feature products to boost visibility</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Image Confirmation Modal -->
<div class="modal fade" id="deleteImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-image text-danger" style="font-size: 1.2rem;"></i>
                    </div>
                </div>
                <h6 class="fw-bold mb-2">Delete this image?</h6>
                <p class="text-muted small mb-0">This action is irreversible.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4 pt-0">
                <button type="button" class="btn btn-light btn-sm rounded-pill px-3" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1" style="font-size: 0.7rem;"></i><span style="font-size: 0.8rem;">Cancel</span>
                </button>
                <a href="#" id="confirmDeleteImageBtn" class="btn btn-danger btn-sm rounded-pill px-3">
                    <i class="fas fa-trash me-1" style="font-size: 0.7rem;"></i><span style="font-size: 0.8rem;">Delete</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Delete image modal - wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    const deleteImageModalEl = document.getElementById('deleteImageModal');
    if (deleteImageModalEl) {
        const deleteImageModal = new bootstrap.Modal(deleteImageModalEl);
        const deleteImageButtons = document.querySelectorAll('.delete-image-btn');

        deleteImageButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const imageId = this.getAttribute('data-image-id');
                const productId = this.getAttribute('data-product-id');
                
                document.getElementById('confirmDeleteImageBtn').href = '?id=' + productId + '&delete_image=' + imageId;
                
                deleteImageModal.show();
            });
        });
    }
});

// Handle PDF file display
document.getElementById('pdfInput').addEventListener('change', function(e) {
    const fileName = this.files[0]?.name || '';
    const pdfFileNameDiv = document.getElementById('pdfFileName');
    
    if (fileName) {
        pdfFileNameDiv.textContent = '✓ ' + fileName;
        pdfFileNameDiv.style.display = 'block';
    } else {
        pdfFileNameDiv.style.display = 'none';
    }
});

function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(previewId).src = e.target.result;
            document.getElementById(previewId).style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
