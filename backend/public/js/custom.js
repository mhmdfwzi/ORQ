// ===================================
// Medicamo - Custom JavaScript
// ===================================

// Initialize AOS (Animate On Scroll) - instant load
AOS.init({
    duration: 400,
    easing: 'ease-out',
    once: true,
    mirror: false,
    offset: 30,
    disable: 'mobile' // Disable on mobile devices
});

// ===================================
// Hero Text Slider
// ===================================
(function () {
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slider-dot');
    let currentSlide = 0;
    let slideInterval;

    if (slides.length === 0) return;

    function showSlide(index) {
        // Remove active from all
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        // Reset index if needed
        if (index >= slides.length) index = 0;
        if (index < 0) index = slides.length - 1;

        currentSlide = index;

        // Add active to current
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function startAutoSlide() {
        slideInterval = setInterval(nextSlide, 7000); // Every 7 seconds
    }

    function stopAutoSlide() {
        clearInterval(slideInterval);
    }

    // Dot click handlers
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            stopAutoSlide();
            showSlide(index);
            startAutoSlide();
        });
    });

    // Start auto-sliding
    startAutoSlide();

    // Pause on hover
    const sliderContainer = document.querySelector('.hero-text-slider');
    if (sliderContainer) {
        sliderContainer.addEventListener('mouseenter', stopAutoSlide);
        sliderContainer.addEventListener('mouseleave', startAutoSlide);
    }
})();

// ===================================
// Header Scroll Effect
// ===================================
window.addEventListener('scroll', function () {
    const header = document.getElementById('header');
    if (window.scrollY > 100) {
        header.classList.add('header-scrolled');
    } else {
        header.classList.remove('header-scrolled');
    }
});

// ===================================
// Back to Top Button
// ===================================
const backToTopButton = document.getElementById('backToTop');

if (backToTopButton) {
    window.addEventListener('scroll', function () {
        if (window.scrollY > 300) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }
    });

    backToTopButton.addEventListener('click', function () {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ===================================
// Add to Cart Animation
// ===================================
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-to-cart-btn') ||
        e.target.closest('.add-to-cart-btn') ||
        e.target.classList.contains('cart-btn') ||
        e.target.closest('.cart-btn')) {

        const button = e.target.classList.contains('add-to-cart-btn') || e.target.classList.contains('cart-btn')
            ? e.target
            : e.target.closest('.add-to-cart-btn') || e.target.closest('.cart-btn');

        // Get product ID from data attribute
        const productId = button.getAttribute('data-product-id');

        if (!productId) {
            console.log('No product ID found');
            return;
        }

        // Store original content
        const originalContent = button.innerHTML;

        // Show loading state
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value;

        // Make AJAX request to add to cart
        fetch(`/cart/add/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ quantity: 1 })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success animation
                    button.innerHTML = '<i class="fas fa-check"></i> Added';
                    button.style.background = 'var(--secondary-green, #10b981)';

                    // Update cart counter
                    const cartBadge = document.querySelector('.cart-badge');
                    if (cartBadge && data.cartCount !== undefined) {
                        cartBadge.textContent = data.cartCount;
                    } else if (cartBadge) {
                        const currentCount = parseInt(cartBadge.textContent) || 0;
                        cartBadge.textContent = currentCount + 1;
                    }

                    // Counter animation
                    if (cartBadge) {
                        cartBadge.style.animation = 'none';
                        setTimeout(() => {
                            cartBadge.style.animation = 'pulse 0.5s ease';
                        }, 10);
                    }

                    // Show notification
                    showNotification('Product added to cart successfully', 'success');
                } else {
                    showNotification(data.message || 'Error adding to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error adding to cart', 'error');
            })
            .finally(() => {
                // Reset button after 2 seconds
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.style.background = '';
                    button.disabled = false;
                }, 2000);
            });
    }
});

// ===================================
// Notification System
// ===================================
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideDown 0.3s ease reverse';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// ===================================
// Wishlist Toggle Handler
// ===================================
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-to-wishlist-btn') ||
        e.target.closest('.add-to-wishlist-btn') ||
        e.target.classList.contains('wishlist-btn') ||
        e.target.closest('.wishlist-btn')) {

        const button = e.target.classList.contains('add-to-wishlist-btn') || e.target.classList.contains('wishlist-btn')
            ? e.target
            : e.target.closest('.add-to-wishlist-btn') || e.target.closest('.wishlist-btn');

        const productId = button.getAttribute('data-product-id');
        if (!productId) return;

        const icon = button.querySelector('i') || button;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value;

        // Show loading
        icon.classList.remove('fas', 'far');
        icon.classList.add('fas', 'fa-spinner', 'fa-spin');

        fetch(`/wishlist/toggle/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (response.status === 401) {
                    // Not logged in
                    return response.json().then(data => {
                        showNotification('Please login to add to wishlist', 'error');
                        if (data.redirect) {
                            setTimeout(() => window.location.href = data.redirect, 1500);
                        }
                        throw new Error('Not authenticated');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add('fa-heart');

                    if (data.in_wishlist) {
                        button.classList.add('in-wishlist');
                        icon.style.color = '#ff4757';
                    } else {
                        button.classList.remove('in-wishlist');
                        icon.style.color = '';
                    }

                    showNotification(data.message, 'success');
                }
            })
            .catch(error => {
                if (error.message !== 'Not authenticated') {
                    icon.classList.remove('fa-spinner', 'fa-spin');
                    icon.classList.add('fa-heart');
                    showNotification('Error updating wishlist', 'error');
                }
            });
    }
});

// ===================================
// Product Image Gallery (for product details page)
// ===================================
const thumbnails = document.querySelectorAll('.thumbnail');
const mainImage = document.querySelector('.main-image img');

thumbnails.forEach(thumbnail => {
    thumbnail.addEventListener('click', function () {
        // Remove active class from all thumbnails
        thumbnails.forEach(t => t.classList.remove('active'));

        // Add active class to selected thumbnail
        this.classList.add('active');

        // Change main image
        const newSrc = this.querySelector('img').src;
        if (mainImage) {
            mainImage.src = newSrc;
        }
    });
});

// ===================================
// Quantity Controls (AJAX-based for cart page)
// ===================================
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.quantity-btn');
    if (!btn) return;

    const controls = btn.closest('.quantity-controls');
    const input = controls.querySelector('.quantity-input');
    const updateUrl = controls.getAttribute('data-update-url');
    let currentVal = parseInt(input.value);

    if (btn.classList.contains('plus')) {
        currentVal += 1;
    } else if (btn.classList.contains('minus') && currentVal > 1) {
        currentVal -= 1;
    } else {
        return;
    }

    input.value = currentVal;
    updateCartTotal();

    // If on cart page with AJAX URL, send update to server
    if (updateUrl) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ quantity: currentVal })
        })
            .catch(error => console.error('Cart update error:', error));
    }
});

// ===================================
// Update Cart Total
// ===================================
function updateCartTotal() {
    const cartRows = document.querySelectorAll('.cart-table tbody tr');
    let subtotal = 0;

    cartRows.forEach(row => {
        const priceText = row.querySelector('.cart-price').textContent;
        const price = parseFloat(priceText.replace(/[^0-9.]/g, ''));
        const quantity = parseInt(row.querySelector('.quantity-input').value);
        const total = price * quantity;

        row.querySelector('.cart-total-price').textContent = `${total.toFixed(2)} EGP`;
        subtotal += total;
    });

    // Update subtotal
    const subtotalElement = document.querySelector('.summary-row:nth-child(1) .summary-value');
    if (subtotalElement) {
        subtotalElement.textContent = `${subtotal.toFixed(2)} EGP`;
    }

    // Calculate shipping (example: 5 EGP)
    const shipping = 5;

    // Calculate final total
    const total = subtotal + shipping;
    const totalElement = document.querySelector('.summary-total .summary-value');
    if (totalElement) {
        totalElement.textContent = `${total.toFixed(2)} EGP`;
    }
}

// ===================================
// Remove from Cart
// ===================================
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-btn') ||
        e.target.closest('.remove-btn')) {

        const button = e.target.classList.contains('remove-btn')
            ? e.target
            : e.target.closest('.remove-btn');

        const row = button.closest('tr');

        // Delete animation
        row.style.animation = 'fadeOut 0.3s ease';

        setTimeout(() => {
            row.remove();
            updateCartTotal();

            // Update cart counter
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                const currentCount = parseInt(cartBadge.textContent);
                cartBadge.textContent = Math.max(0, currentCount - 1);
            }

            showNotification('Product removed from cart', 'success');
        }, 300);
    }
});

// Add fadeOut animation CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(50px);
        }
    }
`;
document.head.appendChild(style);

// ===================================
// Form Validation - Only for contact/newsletter forms
// ===================================
const contactForms = document.querySelectorAll('form.contact-form, form.newsletter-form');

contactForms.forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        let isValid = true;
        const inputs = form.querySelectorAll('input[required], textarea[required]');

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.style.borderColor = '#dc2626';

                // Remove red color when typing
                input.addEventListener('input', function () {
                    this.style.borderColor = '';
                }, { once: true });
            }
        });

        if (isValid) {
            // Actually submit the form
            form.submit();
        } else {
            showNotification('Please fill in all required fields', 'error');
        }
    });
});

// ===================================
// Password Toggle
// ===================================
document.addEventListener('click', function (e) {
    if (e.target.closest('.password-toggle button')) {
        const button = e.target.closest('.password-toggle button');
        const input = button.parentElement.querySelector('input');
        const icon = button.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
});

// ===================================
// Search Functionality
// ===================================
const searchInput = document.querySelector('.search-box input');
const productCards = document.querySelectorAll('.product-card');

if (searchInput) {
    searchInput.addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();

        productCards.forEach(card => {
            const title = card.querySelector('.product-title').textContent.toLowerCase();
            const category = card.querySelector('.product-category').textContent.toLowerCase();

            if (title.includes(searchTerm) || category.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
}

// ===================================
// Filter Products
// ===================================
const filterSelects = document.querySelectorAll('.filter-select');

filterSelects.forEach(select => {
    select.addEventListener('change', function () {
        const filterType = this.id;
        const filterValue = this.value;

        productCards.forEach(card => {
            if (filterValue === 'all') {
                card.style.display = 'block';
            } else {
                // Filter logic can be added here based on filter type
                // Example: by category or price
            }
        });
    });
});

// ===================================
// Initialize on Page Load
// ===================================
window.addEventListener('load', function () {
    // Update cart total if on cart page
    if (document.querySelector('.cart-table')) {
        updateCartTotal();
    }

    // Activate first thumbnail
    if (thumbnails.length > 0) {
        thumbnails[0].classList.add('active');
    }
});

// ===================================
// Console Message
// ===================================
console.log('%c * MEDICAMO - Pharmaceutical Production Company ',
    'background: #1e3a8a; color: #fff; font-size: 20px; padding: 10px; border-radius: 5px;');
console.log('%c Developed with ❤️ ',
    'background: #10b981; color: #fff; font-size: 14px; padding: 5px; border-radius: 3px;');
// ===================================
// Enhanced Mobile Menu Toggle
// ===================================
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const mobileMenu = document.getElementById('mobileMenu');
const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
const mobileMenuClose = document.getElementById('mobileMenuClose');

function openMobileMenu() {
    mobileMenu?.classList.add('active');
    mobileMenuOverlay?.classList.add('active');
    mobileMenuBtn?.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
    mobileMenu?.classList.remove('active');
    mobileMenuOverlay?.classList.remove('active');
    mobileMenuBtn?.classList.remove('active');
    document.body.style.overflow = '';
}

// Open menu on hamburger click
mobileMenuBtn?.addEventListener('click', function (e) {
    e.stopPropagation();
    if (mobileMenu?.classList.contains('active')) {
        closeMobileMenu();
    } else {
        openMobileMenu();
    }
});

// Close menu on X button click
mobileMenuClose?.addEventListener('click', closeMobileMenu);

// Close menu on overlay click
mobileMenuOverlay?.addEventListener('click', closeMobileMenu);

// Close menu on Escape key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && mobileMenu?.classList.contains('active')) {
        closeMobileMenu();
    }
});

// Close menu when clicking a link
document.querySelectorAll('.mobile-menu-link').forEach(link => {
    link.addEventListener('click', closeMobileMenu);
});

