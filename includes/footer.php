    <!-- Footer -->
    <footer class="bg-white pt-4 border-top">
        <!-- Newsletter Section (Compact) -->
        <div class="container mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="p-4 rounded-4 bg-light border border-light-subtle position-relative overflow-hidden">
                        <div class="row align-items-center g-3">
                            <div class="col-md-7">
                                <h5 class="fw-bold text-dark mb-2"><i class="fas fa-star text-primary me-2"></i>L'excellence à votre portée</h5>
                                <p class="text-muted mb-0 small">Rejoignez l'élite des professionnels. Recevez nos offres exclusives et découvrez nos innovations en avant-première.</p>
                            </div>
                            <div class="col-md-5">
                                <?php if ($is_subscribed): ?>
                                <div class="text-center">
                                    <div class="d-inline-flex align-items-center gap-2 bg-success bg-opacity-10 rounded-pill px-2 py-1 border border-success border-opacity-25">
                                        <i class="fas fa-check-circle text-success" style="font-size: 1rem;"></i>
                                        <span class="fw-semibold text-success" style="font-size: 0.85rem;">Abonné(e)</span>
                                        <a href="<?php echo BASE_URL; ?>/unsubscribe.php" 
                                           class="text-secondary ms-1" 
                                           style="font-size: 0.9rem; text-decoration: none;"
                                           title="Se désabonner">
                                            <i class="fas fa-times-circle"></i>
                                        </a>
                                    </div>
                                </div>
                                <?php else: ?>
                                <form method="POST" action="<?php echo BASE_URL; ?>/subscribe.php" class="position-relative" id="newsletterForm">
                                    <div class="newsletter-input-wrapper">
                                        <input type="email" 
                                               name="email" 
                                               class="form-control form-control-lg ps-5 pe-5 rounded-pill border-0 shadow-sm" 
                                               placeholder="Votre email ..." 
                                               style="height: 55px; font-size: 0.95rem; padding-right: 140px !important;"
                                               required>
                                        <i class="fas fa-envelope position-absolute top-50 start-0 translate-middle-y ms-4 text-primary fs-5"></i>
                                        <button class="btn btn-primary rounded-pill position-absolute top-50 end-0 translate-middle-y me-2 shadow-sm fw-bold px-4" 
                                                type="submit" 
                                                name="subscribe"
                                                style="height: 40px;">
                                            S'abonner
                                        </button>
                                    </div>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Newsletter Notifications (only errors) -->
        <?php if (isset($_GET['newsletter']) && in_array($_GET['newsletter'], ['invalid', 'error', 'empty', 'unsubscribed'])): ?>
        <div class="container mb-3">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <?php if ($_GET['newsletter'] === 'unsubscribed'): ?>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        Vous avez été désabonné de la newsletter. Vous pouvez vous réabonner à tout moment.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php elseif ($_GET['newsletter'] === 'invalid'): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Veuillez entrer une adresse email valide.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php elseif ($_GET['newsletter'] === 'error'): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle me-2"></i>
                        Une erreur s'est produite. Veuillez réessayer.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Main Footer Content -->
        <div class="container pb-5">
            <div class="row g-4">
                <!-- Brand Column -->
                <div class="col-lg-4 col-md-6">
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="<?php echo BASE_URL; ?>/assets/images/logo.jpeg" alt="Gpower" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <h3 class="fw-bold text-dark mb-0">GPOWER</h3>
                        </div>
                        <p class="text-muted mb-4 small" style="line-height: 1.6;">
                            Votre partenaire de confiance toujours disponible.
                        </p>
                        <div class="d-flex gap-3">
                            <a href="#" class="social-icon-btn facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon-btn instagram" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon-btn linkedin" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-icon-btn whatsapp" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="fw-bold text-dark mb-3">Navigation</h6>
                    <ul class="list-unstyled footer-nav-links">
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="about.php">À propos</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>

                <!-- Info Links -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="fw-bold text-dark mb-3">Informations</h6>
                    <ul class="list-unstyled footer-nav-links">
                        <li><a href="legal.php?section=mentions">Mentions Légales</a></li>
                        <li><a href="legal.php?section=privacy">Confidentialité</a></li>
                        <li><a href="legal.php?section=terms">Conditions</a></li>
                    </ul>
                </div>

                <!-- Help Card -->
                <div class="col-lg-4 col-md-12">
                    <div class="p-4 rounded-4 bg-light border border-light-subtle hover-lift transition-all">
                        <h6 class="fw-bold text-dark mb-2">Besoin d'aide ?</h6>
                        <p class="text-muted small mb-3">Notre équipe est disponible 24h/24 pour vous assister.</p>
                        
                        <div class="d-grid gap-2">
                            <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? '2250707070707'; ?>" 
                               target="_blank" 
                               class="btn btn-success text-white fw-bold py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm btn-pulse">
                                <i class="fab fa-whatsapp"></i> Discuter sur WhatsApp
                            </a>
                            <a href="mailto:<?php echo $settings['site_email'] ?? 'contact@gpower.ci'; ?>" 
                               class="btn btn-white bg-white border fw-bold py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                <i class="fas fa-envelope"></i> Envoyer un Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="border-top py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="text-muted small mb-0">&copy; <?php echo date('Y'); ?> Gpower Inc. Tous droits réservés.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <ul class="list-inline mb-0 small">
                            <li class="list-inline-item ms-3"><a href="#" class="text-muted text-decoration-none">Confidentialité</a></li>
                            <li class="list-inline-item ms-3"><a href="#" class="text-muted text-decoration-none">Conditions</a></li>
                            <li class="list-inline-item ms-3"><a href="#" class="text-muted text-decoration-none">Cookies</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/<?php echo $settings['whatsapp_number'] ?? '1234567890'; ?>" 
       class="whatsapp-float rounded-circle d-flex align-items-center justify-content-center text-white text-decoration-none" 
       target="_blank"
       title="<?php echo t('contact_whatsapp'); ?>">
        <i class="fas fa-comment-alt"></i>
    </a>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    // Handle unsubscribe with SweetAlert
    document.addEventListener('DOMContentLoaded', function() {
        const unsubscribeBtn = document.querySelector('a[href*="unsubscribe.php"]');
        if (unsubscribeBtn) {
            unsubscribeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;
                
                Swal.fire({
                    title: 'Se désabonner ?',
                    text: 'Ne plus recevoir de notifications',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#6c757d',
                    cancelButtonColor: '#198754',
                    confirmButtonText: 'Oui',
                    cancelButtonText: 'Non',
                    reverseButtons: true,
                    width: '350px',
                    padding: '1.5rem'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        }
    });
    </script>
    
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    
    
</body>
</html>
