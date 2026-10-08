<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$page_title = 'Industrial Power Equipment & Heavy Machinery Supplier';
require_once __DIR__ . '/includes/header.php';

// Get active products with existing local photos for the photo slider
$stmt = $pdo->query("SELECT id, name, price, location, main_image FROM products WHERE status = 'active' AND main_image IS NOT NULL AND main_image != '' ORDER BY id DESC");
$raw_prods = $stmt->fetchAll();
$slider_products = [];
foreach ($raw_prods as $p) {
    if (product_image_url($p['main_image'])) {
        $slider_products[] = $p;
    }
}
$slider_products = array_slice($slider_products, 0, 8);
?>

<!-- Clean Executive Hero Banner (Full Screen Viewport) -->
<section class="hero-section hero-fullscreen text-white position-relative overflow-hidden" 
         style="background: linear-gradient(180deg, rgba(5, 12, 26, 0.36) 0%, rgba(7, 18, 38, 0.54) 100%), url('<?php echo BASE_URL; ?>/assets/images/hero-bg.jpeg') center/cover no-repeat;">
    
    <div class="container py-4 position-relative text-center my-auto" style="z-index: 2;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <!-- Main Title -->
                <h1 class="display-5 display-md-4 fw-extrabold text-white mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.8px; line-height: 1.15; text-shadow: 0 2px 16px rgba(0, 0, 0, 0.9);">
                    Industrial Power Equipment & Heavy Machinery
                </h1>

                <p class="lead text-white mb-4.5 mx-auto" style="max-width: 720px; font-size: 1.05rem; line-height: 1.7; text-shadow: 0 2px 12px rgba(0, 0, 0, 0.9); opacity: 0.95;">
                    We inspect, supply, and export high-capacity diesel & gas generators, industrial turbines, and power plant equipment worldwide.
                </p>

                <!-- Hero Action Button (Bouton seul et légèrement poussé vers le bas) -->
                <div class="d-flex justify-content-center mt-4 pt-2">
                    <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-primary rounded-3 px-5 py-2.5 fw-semibold shadow-sm text-nowrap" style="font-size: 0.92rem;">
                        <i class="fas fa-boxes me-2"></i> Browse Catalog
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Down Hint Indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x mb-3.5 text-center z-2 d-none d-sm-block">
        <a href="#stats" class="text-white-50 text-decoration-none hero-scroll-indicator d-flex flex-column align-items-center gap-1" style="transition: color 0.2s ease;">
            <span class="text-uppercase fw-semibold" style="font-size: 0.7rem; letter-spacing: 1.5px; opacity: 0.85;">Discover More</span>
            <i class="fas fa-chevron-down animate-bounce" style="font-size: 0.8rem;"></i>
        </a>
    </div>
</section>

<!-- Key Industrial Metrics (Juste après la photo d'accueil, sobre, compact et pro) -->
<section id="stats" class="py-3 bg-white border-bottom border-light shadow-xs">
    <div class="container py-1">
        <div class="row row-cols-2 row-cols-lg-4 g-3 text-center align-items-center">
            
            <div class="col border-end-lg border-light py-1">
                <div class="fw-extrabold text-primary mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.65rem; line-height: 1.2;">
                    500<span class="text-primary">+</span>
                </div>
                <div class="fw-semibold text-dark" style="font-size: 0.82rem;">Heavy Power Units</div>
                <div class="text-muted" style="font-size: 0.72rem;">Generators & Turbines</div>
            </div>

            <div class="col border-end-lg border-light py-1">
                <div class="fw-extrabold text-warning mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.65rem; line-height: 1.2;">
                    1.8 <span class="text-warning">GW+</span>
                </div>
                <div class="fw-semibold text-dark" style="font-size: 0.82rem;">Capacity Exported</div>
                <div class="text-muted" style="font-size: 0.72rem;">Global Energy Output</div>
            </div>

            <div class="col border-end-lg border-light py-1">
                <div class="fw-extrabold text-info mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.65rem; line-height: 1.2;">
                    45<span class="text-info">+</span>
                </div>
                <div class="fw-semibold text-dark" style="font-size: 0.82rem;">Export Countries</div>
                <div class="text-muted" style="font-size: 0.72rem;">Worldwide Ocean Freight</div>
            </div>

            <div class="col py-1">
                <div class="fw-extrabold text-success mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.65rem; line-height: 1.2;">
                    100<span class="text-success">%</span>
                </div>
                <div class="fw-semibold text-dark" style="font-size: 0.82rem;">Load Bank Tested</div>
                <div class="text-muted" style="font-size: 0.72rem;">Inspection Guaranteed</div>
            </div>

        </div>
    </div>
</section>

<!-- Simple, Elegant Presentation Section (Sans "ABOUT GPOWER", titre court, bouton À propos, radius diminué) -->
<section id="about" class="py-5 bg-white">
    <div class="container py-4">
        <div class="row align-items-center justify-content-between g-5">
            <!-- Left: Presentation -->
            <div class="col-lg-7">
                <h2 class="fw-extrabold text-dark mb-3" style="font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.5px; font-size: 2.1rem; line-height: 1.25;">
                    Heavy Power Solutions
                </h2>
                <p class="text-muted lead mb-3" style="font-size: 1.05rem; line-height: 1.7;">
                    Gpower specializes in the supply, technical inspection, and worldwide export of high-power diesel generators, industrial gas turbines, and heavy engines.
                </p>
                <p class="text-secondary small mb-4" style="line-height: 1.8; font-size: 0.92rem;">
                    We supply certified machinery from leading manufacturers including Caterpillar, Cummins, MTU, MWM, and Jenbacher. Every equipment unit is verified for operational integrity before ocean freight dispatch.
                </p>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <a href="<?php echo BASE_URL; ?>/about.php" class="btn btn-primary rounded-3 px-4 py-2.5 fw-semibold shadow-sm" style="font-size: 0.88rem;">
                        About Us <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-outline-secondary rounded-3 px-4 py-2.5 fw-semibold" style="font-size: 0.88rem;">
                        Contact Our Team
                    </a>
                </div>
            </div>

            <!-- Right: 3 Key Facts / Pillars (Clean typography without card boxes) -->
            <div class="col-lg-5">
                <div class="ps-lg-4 border-start border-2 border-primary border-opacity-25 d-flex flex-column gap-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-check-circle text-primary fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Certified Load Testing</h6>
                        </div>
                        <p class="text-muted small mb-0 ps-4">Units undergo mechanical diagnostics and full electrical load-bank validation before departure.</p>
                    </div>

                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-ship text-primary fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Worldwide Ocean Freight</h6>
                        </div>
                        <p class="text-muted small mb-0 ps-4">Turnkey international logistics, specialized heavy cargo packaging, and export handling across 45+ countries.</p>
                    </div>

                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-shield-alt text-primary fs-5"></i>
                            <h6 class="fw-bold text-dark mb-0">Verified OEM Machinery</h6>
                        </div>
                        <p class="text-muted small mb-0 ps-4">Direct sourcing from major brands with authentic operating hours and complete technical reports.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pure Photo Slider Section (Photo Seule, Défilement automatique toutes les 3 secondes) -->
<section id="showcase-slider" class="py-5 bg-light border-top border-bottom border-light">
    <div class="container py-2">
        
        <!-- Single-line clean title -->
        <div class="text-center mb-3">
            <h3 class="fw-extrabold text-dark mb-0" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.55rem; letter-spacing: -0.4px;">
                Featured Equipment in Stock
            </h3>
        </div>

        <?php if (!empty($slider_products)): ?>
        <div class="mx-auto" style="max-width: 1060px;">
            <div id="equipmentPhotoSlider" class="carousel slide shadow-lg rounded-4 overflow-hidden position-relative" data-bs-ride="carousel" data-bs-interval="3000" data-bs-pause="hover">
                
                <!-- Slides (Photo Seule avec légende minimaliste) -->
                <div class="carousel-inner bg-dark">
                    <?php foreach ($slider_products as $index => $prod): ?>
                    <?php $img_url = product_image_url($prod['main_image']); ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" data-bs-interval="3000">
                        <div class="position-relative" style="height: 520px; max-height: 75vh;">
                            <!-- Clickable Image directly to Details -->
                            <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $prod['id']; ?>" class="d-block w-100 h-100 text-decoration-none">
                                <img src="<?php echo htmlspecialchars($img_url); ?>" 
                                     alt="<?php echo htmlspecialchars($prod['name']); ?>" 
                                     class="d-block w-100 h-100" 
                                     style="object-fit: cover;">
                            </a>
                            
                            <!-- Sleek minimal gradient bar at the bottom with product name, price, and View Details button -->
                            <div class="slide-caption-bar p-3.5 p-md-4 text-white d-flex flex-column flex-sm-row align-items-start align-items-sm-end justify-content-between gap-2" 
                                 style="background: linear-gradient(180deg, transparent 0%, rgba(15, 23, 42, 0.92) 100%);">
                                <div>
                                    <h4 class="fw-bold text-white mb-1" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                                        <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $prod['id']; ?>" class="text-white text-decoration-none">
                                            <?php echo htmlspecialchars($prod['name']); ?>
                                        </a>
                                    </h4>
                                    <span class="text-white-50 small">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i> <?php echo htmlspecialchars($prod['location'] ?? 'Global Depot'); ?>
                                    </span>
                                </div>

                                <div class="text-sm-end">
                                    <span class="fs-5 fw-extrabold text-white d-block"><?php echo format_price($prod['price']); ?></span>
                                    <a href="<?php echo BASE_URL; ?>/product-details.php?id=<?php echo $prod['id']; ?>" 
                                       class="btn btn-sm btn-light rounded-2 px-3.5 py-1.5 fw-bold text-dark mt-1 shadow-sm d-inline-flex align-items-center gap-1.5" 
                                       style="font-size: 0.82rem; position: relative; z-index: 25;">
                                        View Details <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigation Controls -->
                <button class="carousel-control-prev" type="button" data-bs-target="#equipmentPhotoSlider" data-bs-slide="prev" aria-label="Previous">
                    <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.6));"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#equipmentPhotoSlider" data-bs-slide="next" aria-label="Next">
                    <span class="carousel-control-next-icon" aria-hidden="true" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.6));"></span>
                </button>

                <!-- Indicator dots -->
                <div class="carousel-indicators mb-2">
                    <?php foreach ($slider_products as $index => $prod): ?>
                    <button type="button" 
                            data-bs-target="#equipmentPhotoSlider" 
                            data-bs-slide-to="<?php echo $index; ?>" 
                            class="<?php echo $index === 0 ? 'active' : ''; ?>" 
                            aria-label="Slide <?php echo $index + 1; ?>"
                            style="width: 8px; height: 8px; border-radius: 50%; margin: 0 4px;">
                    </button>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- Single Clean Button to Full Catalog (Radius diminué) -->
            <div class="text-center mt-4">
                <a href="<?php echo BASE_URL; ?>/products.php" class="btn btn-primary rounded-3 px-4.5 py-2.5 fw-semibold shadow-sm" style="font-size: 0.9rem;">
                    Browse Full Catalog (60+ Items) <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- Simple Executive Call To Action (Sobre & Pro) -->
<section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(180deg, #070e1c 0%, #0b162d 100%);">
    <div class="container text-center py-2 position-relative" style="z-index: 2;">
        <h3 class="fw-extrabold mb-2 text-white" style="font-family: 'Plus Jakarta Sans', sans-serif; letter-spacing: -0.4px; font-size: 1.75rem;">
            Planning a Heavy Power Project?
        </h3>
        
        <p class="text-white-50 mb-4 mx-auto" style="max-width: 600px; font-size: 0.92rem; line-height: 1.6;">
            Our specialists assist with load calculations, technical inspection reports, and worldwide ocean freight shipping.
        </p>

        <!-- Executive Action Buttons -->
        <div class="d-flex flex-wrap justify-content-center align-items-center" style="gap: 1rem !important;">
            <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>" target="_blank" class="btn btn-success rounded-3 px-4 py-2 fw-semibold shadow-sm text-nowrap" style="font-size: 0.88rem;">
                <i class="fab fa-whatsapp me-2 fs-6"></i> Speak on WhatsApp
            </a>
            <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-outline-light rounded-3 px-4 py-2 fw-semibold text-nowrap" style="font-size: 0.88rem;">
                <i class="fas fa-envelope me-2"></i> Contact Us
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

