<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/language.php';

// Get settings from database
$pdo = getPDOConnection();
$stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
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
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo t('site_name'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="header sticky-top">
        <!-- Main navbar -->
        <nav class="navbar navbar-expand-lg navbar-light py-3">
            <div class="container d-flex align-items-center justify-content-between">
                <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo BASE_URL; ?>/">
                    <img src="<?php echo BASE_URL; ?>/assets/images/logo.jpeg" alt="Gpower" class="brand-logo rounded-circle" style="width: 42px; height: 42px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    <span class="brand-text">Gpower</span>
                </a>

                <!-- Centered nav links on desktop -->
                <div class="d-none d-lg-flex justify-content-center flex-grow-1">
                    <ul class="navbar-nav d-flex flex-row gap-4 mb-0 align-items-center">
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/"><?php echo t('home'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/about.php"><?php echo t('about'); ?></a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/contact.php"><?php echo t('contact'); ?></a></li>
                    </ul>
                </div>

                <!-- Right controls: language + whatsapp + mobile toggler -->
                <div class="d-flex align-items-center gap-2">
                    <div class="d-none d-lg-block">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-1 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight:600;font-size:0.85rem;">
                                <i class="fas fa-globe me-1"></i><?php echo strtoupper($current_lang); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/change_language.php?lang=en">🇬🇧 English</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/change_language.php?lang=fr">🇫🇷 Français</a></li>
                            </ul>
                        </div>
                    </div>

                    <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>" target="_blank" class="btn btn-success btn-whatsapp-desktop d-none d-lg-inline-flex align-items-center justify-content-center" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp" aria-hidden="true"></i>
                        <span class="whatsapp-label ms-2">WhatsApp</span>
                    </a>

                    <button class="navbar-toggler border-0 shadow-none d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Mobile offcanvas menu -->
        <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel" style="position:fixed; top:0; left:0; width:85%; max-width:340px; z-index:1075; background:#ffffff;">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="mobileMenuLabel">
                    <div class="d-flex align-items-center gap-2">
                        <img src="<?php echo BASE_URL; ?>/assets/images/logo.jpeg" alt="Gpower" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                        <span class="brand-text">Gpower</span>
                    </div>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/"><?php echo t('home'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/about.php"><?php echo t('about'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/contact.php"><?php echo t('contact'); ?></a></li>
                </ul>

                <hr class="my-3">

                <div class="mb-3">
                    <p class="small text-muted mb-2"><?php echo t('language'); ?></p>
                    <div class="d-flex gap-2">
                        <a href="<?php echo BASE_URL; ?>/change_language.php?lang=en" class="btn btn-outline-secondary flex-fill">🇬🇧 English</a>
                        <a href="<?php echo BASE_URL; ?>/change_language.php?lang=fr" class="btn btn-outline-secondary flex-fill">🇫🇷 Français</a>
                    </div>
                </div>

                <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>" target="_blank" class="btn btn-success w-100 py-2"> <i class="fab fa-whatsapp me-2"></i> <?php echo t('contact_whatsapp'); ?></a>
            </div>
        </div>
        
        
    </header>
