<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/language.php';
require_once __DIR__ . '/helpers.php';

// Base path without query for JS use
$basePathOnly = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$current_page = basename($_SERVER['PHP_SELF']);

// Get settings from database
$pdo = getPDOConnection();
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    // Table doesn't exist yet (fresh install / incomplete import)
    $settings = [];
}

// Check if user is subscribed to newsletter
$is_subscribed = false;
if (isset($_COOKIE['visitor_email'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM newsletter_subscribers WHERE email = ? AND status = 'active'");
        $stmt->execute([$_COOKIE['visitor_email']]);
        $is_subscribed = $stmt->fetch() ? true : false;
    } catch (Exception $e) {
        // Table doesn't exist yet
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-site-verification" content="MlmZDajPpfi6R98SmUsi_K3Ixj-JMgl2wiVhjIXW1_o" />
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo t('site_name'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/css/style.css'); ?>">
    
    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Top Announcement & Contact Bar -->
    <div class="top-announcement-bar py-1 px-3 text-white border-bottom border-secondary border-opacity-25 d-none d-md-block" style="font-size: 0.78rem; background: #070d19 !important;">
        <div class="container d-flex justify-content-between align-items-center py-0.5">
            <div class="d-flex align-items-center gap-3 text-white-50">
                <span><i class="fas fa-bolt text-warning me-1"></i> <strong class="text-white">GPOWER INDUSTRIAL</strong> Heavy Machinery & Power Plants</span>
                <span class="text-secondary">•</span>
                <span><i class="fas fa-globe me-1 text-info"></i> Worldwide Freight & Logistics</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="tel:<?php echo $settings['contact_phone'] ?? ''; ?>" class="text-white-50 text-decoration-none hover-white">
                    <i class="fas fa-phone-alt me-1 text-primary"></i> <?php echo htmlspecialchars($settings['contact_phone'] ?? '+1 800 GPOWER'); ?>
                </a>
                <span class="text-secondary">•</span>
                <a href="mailto:<?php echo $settings['contact_email'] ?? ''; ?>" class="text-white-50 text-decoration-none hover-white">
                    <i class="fas fa-envelope me-1 text-primary"></i> <?php echo htmlspecialchars($settings['contact_email'] ?? 'contact@gpower.com'); ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header sticky-top">
        <!-- Main navbar -->
        <nav class="navbar navbar-expand-lg py-1.5">
            <div class="container d-flex align-items-center justify-content-between">
                <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo BASE_URL; ?>/">
                    <div class="brand-logo-container p-1 rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="border: 2px solid rgba(37,99,235,0.25);">
                        <img src="<?php echo BASE_URL; ?>/assets/images/logo.jpeg" alt="Gpower" class="brand-logo rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                    </div>
                    <div class="d-flex flex-column">
                        <span class="brand-text text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 1.25rem; letter-spacing: -0.5px; line-height: 1;">GPOWER<span class="text-primary">.</span></span>
                        <span class="brand-subtext text-muted" style="font-size: 0.6rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;">Heavy Equipment</span>
                    </div>
                </a>

                <!-- Centered nav links on desktop -->
                <div class="d-none d-lg-flex justify-content-center flex-grow-1">
                    <ul class="navbar-nav d-flex flex-row gap-3 mb-0 align-items-center">
                        <li class="nav-item"><a class="nav-link fw-semibold px-3 <?php echo ($current_page == 'index.php' || $current_page == '' || $current_page == 'index.fr.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/"><?php echo t('home'); ?></a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold px-3 <?php echo ($current_page == 'products.php' || $current_page == 'product-details.php' || $current_page == 'products.fr.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/products.php">Catalog</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold px-3 <?php echo ($current_page == 'about.php' || $current_page == 'about.fr.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/about.php"><?php echo t('about'); ?></a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold px-3 <?php echo ($current_page == 'contact.php' || $current_page == 'contact.fr.php') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/contact.php"><?php echo t('contact'); ?></a></li>
                    </ul>
                </div>

                <!-- Right controls: WhatsApp + mobile toggler -->
                <div class="d-flex align-items-center gap-2">
                    <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>" target="_blank" class="btn btn-success btn-whatsapp-desktop d-none d-lg-inline-flex align-items-center justify-content-center rounded-3 px-3 py-2 shadow-sm" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp me-2" aria-hidden="true" style="font-size: 1.05rem;"></i>
                        <span class="whatsapp-label fw-bold" style="font-size: 0.85rem;">Request Quote</span>
                    </a>

                    <button class="navbar-toggler border-0 shadow-none d-lg-none p-2 rounded-3 bg-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile offcanvas menu -->
        <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel" style="position:fixed; top:0; left:0; width:78%; max-width:320px; z-index:1075; background: linear-gradient(180deg, #0b132b 0%, #1c2541 100%);">
            <div class="offcanvas-header border-bottom border-secondary border-opacity-25 py-3">
                <h5 class="offcanvas-title" id="mobileMenuLabel">
                    <div class="d-flex align-items-center gap-2">
                        <img src="<?php echo BASE_URL; ?>/assets/images/logo.jpeg" alt="Gpower" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                        <span class="brand-text text-white" style="font-family: 'Plus Jakarta Sans', sans-serif;">GPOWER</span>
                    </div>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link text-white py-2" href="<?php echo BASE_URL; ?>/"><i class="fas fa-home me-2 text-primary"></i><?php echo t('home'); ?></a></li>
                    <li class="nav-item"><a class="nav-link text-white py-2" href="<?php echo BASE_URL; ?>/products.php"><i class="fas fa-boxes me-2 text-info"></i>Catalog</a></li>
                    <li class="nav-item"><a class="nav-link text-white py-2" href="<?php echo BASE_URL; ?>/about.php"><i class="fas fa-building me-2 text-warning"></i><?php echo t('about'); ?></a></li>
                    <li class="nav-item"><a class="nav-link text-white py-2" href="<?php echo BASE_URL; ?>/contact.php"><i class="fas fa-envelope me-2 text-success"></i><?php echo t('contact'); ?></a></li>
                </ul>

                <hr class="my-3 border-secondary border-opacity-25">

                <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>" target="_blank" class="btn btn-success w-100 py-2.5 rounded-3 fw-bold"> 
                    <i class="fab fa-whatsapp me-2"></i> Instant Quote
                </a>
            </div>
        </div>
    </header>

    <?php if (isset($_GET['newsletter'])): ?>
    <div class="container mt-3 position-relative" style="z-index: 1050;">
        <?php if ($_GET['newsletter'] === 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-pill px-4 shadow-sm border-0 d-flex align-items-center gap-2 mb-0" role="alert">
                <i class="fas fa-check-circle fs-5"></i>
                <span><strong>Subscription Confirmed!</strong> Thank you for subscribing to GPower equipment inventory alerts.</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['newsletter'] === 'exists'): ?>
            <div class="alert alert-info alert-dismissible fade show rounded-pill px-4 shadow-sm border-0 d-flex align-items-center gap-2 mb-0" role="alert">
                <i class="fas fa-info-circle fs-5"></i>
                <span><strong>Already Subscribed!</strong> Your email address is already registered for equipment alerts.</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['newsletter'] === 'resubscribed'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-pill px-4 shadow-sm border-0 d-flex align-items-center gap-2 mb-0" role="alert">
                <i class="fas fa-check-circle fs-5"></i>
                <span><strong>Welcome Back!</strong> Your subscription has been reactivated successfully.</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
