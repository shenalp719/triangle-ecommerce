<?php
/**
 * Products Page - Triangle Printing Solutions
 */
session_start();
require_once 'db.php';

$page_title = 'Products';
include 'includes/header.php';
?>

    <section class="container">
        <div style="margin-bottom: 3rem;">
            <h1 style="margin-bottom: 1rem;">Our Products</h1>
            <p style="color: var(--text-light);">Choose from our range of high-quality printable products</p>
        </div>

        <div class="filters mb-3">
            <button class="filter-btn active" data-category="all">All Products</button>
            <button class="filter-btn" data-category="frames">Frames</button>
            <button class="filter-btn" data-category="drinkware">Drinkware</button>
            <button class="filter-btn" data-category="apparel">Apparel</button>
            <button class="filter-btn" data-category="headwear">Headwear</button>
        </div>

        <div class="grid grid-4" id="products-grid">
            
            <div class="product-card"  data-category="frames">
                <div style="height: 250px; background-image: url('https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=800&auto=format&fit=crop'); background-size: cover; background-position: center; border-radius: 8px 8px 0 0;"></div>
                <div class="product-info">
                    <div class="product-category">Frames</div>
                    <h4 class="product-name">Frame Posters (8x10)</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Premium framed poster with your custom image.</p>
                    <div class="product-price">$25.00</div>
                    <button class="btn btn-primary btn-sm btn-block" onclick="app.addToCart(1, 'Frame Poster 8x10', 25, 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=800&auto=format&fit=crop'); window.location.href='customizer-frame.php'">Customize</button>
                </div>
            </div>

            <div class="product-card" data-category="frames">
                <div style="height: 250px; background-image: url('https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=800&auto=format&fit=crop'); background-size: cover; background-position: center; border-radius: 8px 8px 0 0;"></div>
                <div class="product-info">
                    <div class="product-category">Frames</div>
                    <h4 class="product-name">Frame Posters (16x20)</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Large premium framed poster with your custom image.</p>
                    <div class="product-price">$45.00</div>
                    <button class="btn btn-primary btn-sm btn-block" onclick="app.addToCart(2, 'Frame Poster 16x20', 45, 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=800&auto=format&fit=crop'); window.location.href='customizer-frame.php'">Customize</button>
                </div>
            </div>

            <div class="product-card" data-category="drinkware">
                <div style="height: 250px; background-image: url('https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?q=80&w=800&auto=format&fit=crop'); background-size: cover; background-position: center; border-radius: 8px 8px 0 0;"></div>
                <div class="product-info">
                    <div class="product-category">Drinkware</div>
                    <h4 class="product-name">11oz Coffee Mug</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Classic ceramic mug perfect for hot beverages.</p>
                    <div class="product-price">$12.00</div>
                    <button class="btn btn-primary btn-sm btn-block" onclick="app.addToCart(3, 'Coffee Mug 11oz', 12, 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?q=80&w=800&auto=format&fit=crop'); window.location.href='customizer-mug.php?type=mug'">Customize</button>
                </div>
            </div>

            <div class="product-card" data-category="drinkware">
                <div style="height: 250px; background-image: url('https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?q=80&w=800&auto=format&fit=crop'); background-size: cover; background-position: center; border-radius: 8px 8px 0 0;"></div>
                <div class="product-info">
                    <div class="product-category">Drinkware</div>
                    <h4 class="product-name">15oz Coffee Mug</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Large ceramic mug for maximum coffee enjoyment.</p>
                    <div class="product-price">$15.00</div>
                    <button class="btn btn-primary btn-sm btn-block" onclick="app.addToCart(4, 'Coffee Mug 15oz', 15, 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?q=80&w=800&auto=format&fit=crop'); window.location.href='customizer-mug.php?type=mug'">Customize</button>
                </div>
            </div>

            <div class="product-card" data-category="apparel">
                <div style="height: 250px; background-image: url('https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?q=80&w=800&auto=format&fit=crop'); background-size: cover; background-position: center; border-radius: 8px 8px 0 0;"></div>
                <div class="product-info">
                    <div class="product-category">Apparel</div>
                    <h4 class="product-name">100% Cotton T-Shirt</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Premium comfort fit cotton t-shirt in multiple colors.</p>
                    <div class="product-price">$18.00</div>
                    <button class="btn btn-primary btn-sm btn-block" onclick="app.addToCart(5, 'T-Shirt Cotton 100%', 18, 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?q=80&w=800&auto=format&fit=crop'); window.location.href='customizer-shirt.php?type=shirt'">Customize</button>
                </div>
            </div>

            <div class="product-card" data-category="apparel">
                <div style="height: 250px; background-image: url('https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?q=80&w=800&auto=format&fit=crop'); background-size: cover; background-position: center; border-radius: 8px 8px 0 0;"></div>
                <div class="product-info">
                    <div class="product-category">Apparel</div>
                    <h4 class="product-name">Poly-Blend T-Shirt</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Durable poly-blend t-shirt that lasts longer.</p>
                    <div class="product-price">$16.00</div>
                    <button class="btn btn-primary btn-sm btn-block" onclick="app.addToCart(6, 'T-Shirt Poly-Blend', 16, 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?q=80&w=800&auto=format&fit=crop'); window.location.href='customizer-shirt.php?type=shirt'">Customize</button>
                </div>
            </div>

            <div class="product-card" data-category="headwear">
                <div style="height: 250px; background-image: url('https://images.unsplash.com/photo-1588850561407-ed78c282e89b?q=80&w=800&auto=format&fit=crop'); background-size: cover; background-position: center; border-radius: 8px 8px 0 0;"></div>
                <div class="product-info">
                    <div class="product-category">Headwear</div>
                    <h4 class="product-name">Baseball Cap</h4>
                    <h4 class="product-name">Baseball Cap</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Classic baseball cap with full customization options.</p>
                    <div class="product-price">$15.00</div>
                    <button class="btn btn-primary btn-sm btn-block" onclick="app.addToCart(7, 'Baseball Cap', 15, 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?q=80&w=800&auto=format&fit=crop'); window.location.href='customizer-cap.php?type=cap'">Customize</button>
                </div>
            </div>

            <div class="product-card" data-category="headwear">
                <div style="height: 250px; background-image: url('https://images.unsplash.com/photo-1588850561407-ed78c282e89b?q=80&w=800&auto=format&fit=crop'); background-size: cover; background-position: center; border-radius: 8px 8px 0 0;"></div>
                <div class="product-info">
                    <div class="product-category">Headwear</div>
                    <h4 class="product-name">Snapback Cap</h4>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Trendy snapback cap with adjustable sizing.</p>
                    <div class="product-price">$18.00</div>
                    <button class="btn btn-primary btn-sm btn-block" onclick="app.addToCart(8, 'Snapback Cap', 18, 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?q=80&w=800&auto=format&fit=crop'); window.location.href='customizer-cap.php?type=cap'">Customize</button>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Product filtering logic
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const category = this.dataset.category;
                const cards = document.querySelectorAll('.product-card');
                
                cards.forEach(card => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.style.display = 'block';
                        setTimeout(() => card.style.opacity = '1', 50);
                    } else {
                        card.style.opacity = '0';
                        setTimeout(() => card.style.display = 'none', 300);
                    }
                });
            });
        });
    </script>

<?php include 'includes/footer.php'; ?>