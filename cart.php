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
        const items = appState.cart;
        
        if (items.length === 0) {
            alert('Your cart is empty');
            return;
        }
        
        // 1. Do the math so checkout.php doesn't crash
        const subtotal = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const shipping = subtotal > 0 ? 15 : 0;
        const tax = subtotal * 0.08;
        const finalTotal = subtotal + shipping + tax;
        
        // 2. Package it perfectly
        const orderData = {
            items: items,
            total: finalTotal,
            itemCount: items.reduce((sum, item) => sum + item.quantity, 0)
        };
        
        try {
            // Change button text
            const checkoutBtn = document.querySelector('.checkout-btn');
            if (checkoutBtn) checkoutBtn.innerText = 'Connecting...';

            // 3. Send it to checkout.php WITH credentials!
            const response = await fetch('/triangle-ecommerce/checkout.php', {
                method: 'POST',
                credentials: 'same-origin', // The magic handshake
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(orderData)
            });
            
            const result = await response.json();
            
            if (result.url) {
                window.location.href = result.url;
            } else {
                console.error("Server Error:", result);
                alert('Error: ' + (result.error || 'Could not reach Stripe.'));
                if (checkoutBtn) checkoutBtn.innerText = 'Proceed to Checkout';
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            alert('Network error. Please try again.');
        }
    }
    </script>

<?php include 'includes/footer.php'; ?>