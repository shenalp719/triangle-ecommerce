<?php
/**
 * Shopping Cart Page - Triangle Printing Solutions
 */
session_start();
require_once 'db.php';

$page_title = 'Shopping Cart';
include 'includes/header.php';
?>

    <section class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
        <h1 style="margin-bottom: 2rem;">Shopping Cart</h1>

        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
            <div>
                <div id="cart-items" class="grid" style="grid-template-columns: 1fr;">
                    </div>
            </div>

            <div style="height: fit-content; position: sticky; top: 6rem;">
                <div class="cart-summary">
                    <h3 style="margin-bottom: 1.5rem;">Order Summary</h3>
                    
                    <div class="summary-row">
                        <span class="summary-label">Subtotal:</span>
                        <span class="summary-value" id="subtotal">$0.00</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Shipping:</span>
                        <span class="summary-value" id="shipping">$0.00</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Tax (8%):</span>
                        <span class="summary-value" id="tax">$0.00</span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="total">$0.00</span>
                    </div>
                    
                    <button class="btn btn-primary btn-block" style="margin-top: 1.5rem; padding: 1rem;" onclick="startStripeCheckout()">
                        Proceed to Checkout
                    </button>
                    
                    <a href="products.php" class="btn btn-secondary btn-block" style="margin-top: 0.75rem; padding: 1rem;">
                        Continue Shopping
                    </a>
                    
                    <p style="font-size: 0.85rem; color: var(--text-light); margin-top: 1rem; text-align: center;">
                        Free shipping on orders over $150
                    </p>
                </div>

                <div class="card" style="margin-top: 1rem;">
                    <div class="card-body" style="text-align: center;">
                        <h5 style="margin-bottom: 1rem;">Need Help?</h5>
                        <button class="btn btn-dark btn-sm btn-block" style="margin-bottom: 0.5rem;" onclick="document.getElementById('chatbot-toggle').click()">
                            Chat with us
                        </button>
                        <a href="contact.php" class="btn btn-dark btn-sm btn-block">
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        @media (max-width: 767px) {
            .container > div {
                grid-template-columns: 1fr !important;
            }
            
            .cart-summary {
                position: static;
                margin-top: 2rem;
            }
        }
    </style>

    <script>
        async function startStripeCheckout() {
            // 1. Get items from your app.js global state
            const cartItems = app.getCartItems();
            
            if (cartItems.length === 0) {
                app.showNotification('Your cart is empty!', 'warning');
                return;
            }

            // 2. Calculate the exact grand total (including tax and shipping)
            const subtotal = app.getCartTotal();
            const tax = subtotal * 0.08; // 8% tax
            const shipping = subtotal > 150 ? 0 : 15; // Free shipping over $150
            const grandTotal = subtotal + tax + shipping;

            // 3. Update UI to show loading state
            const btn = document.querySelector('button[onclick="startStripeCheckout()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Redirecting to Secure Checkout...';
            btn.disabled = true;
            btn.style.backgroundColor = '#666';

            try {
                // 4. Send the total to your backend
                const response = await fetch('checkout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        total: grandTotal,
                        itemCount: cartItems.length
                    })
                });

                const session = await response.json();

                if (session.error) {
                    app.showNotification('Error: ' + session.error, 'error');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    btn.style.backgroundColor = 'var(--primary)';
                } else {
                    // 5. Redirect to Stripe!
                    window.location.href = session.url;
                }
            } catch (error) {
                console.error("Error:", error);
                app.showNotification('Checkout failed. Please try again.', 'error');
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.style.backgroundColor = 'var(--primary)';
            }
        }
    </script>

<?php include 'includes/footer.php'; ?>