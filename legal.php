<?php
require_once 'config/config.php';
require_once 'includes/header.php';

$section = $_GET['section'] ?? 'mentions';
?>

<div class="container py-4 mt-2">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="mb-3">
                <a href="<?php echo BASE_URL; ?>/" class="btn btn-white rounded-pill px-4 py-2 shadow-sm hover-lift text-decoration-none fw-bold">
                    <i class="fas fa-arrow-left me-2 text-primary"></i> Back
                </a>
            </div>

            <div class="text-center mb-4">
                <h1 class="fw-bold display-5 mb-2">Legal Information</h1>
                <p class="text-muted lead">Transparency and trust are at the heart of our commitments.</p>
            </div>

            <div>
                <!-- Horizontal Navigation -->
                <div class="row g-2 mb-4 justify-content-center">
                    <div class="col-4 col-md-auto">
                        <a href="?section=mentions" class="btn w-100 h-100 px-1 py-2 px-md-4 py-md-3 rounded-pill shadow-sm d-flex flex-column flex-md-row align-items-center justify-content-center gap-1 gap-md-2 hover-lift <?php echo $section === 'mentions' ? 'btn-primary' : 'bg-white text-muted border'; ?>">
                            <i class="fas fa-building"></i> 
                            <span class="fw-bold d-none d-md-inline">Legal Notice</span>
                            <span class="fw-bold d-inline d-md-none" style="font-size: 0.7rem;">Legal</span>
                        </a>
                    </div>
                    <div class="col-4 col-md-auto">
                        <a href="?section=privacy" class="btn w-100 h-100 px-1 py-2 px-md-4 py-md-3 rounded-pill shadow-sm d-flex flex-column flex-md-row align-items-center justify-content-center gap-1 gap-md-2 hover-lift <?php echo $section === 'privacy' ? 'btn-primary' : 'bg-white text-muted border'; ?>">
                            <i class="fas fa-user-shield"></i>
                            <span class="fw-bold d-none d-md-inline">Privacy</span>
                            <span class="fw-bold d-inline d-md-none" style="font-size: 0.7rem;">Privacy</span>
                        </a>
                    </div>
                    <div class="col-4 col-md-auto">
                        <a href="?section=terms" class="btn w-100 h-100 px-1 py-2 px-md-4 py-md-3 rounded-pill shadow-sm d-flex flex-column flex-md-row align-items-center justify-content-center gap-1 gap-md-2 hover-lift <?php echo $section === 'terms' ? 'btn-primary' : 'bg-white text-muted border'; ?>">
                            <i class="fas fa-file-contract"></i>
                            <span class="fw-bold d-none d-md-inline">Terms (T&C)</span>
                            <span class="fw-bold d-inline d-md-none" style="font-size: 0.7rem;">T&C</span>
                        </a>
                    </div>
                </div>

                <!-- Content Area -->
                <div>
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-5">
                            <?php if ($section === 'mentions'): ?>
                                <h2 class="fw-bold mb-4 text-primary">Legal Notice</h2>
                                <div class="mb-4">
                                    <h5 class="fw-bold">1. Site Owner</h5>
                                    <p class="text-muted">
                                        This website is published by GPOWER SARL.<br>
                                        Registered office: Abidjan, Ivory Coast<br>
                                        Email: contact@gpower.ci<br>
                                        Phone: +225 07 07 07 07 07
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">2. Hosting</h5>
                                    <p class="text-muted">
                                        This website is hosted by [Hosting Provider Name], [Provider Address].
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">3. Intellectual Property</h5>
                                    <p class="text-muted">
                                        All content on this website is protected by applicable copyright and intellectual property laws. All rights reserved.
                                    </p>
                                </div>

                            <?php elseif ($section === 'privacy'): ?>
                                <h2 class="fw-bold mb-4 text-primary">Privacy Policy</h2>
                                <p class="lead text-muted mb-4">
                                    At GPOWER, we take the protection of your personal data seriously.
                                </p>
                                <div class="mb-4">
                                    <h5 class="fw-bold">Data Collection</h5>
                                    <p class="text-muted">
                                        Information collected on this site is stored by GPOWER for customer management and to send offers.
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">Use of Data</h5>
                                    <p class="text-muted">
                                        Data is retained for up to 3 years and is used by our marketing and sales teams located in Ivory Coast.
                                    </p>
                                </div>
                                <div class="alert alert-info border-0 rounded-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    In accordance with applicable law, you may exercise your right to access and correct your personal data by contacting us.
                                </div>

                            <?php elseif ($section === 'terms'): ?>
                                <h2 class="fw-bold mb-4 text-primary">Terms & Conditions</h2>
                                <div class="mb-4">
                                    <h5 class="fw-bold">1. Purpose</h5>
                                    <p class="text-muted">
                                        These terms govern the sale of electronic equipment and accessories by GPOWER.
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">2. Pricing</h5>
                                    <p class="text-muted">
                                        Product prices are shown in US Dollars (USD), including taxes, unless stated otherwise.
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">3. Orders</h5>
                                    <p class="text-muted">
                                        Orders can be placed via our website or through WhatsApp. Contractual information is provided in English.
                                    </p>
                                </div>
                                <div class="mb-4">
                                    <h5 class="fw-bold">4. Delivery</h5>
                                    <p class="text-muted">
                                        Products are delivered to the address provided during checkout within the timeframe displayed on the order confirmation page.
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
