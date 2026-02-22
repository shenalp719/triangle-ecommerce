<?php
/**
 * Product Customizer - Mug, T-Shirt, Cap
 * Triangle Printing Solutions
 */
session_start();
require_once 'db.php';

$type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'mug';
$page_title = ucfirst($type) . ' Customizer';

// Product configuration
$products = [
    'mug' => [
        'name' => 'Custom Mug',
        'price' => 12,
        'colors' => ['white', 'black', 'red', 'blue', 'green'],
        'sizes' => ['11oz', '15oz'],
        'description' => 'Create your perfect custom mug with text and images'
    ],
    'shirt' => [
        'name' => 'T-Shirt',
        'price' => 18,
        'colors' => ['white', 'navy', 'red', 'black', 'gray'],
        'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        'description' => 'Design your custom printed t-shirt'
    ],
    'cap' => [
        'name' => 'Custom Cap',
        'price' => 15,
        'colors' => ['navy', 'black', 'red', 'white', 'khaki'],
        'sizes' => 'One Size',
        'description' => 'Create your personalized embroidered cap'
    ]
];

$product = $products[$type] ?? $products['mug'];

include 'includes/header.php';
?>

    <section style="background-color: var(--light-gray); padding: 2rem; margin-bottom: 0;">
        <div class="container-md">
            <h1 style="margin-bottom: 0.5rem;"><?php echo $product['name']; ?> Customizer</h1>
            <p style="color: var(--text-light);"><?php echo $product['description']; ?></p>
        </div>
    </section>

    <section style="display: flex; gap: 2rem; padding: 2rem; max-width: 1400px; margin: 0 auto;">
        <!-- Left Panel - Tools -->
        <div style="width: 280px; background-color: var(--white); border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.5rem; height: fit-content;">
            <h4 style="margin-bottom: 1.5rem;">Customization</h4>

            <!-- Product Color -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Product Color</label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                    <?php foreach ($product['colors'] as $color): ?>
                        <button class="color-btn" data-color="<?php echo $color; ?>" style="width: 100%; height: 50px; border-radius: 0.5rem; border: 3px solid transparent; cursor: pointer; background-color: <?php echo $color; ?>; transition: border-color 0.3s;" title="<?php echo ucfirst($color); ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Text Editor -->
            <div style="margin-top: 1.5rem; padding: 1rem; background-color: var(--light-gray); border-radius: 0.5rem;">
                <h5 style="margin-bottom: 1rem;">Add Text</h5>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem;">Text Content</label>
                    <textarea id="text-content" placeholder="Enter your text..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; resize: vertical; font-family: 'Inter', sans-serif; font-size: 0.85rem; min-height: 60px;"></textarea>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem;">Font Family</label>
                    <select id="text-font" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.25rem; font-size: 0.85rem;">
                        <option value="Arial">Arial</option>
                        <option value="Helvetica">Helvetica</option>
                        <option value="Courier New">Courier New</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Verdana">Verdana</option>
                        <option value="Times New Roman">Times New Roman</option>
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem;">Size</label>
                    <input type="range" id="text-size" min="8" max="72" value="24" style="width: 100%; cursor: pointer;">
                    <span style="font-size: 0.75rem; color: var(--text-light);">
                        <span id="size-value">24</span>px
                    </span>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem;">Color</label>
                    <input type="color" id="text-color" value="#000000" style="width: 100%; height: 40px; cursor: pointer; border: none; border-radius: 0.5rem;">
                </div>

                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                    <button id="text-bold" data-option="bold" style="flex: 1; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.25rem; background-color: var(--white); cursor: pointer; font-weight: 700;" title="Bold">B</button>
                    <button id="text-italic" data-option="italic" style="flex: 1; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.25rem; background-color: var(--white); cursor: pointer; font-style: italic;" title="Italic">I</button>
                </div>

                <?php if ($type === 'mug'): ?>
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem;">
                            <input type="checkbox" id="curved-text"> Curved Text
                        </label>
                        <small style="color: var(--text-light);">Wraps text around the mug</small>
                    </div>
                <?php endif; ?>

                <button style="width: 100%; padding: 0.75rem; background-color: var(--primary-red); color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;" id="add-text">
                    Add Text
                </button>
            </div>

            <!-- Image Upload -->
            <div style="margin-top: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Add Image</label>
                <input type="file" id="product-image-upload" accept="image/*" style="width: 100%; padding: 0.75rem; border: 2px dashed var(--primary-red); border-radius: 0.5rem; cursor: pointer;">
                <small style="color: var(--text-light);">JPG, PNG (max 5MB)</small>
            </div>

            <!-- Size Selection -->
            <?php if (!empty($product['sizes']) && $product['sizes'] !== 'One Size'): ?>
                <div style="margin-top: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Size</label>
                    <select id="product-size" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                        <?php foreach ($product['sizes'] as $size): ?>
                            <option value="<?php echo $size; ?>"><?php echo $size; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Layers -->
            <div style="margin-top: 1.5rem; padding: 1rem; background-color: var(--light-gray); border-radius: 0.5rem;">
                <h5 style="margin-bottom: 1rem;">Layers</h5>
                <div id="layers-list" style="max-height: 150px; overflow-y: auto;">
                    <!-- Populated dynamically -->
                </div>
                <button style="width: 100%; padding: 0.5rem; margin-top: 1rem; background-color: var(--dark-gray); color: white; border: none; border-radius: 0.25rem; cursor: pointer; font-size: 0.85rem;" id="reset-product">
                    Reset All
                </button>
            </div>
        </div>

        <!-- Center - Preview -->
        <div style="flex: 1; background-color: var(--light-gray); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; min-height: 500px; position: relative;">
            <div id="product-preview" style="width: 300px; height: 300px; background: white; border-radius: 0.75rem; box-shadow: 0 10px 30px rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                <?php if ($type === 'mug'): ?>
                    <div style="font-size: 5rem; transform: perspective(600px) rotateY(-15deg);">☕</div>
                <?php elseif ($type === 'shirt'): ?>
                    <div style="font-size: 5rem;">👕</div>
                <?php else: ?>
                    <div style="font-size: 5rem;">🧢</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Panel - Options & Add To Cart -->
        <div style="width: 300px; background-color: var(--white); border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.5rem; height: fit-content;">
            <h4 style="margin-bottom: 1.5rem;">Order Details</h4>

            <!-- Product Info -->
            <div style="margin-bottom: 1.5rem; padding: 1rem; background-color: var(--light-gray); border-radius: 0.5rem;">
                <small style="color: var(--text-light);">Product</small>
                <div style="font-weight: 600; margin-bottom: 0.5rem;"><?php echo $product['name']; ?></div>
                <small style="color: var(--text-light);">Base Price</small>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-red);">$<?php echo number_format($product['price'], 2); ?></div>
            </div>

            <!-- Additional Options -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Quantity</label>
                <div style="display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 0.5rem; overflow: hidden;">
                    <button class="qty-adjust-prod" data-value="-1" style="flex: 0 0 40px; height: 40px; background-color: var(--light-gray); border: none; cursor: pointer;">−</button>
                    <input type="number" id="product-quantity" value="1" min="1" style="flex: 1; border: none; text-align: center; font-weight: 600;"/>
                    <button class="qty-adjust-prod" data-value="+1" style="flex: 0 0 40px; height: 40px; background-color: var(--light-gray); border: none; cursor: pointer;">+</button>
                </div>
            </div>

            <!-- Total Price -->
            <div style="background-color: var(--light-gray); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
                <small style="color: var(--text-light);">Total Price</small>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary-red);" id="product-total">$<?php echo number_format($product['price'], 2); ?></div>
            </div>

            <!-- Add to Cart Button -->
            <button style="width: 100%; padding: 1rem; background-color: var(--primary-red); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer;margin-bottom: 0.75rem;" id="add-product-cart">
                Add to Cart
            </button>

            <!-- Save Design -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <button style="width: 100%; padding: 0.75rem; background-color: var(--light-gray); color: var(--text-dark); border: 2px solid var(--primary-red); border-radius: 0.5rem; font-weight: 600; cursor: pointer;" id="save-product-design">
                    Save Design
                </button>
            <?php else: ?>
                <a href="login.php" style="display: block; text-align: center; padding: 0.75rem; background-color: var(--light-gray); color: var(--text-dark); border: 2px solid var(--primary-red); border-radius: 0.5rem; font-weight: 600; text-decoration: none;">
                    Login to Save
                </a>
            <?php endif; ?>
        </div>
    </section>

    <script>
        const productType = '<?php echo $type; ?>';
        const basePrice = <?php echo $product['price']; ?>;

        // Quantity adjustment
        document.querySelectorAll('.qty-adjust-prod').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById('product-quantity');
                const change = parseInt(btn.dataset.value);
                input.value = Math.max(1, parseInt(input.value) + change);
                updateProductTotal();
            });
        });

        function updateProductTotal() {
            const quantity = parseInt(document.getElementById('product-quantity').value) || 1;
            const total = basePrice * quantity;
            document.getElementById('product-total').textContent = '$' + total.toFixed(2);
        }

        // Add to cart
        document.getElementById('add-product-cart').addEventListener('click', () => {
            const quantity = parseInt(document.getElementById('product-quantity').value) || 1;
            const price = basePrice;
            
            for (let i = 0; i < quantity; i++) {
                app.addToCart(
                    productType + '-' + Date.now() + '-' + i,
                    '<?php echo $product['name']; ?>',
                    price,
                    null
                );
            }
            
            app.showNotification(quantity + ' item(s) added to cart!', 'success');
            document.getElementById('product-quantity').value = 1;
            updateProductTotal();
        });

        // Text styling
        document.getElementById('text-size').addEventListener('input', (e) => {
            document.getElementById('size-value').textContent = e.target.value;
        });

        document.getElementById('add-text').addEventListener('click', () => {
            const text = document.getElementById('text-content').value;
            if (!text.trim()) {
                app.showNotification('Please enter text', 'warning');
                return;
            }
            
            const layer = document.createElement('div');
            layer.style.padding = '0.5rem';
            layer.style.marginBottom = '0.5rem';
            layer.style.background = 'var(--light-gray)';
            layer.style.borderRadius = '0.25rem';
            layer.style.fontSize = '0.85rem';
            layer.textContent = text.substring(0, 20) + (text.length > 20 ? '...' : '');
            
            document.getElementById('layers-list').appendChild(layer);
            document.getElementById('text-content').value = '';
            
            app.showNotification('Text added!', 'success');
        });

        document.getElementById('reset-product').addEventListener('click', () => {
            if (confirm('Reset all customizations?')) {
                document.getElementById('layers-list').innerHTML = '';
                document.getElementById('text-content').value = '';
                app.showNotification('Design reset!', 'info');
            }
        });
    </script>

    <style>
        @media (max-width: 1024px) {
            section > div {
                flex-direction: column;
                gap: 1.5rem;
            }

            section > div > div {
                width: 100% !important;
                height: auto !important;
            }
        }
    </style>

<?php include 'includes/footer.php'; ?>
