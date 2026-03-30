// Main JavaScript for Gpower

// Product Gallery functionality
function changeMainImage(imgSrc, element) {
    document.getElementById('mainProductImage').src = imgSrc;
    
    // Update active state
    document.querySelectorAll('.product-gallery img').forEach(img => {
        img.classList.remove('border-primary');
        img.classList.add('border-transparent');
    });
    element.classList.remove('border-transparent');
    element.classList.add('border-primary');
}

// Image preview for file uploads
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Confirm delete action
function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this item?');
}

// WhatsApp integration
function contactWhatsApp(productName, phone) {
    const message = encodeURIComponent(`Hello, I'm interested in ${productName}`);
    const url = `https://wa.me/${phone}?text=${message}`;
    window.open(url, '_blank');
}

// Search functionality
function searchProducts(query) {
    // This will be handled by the server, but we can add client-side filtering if needed
    console.log('Searching for:', query);
}

// Smooth scroll to top
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Initialize tooltips (Bootstrap)
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Add smooth scrolling to all links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Live Filter Implementation (Grid Update)
    const searchForms = document.querySelectorAll('.hero-search-form, .sidebar-search-form');
    const productsGrid = document.getElementById('products-grid');
    
    if (productsGrid) {
        searchForms.forEach(form => {
            const input = form.querySelector('input[name="search"]');
            if (!input) return;

            // Prevent default form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
            });

            let debounceTimer;

            input.addEventListener('input', function() {
                const query = this.value.trim();
                
                clearTimeout(debounceTimer);
                
                debounceTimer = setTimeout(() => {
                    // Show loading state (optional)
                    productsGrid.style.opacity = '0.5';
                    
                    fetch(`ajax_filter_products.php?search=${encodeURIComponent(query)}`)
                        .then(response => response.text())
                        .then(html => {
                            productsGrid.innerHTML = html;
                            productsGrid.style.opacity = '1';
                        })
                        .catch(err => {
                            console.error('Filter error:', err);
                            productsGrid.style.opacity = '1';
                        });
                }, 300); // Debounce delay
            });
        });
    }
});

// Offcanvas manual fallback: ensure mobile menu opens even if Bootstrap fails to initialize
document.addEventListener('DOMContentLoaded', function() {
    const togglers = document.querySelectorAll('[data-bs-toggle="offcanvas"]');
    togglers.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('data-bs-target') || this.getAttribute('data-target');
            if (!target) return;
            const panel = document.querySelector(target);
            if (!panel) return;

            // If panel is already shown, hide it
            if (panel.classList.contains('show')) {
                panel.classList.remove('show');
                document.body.classList.remove('offcanvas-open');
                const bd = document.querySelector('.offcanvas-backdrop'); if (bd) bd.remove();
                this.setAttribute('aria-expanded', 'false');
                panel.setAttribute('aria-hidden', 'true');
                return;
            }

            // Show the panel immediately
            panel.classList.add('show');
            panel.setAttribute('aria-hidden', 'false');
            this.setAttribute('aria-expanded', 'true');

            // Add backdrop if missing
            if (!document.querySelector('.offcanvas-backdrop')) {
                const backdrop = document.createElement('div');
                backdrop.className = 'offcanvas-backdrop fade show';
                document.body.appendChild(backdrop);
            }

            // Prevent body scroll
            document.body.classList.add('offcanvas-open');
        });
    });

    // Close manual offcanvas when clicking backdrop or close buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.offcanvas-backdrop')) {
            document.querySelectorAll('.offcanvas.show').forEach(p => p.classList.remove('show'));
            document.body.classList.remove('offcanvas-open');
            const bd = document.querySelector('.offcanvas-backdrop'); if (bd) bd.remove();
        }
        if (e.target.matches('[data-bs-dismiss="offcanvas"]') || e.target.closest('[data-bs-dismiss="offcanvas"]')) {
            document.querySelectorAll('.offcanvas.show').forEach(p => p.classList.remove('show'));
            document.body.classList.remove('offcanvas-open');
            const bd = document.querySelector('.offcanvas-backdrop'); if (bd) bd.remove();
        }
    });
    
    // Make entire product card clickable (except interactive children like buttons/links)
    document.querySelectorAll('.product-card[data-href]').forEach(card => {
        // Visual affordance
        card.style.cursor = 'pointer';

        card.addEventListener('click', function(e) {
            // If click inside an interactive element, do nothing
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('input') || e.target.closest('.product-contact') ) {
                return;
            }
            const href = this.getAttribute('data-href');
            if (href) {
                window.location.href = href;
            }
        });
    });

    // Language selector removed — site is English-only.
    // No-op placeholder to keep file structure consistent.

});
