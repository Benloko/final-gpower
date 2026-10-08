    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 border-top border-secondary border-opacity-25" style="background: #060b17 !important;">
        <div class="container pb-4">
            <div class="row g-4">
                <!-- Brand & Mission Column -->
                <div class="col-lg-3 col-md-6">
                    <div class="mb-4">
                        <a href="<?php echo BASE_URL; ?>/" class="d-flex align-items-center gap-2.5 text-decoration-none mb-3">
                            <div class="p-1 rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <img src="<?php echo BASE_URL; ?>/assets/images/logo.jpeg" alt="Gpower" class="rounded-circle" style="width: 34px; height: 34px; object-fit: cover;">
                            </div>
                            <span class="brand-text text-white fs-4 fw-extrabold" style="font-family: 'Plus Jakarta Sans', sans-serif;">GPOWER<span class="text-primary">.</span></span>
                        </a>
                        <p class="text-white-50 small mb-4" style="line-height: 1.6; max-width: 270px;">
                            Global supplier of certified industrial diesel generators, gas turbines, heavy engines, and power plant equipment.
                        </p>
                        <div class="d-flex gap-2">
                            <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>" target="_blank" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center text-success border-secondary border-opacity-50" style="width: 36px; height: 36px;">
                                <i class="fab fa-whatsapp fs-6"></i>
                            </a>
                            <a href="https://www.facebook.com/profile.php?id=61579119517182" target="_blank" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center text-white-50 border-secondary border-opacity-50" style="width: 36px; height: 36px;">
                                <i class="fab fa-facebook-f fs-6"></i>
                            </a>
                            <a href="https://www.tiktok.com/@generator_power23" target="_blank" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center text-white-50 border-secondary border-opacity-50" style="width: 36px; height: 36px;">
                                <i class="fab fa-tiktok fs-6"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Newsletter & Equipment Alerts Column -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="fw-bold text-white mb-2 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Equipment Inventory Alerts</h6>
                    <p class="text-white-50 small mb-3" style="font-size: 0.8rem; line-height: 1.5;">
                        Subscribe to receive instant technical spec sheets and priority notifications on newly listed heavy power equipment.
                    </p>

                    <!-- Rounded Pill Subscription Form -->
                    <form action="<?php echo BASE_URL; ?>/subscribe.php" method="POST" class="mb-3">
                        <div class="input-group bg-dark bg-opacity-60 rounded-pill p-1 border border-secondary border-opacity-40 shadow-sm">
                            <input type="email" name="email" class="form-control border-0 shadow-none bg-transparent text-white ps-3 small" 
                                   placeholder="Enter business email..." required style="height: 38px; font-size: 0.84rem;">
                            <button type="submit" name="subscribe" class="btn btn-primary rounded-pill px-3.5 fw-semibold d-flex align-items-center gap-1.5" style="height: 38px; font-size: 0.82rem;">
                                <span>Subscribe</span> <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Direct Line Sub-info -->
                    <div class="d-flex align-items-center gap-3 text-white-50 small" style="font-size: 0.76rem;">
                        <span class="d-flex align-items-center gap-1.5"><i class="fas fa-shield-alt text-success"></i> Verified Privacy</span>
                        <span class="text-secondary">•</span>
                        <span class="d-flex align-items-center gap-1.5"><i class="fas fa-phone-alt text-primary"></i> <?php echo htmlspecialchars($settings['contact_phone'] ?? '+1 800 GPOWER'); ?></span>
                    </div>
                </div>

                <!-- Navigation Column -->
                <div class="col-lg-2 col-md-3 col-6 ps-lg-4">
                    <h6 class="fw-bold text-white mb-3 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Navigation</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 small">
                        <li><a href="<?php echo BASE_URL; ?>/" class="text-white-50 text-decoration-none hover-white"><i class="fas fa-chevron-right me-1 text-primary" style="font-size: 0.65rem;"></i> <?php echo t('home'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>/products.php" class="text-white-50 text-decoration-none hover-white"><i class="fas fa-chevron-right me-1 text-primary" style="font-size: 0.65rem;"></i> Catalog</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/about.php" class="text-white-50 text-decoration-none hover-white"><i class="fas fa-chevron-right me-1 text-primary" style="font-size: 0.65rem;"></i> <?php echo t('about'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>/contact.php" class="text-white-50 text-decoration-none hover-white"><i class="fas fa-chevron-right me-1 text-primary" style="font-size: 0.65rem;"></i> <?php echo t('contact'); ?></a></li>
                    </ul>
                </div>

                <!-- Equipment Categories Column -->
                <div class="col-lg-3 col-md-3 col-6">
                    <h6 class="fw-bold text-white mb-3 text-uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Equipment Sectors</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 small">
                        <li><a href="<?php echo BASE_URL; ?>/products.php?search=CAT" class="text-white-50 text-decoration-none hover-white"><i class="fas fa-bolt me-1 text-warning" style="font-size: 0.7rem;"></i> Diesel Generators</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/products.php?search=Turbine" class="text-white-50 text-decoration-none hover-white"><i class="fas fa-wind me-1 text-info" style="font-size: 0.7rem;"></i> Gas Turbines</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/products.php?search=Engine" class="text-white-50 text-decoration-none hover-white"><i class="fas fa-cog me-1 text-primary" style="font-size: 0.7rem;"></i> Heavy Engines</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/products.php?search=Transformer" class="text-white-50 text-decoration-none hover-white"><i class="fas fa-plug me-1 text-success" style="font-size: 0.7rem;"></i> Power Transformers</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright Bar -->
        <div class="border-top border-secondary border-opacity-25 py-3 bg-black bg-opacity-40">
            <div class="container">
                <div class="row align-items-center g-2">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="text-white-50 small mb-0" style="font-size: 0.78rem;">&copy; <?php echo date('Y'); ?> GPOWER Industrial Heavy Equipment Inc. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <ul class="list-inline mb-0 small" style="font-size: 0.78rem;">
                            <li class="list-inline-item ms-3"><a href="<?php echo BASE_URL; ?>/legal.php?section=privacy" class="text-white-50 text-decoration-none hover-white">Privacy Policy</a></li>
                            <li class="list-inline-item ms-3"><a href="<?php echo BASE_URL; ?>/legal.php?section=terms" class="text-white-50 text-decoration-none hover-white">Terms of Sale</a></li>
                            <li class="list-inline-item ms-3"><a href="<?php echo BASE_URL; ?>/legal.php?section=mentions" class="text-white-50 text-decoration-none hover-white">Legal Notice</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>" 
       class="whatsapp-float rounded-circle d-flex align-items-center justify-content-center text-white text-decoration-none shadow-lg" 
       target="_blank"
       title="WhatsApp Specialist"
       style="position: fixed; bottom: 24px; right: 24px; width: 52px; height: 52px; background: #25D366; z-index: 999; font-size: 1.4rem; transition: transform 0.2s ease;">
        <i class="fab fa-whatsapp"></i>
    </a>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
