<?php
/**
 * Home Page - Triangle Printing Solutions
 */
session_start();
require_once 'db.php';

$page_title = 'Home';
include 'includes/header.php';
?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <h1>Customize Your <span class="hero-highlight">Print Products</span></h1>
            <p>Professional Web-to-Print Solutions for Every Need</p>
            <div class="hero-actions">
                <a href="products.php" class="btn btn-primary btn-lg">Browse Products</a>
                <a href="customizer-frame.php" class="btn btn-secondary btn-lg">Start Customizing</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container" style="margin-bottom: 6rem;">
        <h2 style="text-align: center; margin-bottom: 3rem;">Why Choose Triangle?</h2>
        
        <div class="grid grid-3">
            <div class="card">
                <div class="card-body" style="text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🎨</div>
                    <h4>Professional Design Tools</h4>
                    <p>Intuitive customization engine with real-time preview and HD resolution support.</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body" style="text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">⚡</div>
                    <h4>Fast & Reliable</h4>
                    <p>Standard 5-7 day delivery or express 2-3 days. All orders quality-checked before shipping.</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body" style="text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">💰</div>
                    <h4>Competitive Pricing</h4>
                    <p>Fair prices with bulk discounts. Free shipping on orders over $150.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="container" style="margin-bottom: 6rem;">
        <h2 style="text-align: center; margin-bottom: 3rem;">Featured Products</h2>
        
        <div class="grid grid-4">
            <div class="product-card">
                <div style="height: 250px; background: linear-gradient(135deg, #E31E24, #111111); display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">🖼️</div>
                <div class="product-info">
                    <div class="product-category">Frames</div>
                    <h4 class="product-name">Frame Posters</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Upload your image and create a beautiful framed poster.</p>
                    <div class="product-price">From $25</div>
                    <a href="customizer-frame.php" class="btn btn-primary btn-sm btn-block">Customize Now</a>
                </div>
            </div>
            
            <div class="product-card">
                <div style="height: 250px; background: linear-gradient(135deg, #F5F5F5, #777777); display: flex; align-items: center; justify-content: center; color: #E31E24; font-size: 4rem;">☕</div>
                <div class="product-info">
                    <div class="product-category">Drinkware</div>
                    <h4 class="product-name">Custom Mugs</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Add photos, text, or designs to create personalized mugs.</p>
                    <div class="product-price">From $12</div>
                    <a href="customizer-product.php?type=mug" class="btn btn-primary btn-sm btn-block">Customize Now</a>
                </div>
            </div>
            
            <div class="product-card">
                <div style="height: 250px; background: linear-gradient(135deg, #E31E24, #F5F5F5); display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">👕</div>
                <div class="product-info">
                    <div class="product-category">Apparel</div>
                    <h4 class="product-name">T-Shirts</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Custom printed t-shirts with your designs and artwork.</p>
                    <div class="product-price">From $18</div>
                    <a href="customizer-product.php?type=shirt" class="btn btn-primary btn-sm btn-block">Customize Now</a>
                </div>
            </div>
            
            <div class="product-card">
                <div style="height: 250px; background: linear-gradient(135deg, #111111, #E31E24); display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">🧢</div>
                <div class="product-info">
                    <div class="product-category">Headwear</div>
                    <h4 class="product-name">Custom Caps</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Embroidered or printed caps for your brand or event.</p>
                    <div class="product-price">From $15</div>
                    <a href="customizer-product.php?type=cap" class="btn btn-primary btn-sm btn-block">Customize Now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section style="background-color: var(--light-gray); padding: 4rem 2rem; margin-bottom: 6rem;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 3rem;">Our Simple Process</h2>
            
            <div class="grid grid-4" style="text-align: center;">
                <div>
                    <div style="width: 80px; height: 80px; background-color: var(--primary-red); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; margin: 0 auto 1rem;">1</div>
                    <h4>Design</h4>
                    <p>Use our design tools to customize your product exactly how you want it.</p>
                </div>
                
                <div>
                    <div style="width: 80px; height: 80px; background-color: var(--primary-red); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; margin: 0 auto 1rem;">2</div>
                    <h4>Review</h4>
                    <p>Check your HD preview and ensure everything is perfect.</p>
                </div>
                
                <div>
                    <div style="width: 80px; height: 80px; background-color: var(--primary-red); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; margin: 0 auto 1rem;">3</div>
                    <h4>Order</h4>
                    <p>Add to cart, checkout, and make payment securely.</p>
                </div>
                
                <div>
                    <div style="width: 80px; height: 80px; background-color: var(--primary-red); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; margin: 0 auto 1rem;">4</div>
                    <h4>Receive</h4>
                    <p>Get your professionally printed product within 5-7 business days.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="container" style="text-align: center; margin-bottom: 6rem;">
        <h2>Ready to Get Started?</h2>
        <p style="font-size: 1.125rem; margin-bottom: 2rem;">Create your custom print product today and bring your vision to life.</p>
        <a href="customizer-frame.php" class="btn btn-primary btn-lg">Launch Customizer</a>
    </section>

<?php include 'includes/footer.php'; ?>
