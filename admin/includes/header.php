<?php
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$pdo = getPDOConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Gpower Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/admin.css">
</head>
<body style="background: #f8f9fa;">
    <!-- Modern Admin Header -->
    <nav class="navbar navbar-expand-md bg-white shadow-sm border-bottom sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?php echo ADMIN_URL; ?>/" style="color: #1a1a1a;">
                <img src="<?php echo BASE_URL; ?>/assets/images/logo.jpeg" alt="Gpower" class="rounded-circle shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                <div class="d-flex flex-column">
                    <span style="font-size: 1.1rem; line-height: 1;">Gpower</span>
                    <small class="text-muted" style="font-size: 0.7rem; font-weight: 400;">Admin Dashboard</small>
                </div>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                <i class="fas fa-bars" style="font-size: 1.5rem; color: #1a1a1a;"></i>
            </button>
            
            <div class="collapse navbar-collapse d-none d-md-flex" id="adminNavbar">
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item">
                        <a class="btn btn-sm btn-outline-dark rounded-3" href="<?php echo BASE_URL; ?>/" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i>
                            <span>View Site</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 rounded-3" 
                           href="#" 
                           role="button" 
                           data-bs-toggle="dropdown"
                           style="background: #f8f9fa; color: #1a1a1a; font-weight: 500;">
                            <i class="fas fa-user-circle fa-lg"></i>
                            <span><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2" style="min-width: 200px;">
                            <li><a class="dropdown-item py-2" href="<?php echo ADMIN_URL; ?>/change-password.php">
                                <i class="fas fa-key me-2 text-muted"></i>Change Password
                            </a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="<?php echo ADMIN_URL; ?>/logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Offcanvas Menu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileMenu" style="max-width: 280px;">
        <div class="offcanvas-header border-bottom">
            <div class="d-flex align-items-center gap-2">
                <img src="<?php echo BASE_URL; ?>/assets/images/logo.jpeg" alt="Gpower" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                <div>
                    <h6 class="mb-0 fw-bold">Gpower</h6>
                    <small class="text-muted">Admin Dashboard</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="nav flex-column p-3">
                <li class="nav-item mb-2">
                    <a class="nav-link rounded-3 d-flex align-items-center gap-3 px-3 py-3 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                       href="<?php echo ADMIN_URL; ?>/index.php"
                       style="background: <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? '#e8f5e9' : 'transparent'; ?>; color: <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? '#2e7d32' : '#6c757d'; ?>; font-weight: 500; transition: all 0.2s;">
                        <i class="fas fa-tachometer-alt" style="width: 20px;"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link rounded-3 d-flex align-items-center gap-3 px-3 py-3 <?php echo in_array(basename($_SERVER['PHP_SELF']), ['products.php', 'product-form.php', 'product-edit.php']) ? 'active' : ''; ?>" 
                       href="<?php echo ADMIN_URL; ?>/products.php"
                       style="background: <?php echo in_array(basename($_SERVER['PHP_SELF']), ['products.php', 'product-form.php', 'product-edit.php']) ? '#e8f5e9' : 'transparent'; ?>; color: <?php echo in_array(basename($_SERVER['PHP_SELF']), ['products.php', 'product-form.php', 'product-edit.php']) ? '#2e7d32' : '#6c757d'; ?>; font-weight: 500; transition: all 0.2s;">
                        <i class="fas fa-box" style="width: 20px;"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link rounded-3 d-flex align-items-center gap-3 px-3 py-3 <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>" 
                       href="<?php echo ADMIN_URL; ?>/messages.php"
                       style="background: <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? '#e8f5e9' : 'transparent'; ?>; color: <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? '#2e7d32' : '#6c757d'; ?>; font-weight: 500; transition: all 0.2s;">
                        <i class="fas fa-envelope" style="width: 20px;"></i>
                        <span>Messages</span>
                        <?php
                        try {
                            $pdo = getPDOConnection();
                            $stmt = $pdo->query("SELECT COUNT(*) as new_count FROM contact_messages WHERE status = 'new'");
                            $new_count = $stmt->fetch()['new_count'] ?? 0;
                            if ($new_count > 0):
                        ?>
                        <span class="badge rounded-pill" style="background: #dc3545; font-size: 0.7rem;"><?php echo $new_count; ?></span>
                        <?php 
                            endif;
                        } catch (Exception $e) {}
                        ?>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link rounded-3 d-flex align-items-center gap-3 px-3 py-3 <?php echo basename($_SERVER['PHP_SELF']) == 'newsletter.php' ? 'active' : ''; ?>" 
                       href="<?php echo ADMIN_URL; ?>/newsletter.php"
                       style="background: <?php echo basename($_SERVER['PHP_SELF']) == 'newsletter.php' ? '#e8f5e9' : 'transparent'; ?>; color: <?php echo basename($_SERVER['PHP_SELF']) == 'newsletter.php' ? '#2e7d32' : '#6c757d'; ?>; font-weight: 500; transition: all 0.2s;">
                        <i class="fas fa-users" style="width: 20px;"></i>
                        <span>Newsletter</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link rounded-3 d-flex align-items-center gap-3 px-3 py-3 <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" 
                       href="<?php echo ADMIN_URL; ?>/settings.php"
                       style="background: <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? '#e8f5e9' : 'transparent'; ?>; color: <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? '#2e7d32' : '#6c757d'; ?>; font-weight: 500; transition: all 0.2s;">
                        <i class="fas fa-cog" style="width: 20px;"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li class="nav-item"><hr class="my-3" style="opacity: 0.1;"></li>

                <li class="nav-item mb-2">
                    <a class="nav-link rounded-3 d-flex align-items-center gap-3 px-3 py-3" 
                       href="<?php echo BASE_URL; ?>/" target="_blank"
                       style="color: #6c757d; font-weight: 500; transition: all 0.2s;">
                        <i class="fas fa-external-link-alt" style="width: 20px;"></i>
                        <span>View Site</span>
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link rounded-3 d-flex align-items-center gap-3 px-3 py-3" 
                       href="<?php echo ADMIN_URL; ?>/logout.php"
                       style="color: #dc3545; font-weight: 500; transition: all 0.2s;">
                        <i class="fas fa-sign-out-alt" style="width: 20px;"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <style>
    .offcanvas-body .nav-link:hover {
        background: #f8f9fa !important;
        transform: translateX(4px);
    }
    .offcanvas-body .nav-link.active:hover {
        background: #e8f5e9 !important;
    }
    </style>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Modern Sidebar -->
            <nav class="col-md-2 d-none d-md-block bg-white border-end p-0" style="min-height: calc(100vh - 73px);">
                <div class="position-sticky pt-4">
                    <ul class="nav flex-column px-3">
                        <li class="nav-item mb-2">
                            <a class="nav-link rounded-3 d-flex align-items-center gap-2 px-3 py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active bg-dark text-white' : 'text-dark'; ?>" 
                               href="<?php echo ADMIN_URL; ?>/index.php"
                               style="font-weight: 500; transition: all 0.2s;">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link rounded-3 d-flex align-items-center gap-2 px-3 py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' || basename($_SERVER['PHP_SELF']) == 'product-form.php' || basename($_SERVER['PHP_SELF']) == 'product-edit.php' ? 'active bg-dark text-white' : 'text-dark'; ?>" 
                               href="<?php echo ADMIN_URL; ?>/products.php"
                               style="font-weight: 500; transition: all 0.2s;">
                                <i class="fas fa-box"></i>
                                <span>Products</span>
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link rounded-3 d-flex align-items-center gap-2 px-3 py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active bg-dark text-white' : 'text-dark'; ?>" 
                               href="<?php echo ADMIN_URL; ?>/messages.php"
                               style="font-weight: 500; transition: all 0.2s;">
                                <i class="fas fa-envelope"></i>
                                <span>Messages</span>
                                <?php
                                try {
                                    $pdo = getPDOConnection();
                                    $stmt = $pdo->query("SELECT COUNT(*) as new_count FROM contact_messages WHERE status = 'new'");
                                    $new_count = $stmt->fetch()['new_count'] ?? 0;
                                    if ($new_count > 0):
                                ?>
                                <span class="badge rounded-pill bg-danger ms-auto"><?php echo $new_count; ?></span>
                                <?php 
                                    endif;
                                } catch (Exception $e) {}
                                ?>
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link rounded-3 d-flex align-items-center gap-2 px-3 py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'newsletter.php' ? 'active bg-dark text-white' : 'text-dark'; ?>" 
                               href="<?php echo ADMIN_URL; ?>/newsletter.php"
                               style="font-weight: 500; transition: all 0.2s;">
                                <i class="fas fa-users"></i>
                                <span>Newsletter</span>
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link rounded-3 d-flex align-items-center gap-2 px-3 py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active bg-dark text-white' : 'text-dark'; ?>" 
                               href="<?php echo ADMIN_URL; ?>/settings.php"
                               style="font-weight: 500; transition: all 0.2s;">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="col-md-10 px-4 py-4" style="background: #f8f9fa;">
