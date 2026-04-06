/**
 * Shopping Cart Management
 * Triangle Printing Solutions
 */

document.addEventListener('DOMContentLoaded', () => {
    renderCart();
    attachCartEventListeners();
});

// ========== RENDER CART ==========
function renderCart() {
    const cartContainer = document.getElementById('cart-items');
    if (!cartContainer) return;
    
    const items = appState.cart;
    
    if (items.length === 0) {
        cartContainer.innerHTML = `
            <div class="empty-cart" style="text-align: center; padding: 3rem; grid-column: 1/-1;">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="color: var(--light-gray); margin-bottom: 1rem;">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <h3>Your cart is empty</h3>
                <p style="color: var(--text-light); margin-bottom: 1.5rem;">Start customizing your products to add items to cart</p>
                <a href="/triangle-ecommerce/products.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        `;
        return;
    }
    
    const html = items.map(item => `
        <div class="cart-item item-${item.id}">
            <div class="item-image">
                ${item.image ? `<img src="${item.image}" alt="${item.name}">` : '<div style="width: 100%; height: 120px; background-color: var(--light-gray); display: flex; align-items: center; justify-content: center;"><span style="color: var(--medium-gray);">No image</span></div>'}
            </div>
            <div class="item-details">
                <h4>${item.name}</h4>
                <p class="item-price">${app.formatPrice(item.price)}</p>
            </div>
            <div class="item-quantity">
                <button class="qty-btn qty-minus" data-id="${item.id}">-</button>
                <input type="number" name="quantity" value="${item.quantity}" class="qty-input" data-id="${item.id}" min="1">
                <button class="qty-btn qty-plus" data-id="${item.id}">+</button>
            </div>
            <div class="item-total">
                <div class="total-price">${app.formatPrice(item.price * item.quantity)}</div>
            </div>
            <button class="item-remove" data-id="${item.id}" title="Remove item">×</button>
        </div>
    `).join('');
    
    cartContainer.innerHTML = html;
    updateCartSummary();
}

// ========== UPDATE CART SUMMARY ==========
function updateCartSummary() {
    const subtotal = appState.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const shipping = subtotal > 0 ? 15 : 0;
    const tax = subtotal * 0.08; // 8% tax
    const total = subtotal + shipping + tax;
    
    const summaryElements = {
        subtotal: document.getElementById('subtotal'),
        shipping: document.getElementById('shipping'),
        tax: document.getElementById('tax'),
        total: document.getElementById('total'),
        itemCount: document.getElementById('item-count')
    };
    
    if (summaryElements.subtotal) {
        summaryElements.subtotal.textContent = app.formatPrice(subtotal);
    }
    if (summaryElements.shipping) {
        summaryElements.shipping.textContent = app.formatPrice(shipping);
    }
    if (summaryElements.tax) {
        summaryElements.tax.textContent = app.formatPrice(tax);
    }
    if (summaryElements.total) {
        summaryElements.total.textContent = app.formatPrice(total);
    }
    if (summaryElements.itemCount) {
        const count = appState.cart.reduce((sum, item) => sum + item.quantity, 0);
        summaryElements.itemCount.textContent = count;
    }
}

// ========== EVENT LISTENERS ==========
function attachCartEventListeners() {
    const cartContainer = document.getElementById('cart-items');
    if (!cartContainer) return;
    
    // Remove buttons
    cartContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('item-remove')) {
            const itemId = e.target.dataset.id;
            app.removeFromCart(itemId);
            e.target.closest('.cart-item').style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => renderCart(), 300);
        }
    });
    
    // Quantity minus buttons
    cartContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('qty-minus')) {
            const itemId = e.target.dataset.id;
            const item = appState.cart.find(i => i.id === itemId);
            if (item && item.quantity > 1) {
                app.updateCartQuantity(itemId, item.quantity - 1);
                renderCart();
            }
        }
    });
    
    // Quantity plus buttons
    cartContainer.addEventListener('click', (e) => {
        if (e.target.classList.contains('qty-plus')) {
            const itemId = e.target.dataset.id;
            const item = appState.cart.find(i => i.id === itemId);
            if (item) {
                app.updateCartQuantity(itemId, item.quantity + 1);
                renderCart();
            }
        }
    });
    
    // Quantity input change
    cartContainer.addEventListener('change', (e) => {
        if (e.target.classList.contains('qty-input')) {
            const itemId = e.target.dataset.id;
            const quantity = parseInt(e.target.value) || 1;
            app.updateCartQuantity(itemId, quantity);
            renderCart();
        }
    });
}

// ========== CART STYLES ==========
const cartStyles = document.createElement('style');
cartStyles.textContent = `
    .cart-item {
        display: grid;
        grid-template-columns: 120px 1fr 140px 120px 40px;
        gap: 1.5rem;
        align-items: center;
        padding: 1.5rem;
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        transition: var(--transition);
    }
    
    .cart-item:hover {
        box-shadow: var(--shadow-light);
    }
    
    .item-image {
        width: 120px;
        height: 120px;
        border-radius: 0.5rem;
        overflow: hidden;
        background-color: var(--light-gray);
    }
    
    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .item-details h4 {
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }
    
    .item-price {
        font-weight: 600;
        color: var(--primary-red);
        margin: 0;
    }
    
    .item-quantity {
        display: flex;
        align-items: center;
        border: 1px solid var(--border-color);
        border-radius: 0.25rem;
        overflow: hidden;
    }
    
    .qty-btn {
        background-color: var(--light-gray);
        border: none;
        width: 36px;
        height: 36px;
        cursor: pointer;
        font-weight: 600;
        color: var(--text-dark);
        transition: var(--transition);
    }
    
    .qty-btn:hover {
        background-color: var(--primary-red);
        color: var(--white);
    }
    
    .qty-input {
        width: 60px;
        border: none;
        text-align: center;
        font-weight: 600;
        padding: 0.5rem;
    }
    
    .qty-input::-webkit-outer-spin-button,
    .qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    
    .qty-input[type=number] {
        -moz-appearance: textfield;
    }
    
    .item-total {
        text-align: right;
    }
    
    .total-price {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-red);
    }
    
    .item-remove {
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--danger);
        cursor: pointer;
        transition: var(--transition);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .item-remove:hover {
        transform: scale(1.2);
        color: #c0392b;
    }
    
    .empty-cart {
        grid-column: 1 / -1;
    }
    
    .cart-summary {
        background-color: var(--light-gray);
        padding: 2rem;
        border-radius: 0.5rem;
        margin-top: 2rem;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    .summary-row:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    
    .summary-row.total {
        font-weight: 700;
        font-size: 1.25rem;
        color: var(--primary-red);
        border-top: 2px solid var(--primary-red);
        padding-top: 1rem;
        border-bottom: none;
    }
    
    .summary-label {
        font-weight: 500;
    }
    
    .summary-value {
        font-weight: 600;
    }
    
    @media (max-width: 767px) {
        .cart-item {
            grid-template-columns: 100px 1fr 40px;
            gap: 1rem;
            padding: 1rem;
        }
        
        .item-image {
            width: 100px;
            height: 100px;
        }
        
        .item-quantity,
        .item-total {
            display: none;
        }
        
        .item-remove {
            width: 30px;
            height: 30px;
            font-size: 1.25rem;
        }
    }
    
    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateX(-20px);
        }
    }
`;
document.head.appendChild(cartStyles);

// ========== API FUNCTIONS ==========
async function proceedToCheckout() {
    const items = appState.cart;
    
    if (items.length === 0) {
        app.showNotification('Your cart is empty', 'warning');
        return;
    }
    
    // Prepare order data
    const orderData = {
        items: items,
        subtotal: items.reduce((sum, item) => sum + (item.price * item.quantity), 0)
    };
    
    // Send to server
    const result = await app.fetchAPI('/triangle-ecommerce/api/create-order.php', {
        method: 'POST',
        body: JSON.stringify(orderData)
    });
    
    if (result && result.success) {
        app.clearCart();
        window.location.href = `/triangle-ecommerce/checkout.php?order=${result.orderId}`;
    }
}

// Make functions globally available
window.proceedToCheckout = proceedToCheckout;
