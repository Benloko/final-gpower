            </main>
        </div>
    </div>
    
    <div class="mobile-backdrop" id="mobileBackdrop"></div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <script>
        // Mobile menu management
        const navbarCollapse = document.getElementById('adminNavbar');
        const backdrop = document.getElementById('mobileBackdrop');
        const navbarToggler = document.querySelector('.navbar-toggler');
        
        // Show/hide backdrop with menu
        navbarCollapse.addEventListener('show.bs.collapse', function() {
            backdrop.classList.add('show');
        });
        
        navbarCollapse.addEventListener('hidden.bs.collapse', function() {
            backdrop.classList.remove('show');
        });
        
        // Close menu when clicking backdrop
        backdrop.addEventListener('click', function() {
            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
            if (bsCollapse) {
                bsCollapse.hide();
            }
        });
        
        // Close menu when clicking on a link
        document.querySelectorAll('#adminNavbar .nav-link').forEach(link => {
            link.addEventListener('click', function() {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            });
        });
        
        // Close menu with close button
        document.querySelector('#adminNavbar .btn-close')?.addEventListener('click', function() {
            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
            if (bsCollapse) {
                bsCollapse.hide();
            }
        });
    </script>
</body>
</html>
