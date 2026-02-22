/**
 * Main Application JavaScript
 * Triangle Printing Solutions
 */

// ========== GLOBAL STATE ==========
const appState = {
    cart: [],
    user: null,
    theme: 'light'
};

// ========== INITIALIZATION ==========
document.addEventListener('DOMContentLoaded', () => {
    initializeApp();
    loadCartFromStorage();
    setupEventListeners();
    updateCartCount();
});

function initializeApp() {
    console.log('Triangle Printing Solutions - Initializing...');
    
    // Check if user is logged in
    checkUserSession();
    
    // Initialize mobile menu
    initializeMobileMenu();
    
    // Setup scroll effects
    setupScrollEffects();
    
    // Initialize animations
    initializeAnimations();
}

// ========== MOBILE MENU ==========
function initializeMobileMenu() {
    const menuToggle = document.getElementById('menu-toggle');
    const mobileNav = document.getElementById('mobile-nav');
    
    if (!menuToggle || !mobileNav) return;
    
    menuToggle.addEventListener('click', () => {
        menuToggle.classList.toggle('active');
        mobileNav.classList.toggle('active');
    });
    
    // Close menu when link is clicked
    const mobileNavLinks = mobileNav.querySelectorAll('.mobile-nav-link');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', () => {
            menuToggle.classList.remove('active');
            mobileNav.classList.remove('active');
        });
    });
}

// ========== USER SESSION ==========
function checkUserSession() {
    // Session is handled by PHP
    // This is a frontend check for UI purposes
    const userEmail = document.body.dataset.userEmail;
    if (userEmail) {
        appState.user = {
            email: userEmail,
            role: document.body.dataset.userRole
        };
    }
}

// ========== EVENT LISTENERS ==========
function setupEventListeners() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            const target = document.querySelector(href);
            
            if (target && href !== '#') {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // CTA button clicks tracking
    document.querySelectorAll('.btn-primary, .btn-secondary').forEach(btn => {
        btn.addEventListener('click', (e) => {
            trackEvent('button_click', {
                text: btn.textContent,
                href: btn.href || 'none'
            });
        });
    });
}

// ========== ANIMATIONS ==========
function initializeAnimations() {
    // Fade-in animation on scroll
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.card, .product-card, .hero').forEach(el => {
            el.style.opacity = '0';
            el.style.animation = 'fadeInUp 0.6s ease forwards';
            observer.observe(el);
        });
    }
}

// ========== SCROLL EFFECTS ==========
function setupScrollEffects() {
    let lastScroll = 0;
    const header = document.querySelector('header');
    
    if (!header) return;
    
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > lastScroll && currentScroll > 100) {
            // Scrolling down - hide header
            header.style.transform = 'translateY(-100%)';
        } else {
            // Scrolling up - show header
            header.style.transform = 'translateY(0)';
        }
        
        lastScroll = currentScroll;
        header.style.transition = 'transform 0.3s ease';
    });
}

// ========== CART MANAGEMENT ==========
function addToCart(productId, productName, price, image = null) {
    const cartItem = {
        id: productId + '_' + Date.now(),
        productId: productId,
        name: productName,
        price: parseFloat(price),
        quantity: 1,
        image: image,
        addedAt: new Date().toISOString()
    };
    
    // Check if item already in cart
    const existingItem = appState.cart.find(item => item.productId === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        appState.cart.push(cartItem);
    }
    
    saveCartToStorage();
    updateCartCount();
    
    // Show success message
    showNotification(`${productName} added to cart!`, 'success');
    
    // Track event
    trackEvent('add_to_cart', {
        productId: productId,
        productName: productName,
        price: price
    });
}

function removeFromCart(itemId) {
    appState.cart = appState.cart.filter(item => item.id !== itemId);
    saveCartToStorage();
    updateCartCount();
}

function updateCartQuantity(itemId, quantity) {
    const item = appState.cart.find(item => item.id === itemId);
    if (item) {
        item.quantity = Math.max(1, parseInt(quantity));
        saveCartToStorage();
        updateCartCount();
    }
}

function saveCartToStorage() {
    try {
        localStorage.setItem('cart', JSON.stringify(appState.cart));
    } catch (e) {
        console.error('Error saving cart:', e);
    }
}

function loadCartFromStorage() {
    try {
        const saved = localStorage.getItem('cart');
        if (saved) {
            appState.cart = JSON.parse(saved);
        }
    } catch (e) {
        console.error('Error loading cart:', e);
        appState.cart = [];
    }
}

function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        const count = appState.cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = count;
    }
}

function getCartTotal() {
    return appState.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
}

function getCartItems() {
    return appState.cart;
}

function clearCart() {
    appState.cart = [];
    saveCartToStorage();
    updateCartCount();
}

// ========== NOTIFICATIONS ==========
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        background-color: ${getNotificationColor(type)};
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 9999;
        animation: slideInRight 0.3s ease;
        max-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function getNotificationColor(type) {
    const colors = {
        success: '#27AE60',
        error: '#E74C3C',
        warning: '#F39C12',
        info: '#E31E24'
    };
    return colors[type] || colors.info;
}

// ========== FORM HANDLING ==========
function setupFormValidation(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.addEventListener('submit', (e) => {
        if (!validateForm(form)) {
            e.preventDefault();
            showNotification('Please fill in all required fields', 'error');
        }
    });
}

function validateForm(form) {
    let isValid = true;
    
    form.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });
    
    return isValid;
}

// ========== UTILITY FUNCTIONS ==========
function formatPrice(price) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price);
}

function getDateString(date) {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function trackEvent(eventName, eventData = {}) {
    // Analytics tracking
    console.log('Event:', eventName, eventData);
    // Could be connected to Google Analytics or other services
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ========== FETCH HELPER ==========
async function fetchAPI(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        showNotification('An error occurred. Please try again.', 'error');
        return null;
    }
}

// ========== CSS ANIMATIONS ==========
const styles = document.createElement('style');
styles.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
    
    header {
        transition: transform 0.3s ease;
    }
    
    .fade-in {
        opacity: 1 !important;
    }
`;
document.head.appendChild(styles);

// ========== EXPORT FOR GLOBAL USE ==========
window.app = {
    addToCart,
    removeFromCart,
    updateCartQuantity,
    getCartItems,
    getCartTotal,
    clearCart,
    showNotification,
    formatPrice,
    fetchAPI,
    trackEvent
};
