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

                <div class="summary-card" style="background: #2a2a2a; padding: 2rem; border-radius: 8px;">
                    <h2 style="margin-top: 0; margin-bottom: 1.5rem;">Order Summary</h2>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span>Subtotal:</span>
                        <strong id="summary-subtotal">LKR 0.00</strong>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                        <span>Tax (8%):</span>
                        <strong id="summary-tax">LKR 0.00</strong>
                    </div>
                    
                    <hr style="border: 0; border-top: 1px solid #444; margin: 1.5rem 0;">
                    
                    <div style="display: flex; justify-content: space-between; margin-bottom: 2rem; color: #E31E24; font-size: 1.25rem;">
                        <strong>Total:</strong>
                        <strong id="summary-total">LKR 0.00</strong>
                    </div>
                    
                    <button onclick="app.proceedToCheckout()" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem; padding: 1rem; font-weight: bold;">Proceed to Checkout</button>
                    <a href="products.php" class="btn" style="display: block; text-align: center; width: 100%; background: transparent; border: 1px solid #555; color: white; padding: 1rem; box-sizing: border-box;">Continue Shopping</a>
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