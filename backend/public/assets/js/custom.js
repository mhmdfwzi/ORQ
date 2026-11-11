// ===================================
// Medicamo - Custom JavaScript
// ===================================

// Initialize AOS (Animate On Scroll)
document.addEventListener('DOMContentLoaded', function () {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        mirror: false,
        offset: 100
    });
});

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

window.addEventListener('scroll', function () {
    if (window.scrollY > 300) {
        backToTopButton.classList.add('show');
    } else {
        backToTopButton.classList.remove('show');
    }
});

backToTopButton.addEventListener('click', function () {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// ===================================
// Add to Cart Animation
// ===================================
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('add-to-cart-btn') ||
        e.target.closest('.add-to-cart-btn')) {

        const button = e.target.classList.contains('add-to-cart-btn')
            ? e.target
            : e.target.closest('.add-to-cart-btn');

        // Add animation
        button.innerHTML = '<i class="fas fa-check"></i> Added';
        button.style.background = 'var(--secondary-green)';

        // Update cart counter
        const cartBadge = document.querySelector('.cart-badge');
        if (cartBadge) {
            const currentCount = parseInt(cartBadge.textContent);
            cartBadge.textContent = currentCount + 1;

            // Counter animation
            cartBadge.style.animation = 'none';
            setTimeout(() => {
                cartBadge.style.animation = 'pulse 0.5s ease';
            }, 10);
        }

        // Reset original text after 2 seconds
        setTimeout(() => {
            button.innerHTML = '<i class="fas fa-cart-plus"></i> Add to Cart';
            button.style.background = '';
        }, 2000);

        // Show notification
        showNotification('Product added to cart successfully', 'success');
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
// Quantity Controls
// ===================================
document.addEventListener('click', function (e) {
    // Increase quantity
    if (e.target.classList.contains('quantity-btn') &&
        e.target.textContent === '+') {
        const input = e.target.parentElement.querySelector('.quantity-input');
        input.value = parseInt(input.value) + 1;
        updateCartTotal();
    }

    // Decrease quantity
    if (e.target.classList.contains('quantity-btn') &&
        e.target.textContent === '−') {
        const input = e.target.parentElement.querySelector('.quantity-input');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
            updateCartTotal();
        }
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
// Mobile Menu Toggle
// ===================================
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const navbarNav = document.getElementById('navbarNav');

if (mobileMenuBtn && navbarNav) {
    mobileMenuBtn.addEventListener('click', function () {
        navbarNav.classList.toggle('active');

        // Change icon
        const icon = this.querySelector('i');
        if (navbarNav.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });

    // Close menu when clicking on any link
    const navLinks = navbarNav.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function () {
            navbarNav.classList.remove('active');
            const icon = mobileMenuBtn.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function (event) {
        if (!event.target.closest('.navbar') && navbarNav.classList.contains('active')) {
            navbarNav.classList.remove('active');
            const icon = mobileMenuBtn.querySelector('i');
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        }
    });
}
