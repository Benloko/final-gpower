<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$page_title = 'Industrial Power Equipment & Heavy Machinery Supplier';
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
$query = "SELECT p.* FROM products p WHERE p.status = 'active'" . $search_filter . " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$params[] = ITEMS_PER_PAGE;
$params[] = $offset;
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!-- Clean Executive Hero Banner -->
<section class="hero-section text-white py-5 position-relative overflow-hidden" 
         style="background: linear-gradient(180deg, rgba(7, 14, 28, 0.88) 0%, rgba(11, 22, 45, 0.94) 100%), url('<?php echo BASE_URL; ?>/assets/images/hero-bg.jpeg') center/cover no-repeat; min-height: 440px;">
    
    <div class="container py-5 position-relative text-center" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <!-- Badge -->
                <div class="d-inline-flex align-items-center gap-2 px-3.5 py-1.5 rounded-pill bg-dark bg-opacity-60 border border-info border-opacity-30 text-info mb-3 backdrop-blur shadow-sm" style="font-size: 0.78rem; font-weight: 700; letter-spacing: 0.5px;">
                    <i class="fas fa-bolt text-warning"></i> GLOBAL INDUSTRIAL POWER GENERATORS & TURBINES SUPPLIER
                </div>

                <!-- Main Title -->
                <h1 class="display-6 fw-extrabold text-white mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.6px; line-height: 1.2;">
                    Industrial Power Equipment & Heavy Machinery
                </h1>

                <p class="lead text-white-50 mb-4 mx-auto" style="max-width: 680px; font-size: 0.98rem; line-height: 1.6;">
                    We inspect, supply, and export high-capacity diesel & gas generators, industrial turbines, and power plant equipment worldwide.
                </p>

                <!-- Hero Action Buttons (Détachés avec espacement forcé de 24px) -->
                <div class="d-flex flex-wrap align-items-center justify-content-center mt-3.5" style="gap: 1.5rem !important;">
                    <a href="#products" class="btn btn-primary rounded-pill px-4 py-2.5 fw-semibold shadow-sm text-nowrap" style="font-size: 0.85rem;">
                        <i class="fas fa-boxes me-2 fs-7"></i> Browse Catalog
                    </a>
                    <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>" target="_blank" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm text-nowrap" style="font-size: 0.85rem;">
                        <i class="fab fa-whatsapp me-2 fs-6"></i> Request Quote
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Executive Key Industrial Metrics / Statistics Counter (Sous la photo de fond) -->
<section class="py-3.5 bg-white border-bottom border-light shadow-sm">
    <div class="container py-1">
        <div class="row row-cols-2 row-cols-lg-4 g-4 text-center text-lg-start">
            
            <!-- Stat 1 -->
            <div class="col border-end-lg border-light">
                <div class="d-flex flex-column flex-lg-row align-items-center align-items-lg-start gap-2.5 p-1">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0 mb-1 mb-lg-0" style="width: 44px; height: 44px;">
                        <i class="fas fa-boxes fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-extrabold text-dark tracking-tight mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.1;">
                            500<span class="text-primary">+</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-0.5 mt-0.5" style="font-size: 0.86rem; font-family: 'Plus Jakarta Sans', sans-serif;">Heavy Power Units</h6>
                        <span class="text-muted small d-block" style="font-size: 0.74rem;">Generators & Turbines Supplied</span>
                    </div>
                </div>
            </div>

            <!-- Stat 2 -->
            <div class="col border-end-lg border-light">
                <div class="d-flex flex-column flex-lg-row align-items-center align-items-lg-start gap-2.5 p-1">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center flex-shrink-0 mb-1 mb-lg-0" style="width: 44px; height: 44px;">
                        <i class="fas fa-bolt fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-extrabold text-dark tracking-tight mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.1;">
                            1.8 <span class="text-warning">GW+</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-0.5 mt-0.5" style="font-size: 0.86rem; font-family: 'Plus Jakarta Sans', sans-serif;">Capacity Exported</h6>
                        <span class="text-muted small d-block" style="font-size: 0.74rem;">Global Energy Output</span>
                    </div>
                </div>
            </div>

            <!-- Stat 3 -->
            <div class="col border-end-lg border-light">
                <div class="d-flex flex-column flex-lg-row align-items-center align-items-lg-start gap-2.5 p-1">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center flex-shrink-0 mb-1 mb-lg-0" style="width: 44px; height: 44px;">
                        <i class="fas fa-globe-americas fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-extrabold text-dark tracking-tight mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.1;">
                            45<span class="text-info">+</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-0.5 mt-0.5" style="font-size: 0.86rem; font-family: 'Plus Jakarta Sans', sans-serif;">Export Countries</h6>
                        <span class="text-muted small d-block" style="font-size: 0.74rem;">Worldwide Freight Logistics</span>
                    </div>
                </div>
            </div>

            <!-- Stat 4 -->
            <div class="col">
                <div class="d-flex flex-column flex-lg-row align-items-center align-items-lg-start gap-2.5 p-1">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center flex-shrink-0 mb-1 mb-lg-0" style="width: 44px; height: 44px;">
                        <i class="fas fa-shield-alt fs-5"></i>
                    </div>
                    <div>
                        <div class="fs-2 fw-extrabold text-dark tracking-tight mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; line-height: 1.1;">
                            100<span class="text-success">%</span>
                        </div>
                        <h6 class="fw-bold text-dark mb-0.5 mt-0.5" style="font-size: 0.86rem; font-family: 'Plus Jakarta Sans', sans-serif;">Load Bank Tested</h6>
                        <span class="text-muted small d-block" style="font-size: 0.74rem;">Certified Inspection Guaranteed</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Products Section (Sleek Compact Rounded Search Bar + Product Cards Grid) -->
<section class="py-5 bg-light position-relative" id="products">
    <div class="container">
        
        <!-- Section Header & Compact Rounded Search Bar -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom border-secondary border-opacity-10 gap-3">
            <div>
                <h4 class="fw-extrabold text-dark mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    Heavy Machinery & Power Listings
                </h4>
                <p class="text-muted mb-0 small">
                    Showing <?php echo count($products); ?> of <?php echo $total_products; ?> certified heavy power listings.
                </p>
            </div>

            <!-- Compact & Beautiful Rounded Search Bar -->
            <div class="w-100" style="max-width: 420px;">
                <form action="#products" method="GET" class="m-0">
                    <div class="input-group bg-white rounded-pill p-1 shadow-sm border border-secondary border-opacity-25" style="transition: border-color 0.2s ease;">
                        <span class="input-group-text bg-transparent border-0 ps-3 text-muted" style="font-size: 0.9rem;">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 shadow-none bg-transparent text-dark ps-2" 
                               placeholder="Search equipment..." 
                               value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                               style="height: 36px; font-size: 0.88rem;">
                        <button type="submit" class="btn btn-primary rounded-pill px-3.5 fw-semibold" style="height: 36px; font-size: 0.84rem;">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
            <div class="mb-4 d-flex align-items-center justify-content-between bg-white p-3 rounded-3 shadow-sm border border-light">
                <span class="text-dark small fw-semibold">
                    <i class="fas fa-info-circle text-primary me-2"></i> Search results for "<strong><?php echo htmlspecialchars($_GET['search']); ?></strong>"
                </span>
                <a href="<?php echo BASE_URL; ?>/#products" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 0.8rem;">
                    <i class="fas fa-times me-1"></i> Clear Search
                </a>
            </div>
        <?php endif; ?>

        <!-- Products Grid -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 gx-3.5 gy-4.5" id="products-grid">
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
                                     class="w-100" style="height: 175px; object-fit: cover; transition: transform 0.4s ease;">
                            <?php else: ?>
                                <?php echo product_image_placeholder('175px', htmlspecialchars($product['name'])); ?>
                            <?php endif; ?>
                        </a>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <!-- Title -->
                            <h6 class="product-title fw-bold text-dark text-truncate mb-2" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                                <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $product['id']; ?>" class="text-dark text-decoration-none hover-primary">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h6>

                            <!-- Location & Status -->
                            <div class="d-flex align-items-center gap-2 mb-2 text-muted small product-status-line">
                                <span class="text-danger fw-semibold" style="font-size: 0.78rem;">
                                    <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($product['location'] ?? 'Global Depot'); ?>
                                </span>
                                <span class="text-muted">•</span>
                                <span class="text-success fw-semibold" style="font-size: 0.78rem;">
                                    <i class="fas fa-check-circle me-1"></i>Ready to Export
                                </span>
                            </div>
                        </div>

                        <!-- Price & Animated Pulsing WhatsApp Button -->
                        <div class="pt-2.5 border-top border-light d-flex align-items-center justify-content-between mt-2">
                            <div>
                                <div class="text-uppercase text-muted" style="font-size: 0.62rem; font-weight: 700; letter-spacing: 0.5px;">ESTIMATED PRICE</div>
                                <div class="product-price fw-extrabold text-primary" style="font-size: 1.05rem;">
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
            <p class="text-muted small mb-3">We couldn't find any equipment matching your criteria.</p>
            <?php if(isset($_GET['search'])): ?>
                <a href="<?php echo BASE_URL; ?>/#products" class="btn btn-sm btn-primary rounded-pill px-4">View All Products</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="d-flex justify-content-center align-items-center gap-2 mt-5">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>#products" 
                   class="btn btn-outline-primary rounded-pill px-4 py-2 fw-semibold">
                    <i class="fas fa-arrow-left me-2"></i> Previous
                </a>
            <?php endif; ?>

            <span class="text-muted px-3 small fw-semibold">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>#products" 
                   class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                    Next Page <i class="fas fa-arrow-right ms-2"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Call To Action Section (Executive Modern CTA) -->
<section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(180deg, #070e1c 0%, #0b162d 100%);">
    <div class="container text-center py-3 position-relative" style="z-index: 2;">
        <div class="d-inline-flex align-items-center gap-2 px-3.5 py-1.5 rounded-pill bg-primary bg-opacity-15 border border-primary border-opacity-30 text-primary mb-3 shadow-sm" style="font-size: 0.76rem; font-weight: 700; letter-spacing: 0.6px;">
            <i class="fas fa-headset text-primary"></i> INDUSTRIAL CONSULTATION & LOGISTICS
        </div>
        
        <h3 class="fw-extrabold mb-2 text-white" style="font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.5px;">
            Planning a Heavy Power Project or Need Equipment Specs?
        </h3>
        
        <p class="text-white-50 mb-4 mx-auto" style="max-width: 650px; font-size: 0.95rem; line-height: 1.6;">
            Our heavy machinery specialists assist with load calculations, technical inspection reports, and worldwide ocean freight shipping.
        </p>

        <!-- Executive Rounded Pill Action Buttons -->
        <div class="d-flex flex-wrap justify-content-center align-items-center mt-3" style="gap: 1.5rem !important;">
            <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>" target="_blank" class="btn btn-success rounded-pill px-4 py-2.5 fw-semibold shadow-sm text-nowrap" style="font-size: 0.85rem;">
                <i class="fab fa-whatsapp me-2 fs-6"></i> Speak with Specialist
            </a>
            <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-outline-light rounded-pill px-4 py-2.5 fw-semibold text-nowrap" style="font-size: 0.85rem;">
                <i class="fas fa-paper-plane me-2"></i> Send Technical Inquiry
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
