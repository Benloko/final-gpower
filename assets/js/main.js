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
