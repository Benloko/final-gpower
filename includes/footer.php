    <!-- Footer -->
    <footer class="bg-dark text-white pt-5 pb-0 border-top border-secondary border-opacity-25" style="background: #070d19 !important;">
        <div class="container pb-4">
            <div class="row g-4 justify-content-between">
                <!-- Col 1: Brand & About -->
                <div class="col-lg-5 col-md-6">
                    <a href="<?php echo BASE_URL; ?>/" class="d-inline-flex align-items-center gap-2 text-decoration-none mb-3">
                        <img src="<?php echo BASE_URL; ?>/assets/images/logo.jpeg" alt="Gpower" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                        <span class="text-white fw-bold" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.25rem; letter-spacing: -0.5px;">GPOWER<span class="text-primary">.</span></span>
                    </a>
                    <p class="text-white-50 small mb-3" style="line-height: 1.6; max-width: 340px; font-size: 0.85rem;">
                        Certified industrial power generators, gas turbines, and heavy machinery for global export.
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? ''; ?>" target="_blank" class="text-white-50 hover-white text-decoration-none p-1.5" title="WhatsApp">
                            <i class="fab fa-whatsapp fs-5 text-success"></i>
                        </a>
                        <a href="https://www.facebook.com/profile.php?id=61579119517182" target="_blank" class="text-white-50 hover-white text-decoration-none p-1.5" title="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.tiktok.com/@generator_power23" target="_blank" class="text-white-50 hover-white text-decoration-none p-1.5" title="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Navigation Links -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h6 class="text-white fw-bold mb-3 small text-uppercase" style="letter-spacing: 0.8px; font-size: 0.8rem;">Navigation</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 small mb-0" style="font-size: 0.85rem;">
                        <li><a href="<?php echo BASE_URL; ?>/" class="text-white-50 text-decoration-none hover-white"><?php echo t('home'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>/products.php" class="text-white-50 text-decoration-none hover-white">Catalog</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/about.php" class="text-white-50 text-decoration-none hover-white"><?php echo t('about'); ?></a></li>
                        <li><a href="<?php echo BASE_URL; ?>/contact.php" class="text-white-50 text-decoration-none hover-white"><?php echo t('contact'); ?></a></li>
                    </ul>
                </div>

                <!-- Col 3: Newsletter & Contact -->
                <div class="col-lg-4 col-md-6">
                    <h6 class="text-white fw-bold mb-2 small text-uppercase" style="letter-spacing: 0.8px; font-size: 0.8rem;">Stay Updated</h6>
                    <p class="text-white-50 small mb-3" style="font-size: 0.82rem; line-height: 1.5;">
                        Subscribe for notifications on newly arrived heavy machinery.
                    </p>
                    <form action="<?php echo BASE_URL; ?>/subscribe.php" method="POST" class="mb-3">
                        <div class="input-group">
                            <input type="email" name="email" class="form-control text-white border-secondary border-opacity-50 rounded-start-2 small" 
                                   placeholder="Your email address..." required style="height: 38px; font-size: 0.82rem; background: rgba(255,255,255,0.06) !important;">
                            <button type="submit" name="subscribe" class="btn btn-primary rounded-end-2 px-3 fw-semibold" style="height: 38px; font-size: 0.82rem;">
                                Subscribe
                            </button>
                        </div>
                    </form>
                    <div class="text-white-50 small" style="font-size: 0.78rem;">
                        <i class="fas fa-envelope text-primary me-1.5"></i> <?php echo htmlspecialchars($settings['site_email'] ?? 'contact@gpower.com'); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright Bar -->
        <div class="border-top border-secondary border-opacity-25 py-3" style="background: rgba(0,0,0,0.35);">
            <div class="container">
                <div class="row align-items-center g-2">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="text-white-50 small mb-0" style="font-size: 0.78rem;">&copy; <?php echo date('Y'); ?> GPOWER. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <ul class="list-inline mb-0 small" style="font-size: 0.78rem;">
                            <li class="list-inline-item ms-3"><a href="<?php echo BASE_URL; ?>/legal.php?section=privacy" class="text-white-50 text-decoration-none hover-white">Privacy</a></li>
                            <li class="list-inline-item ms-3"><a href="<?php echo BASE_URL; ?>/legal.php?section=terms" class="text-white-50 text-decoration-none hover-white">Terms</a></li>
                            <li class="list-inline-item ms-3"><a href="<?php echo BASE_URL; ?>/legal.php?section=mentions" class="text-white-50 text-decoration-none hover-white">Legal</a></li>
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
       style="position: fixed; bottom: 24px; right: 24px; width: 50px; height: 50px; background: #25D366; z-index: 999; font-size: 1.35rem; transition: transform 0.2s ease;">
        <i class="fab fa-whatsapp"></i>
    </a>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
