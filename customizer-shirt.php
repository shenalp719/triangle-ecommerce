<?php
/**
 * Product Customizer - Mug, T-Shirt, Cap with 3D Preview
 * Triangle Printing Solutions
 */
session_start();
require_once 'db.php';

$type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'mug';
$page_title = ucfirst($type) . ' Customizer';

// Product configuration with hex colors
$products = [
    'mug' => [
        'name' => 'Custom Mug',
        'price' => 12,
        'colors' => ['#FFFFFF', '#000000', '#E31E24', '#0066CC', '#00AA00'],
        'colorNames' => ['White', 'Black', 'Red', 'Blue', 'Green'],
        'sizes' => ['11oz', '15oz'],
        'description' => 'Create your perfect custom mug with text and images',
        '3d' => true
    ],
    'shirt' => [
        'name' => 'T-Shirt',
        'price' => 18,
        'colors' => ['#FFFFFF', '#001a4d', '#E31E24', '#000000', '#808080'],
        'colorNames' => ['White', 'Navy', 'Red', 'Black', 'Gray'],
        'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        'description' => 'Design your custom printed t-shirt',
        '3d' => true
    ],
    'cap' => [
        'name' => 'Custom Cap',
        'price' => 15,
        'colors' => ['#001a4d', '#000000', '#E31E24', '#FFFFFF', '#CDAA7D'],
        'colorNames' => ['Navy', 'Black', 'Red', 'White', 'Khaki'],
        'sizes' => 'One Size',
        'description' => 'Create your personalized embroidered cap',
        '3d' => true
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
        <div style="width: 280px; background-color: var(--white); border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.5rem; height: fit-content; max-height: 90vh; overflow-y: auto;">
            <h4 style="margin-bottom: 1.5rem;">Customization</h4>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Product Color</label>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem;">
                    <?php foreach ($product['colors'] as $idx => $color): ?>
                        <button class="color-btn" data-color="<?php echo $color; ?>" data-index="<?php echo $idx; ?>" style="width: 100%; height: 50px; border-radius: 0.5rem; border: 3px solid transparent; cursor: pointer; background-color: <?php echo $color; ?>; transition: all 0.3s;" title="<?php echo $product['colorNames'][$idx]; ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="margin-top: 1.5rem; padding: 1rem; background-color: var(--light-gray); border-radius: 0.5rem;">
                <?php if ($type === 'shirt'): ?>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; margin-top: 0.5rem;">Placement</label>
                    <select id="text-placement" style="width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid var(--border-color); border-radius: 0.25rem; background-color: var(--dark-grey); color: white;">
                        <option value="front">Front of Shirt</option>
                        <option value="back">Back of Shirt</option>
                    </select>
                <?php else: ?>
                    <input type="hidden" id="text-placement" value="front">
                <?php endif; ?>
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
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem;">Font Size</label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="range" id="text-size" min="8" max="72" value="24" style="flex: 1; cursor: pointer;">
                        <span style="font-size: 0.85rem; font-weight: 600; min-width: 40px;">
                            <span id="size-value">24</span>px
                        </span>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem;">Text Color</label>
                    <input type="color" id="text-color" value="#000000" style="width: 100%; height: 40px; cursor: pointer; border: none; border-radius: 0.5rem;">
                </div>

                <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                    <button id="text-bold" data-option="bold" style="flex: 1; padding: 0.5rem; border: 2px solid var(--border-color); border-radius: 0.25rem; background-color: var(--white); cursor: pointer; font-weight: 700;" title="Bold">B</button>
                    <button id="text-italic" data-option="italic" style="flex: 1; padding: 0.5rem; border: 2px solid var(--border-color); border-radius: 0.25rem; background-color: var(--white); cursor: pointer; font-style: italic;" title="Italic">I</button>
                </div>

                <button style="width: 100%; padding: 0.75rem; background-color: var(--primary-red); color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;" id="add-text">
                    Add Text to Product
                </button>
            </div>
            <div style="margin-top: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Add Image to Product</label>
                
                <?php if ($type === 'shirt'): ?>
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; margin-top: 0.5rem;">Placement</label>
                    <select id="image-placement" style="width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid var(--border-color); border-radius: 0.25rem; background-color: var(--dark-grey); color: white;">
                        <option value="front">Front of Shirt</option>
                        <option value="back">Back of Shirt</option>
                    </select>
                <?php else: ?>
                    <input type="hidden" id="image-placement" value="front">
                <?php endif; ?>

                <input type="file" id="product-image-upload" accept="image/*" style="width: 100%; padding: 0.75rem; border: 2px dashed var(--primary-red); border-radius: 0.5rem; cursor: pointer;">
                <small style="color: var(--text-light);">JPG, PNG (max 5MB)</small>
            </div>

            <div id="upload-status" style="margin-top: 0.75rem; padding: 0.75rem; border-radius: 0.5rem; font-size: 0.85rem; display: none; text-align: center; font-weight: 600;"></div>

            <div style="margin-top: 1.5rem; padding: 1rem; background-color: var(--light-gray); border-radius: 0.5rem;">
                <h5 style="margin-bottom: 1rem;">Design Layers</h5>
                <div id="layers-list" style="max-height: 200px; overflow-y: auto; background-color: white; border-radius: 0.5rem; padding: 0.5rem;">
                    <div style="color: var(--text-light); font-size: 0.85rem; text-align: center; padding: 1rem;">No layers yet</div>
                </div>
                <button style="width: 100%; padding: 0.75rem; margin-top: 1rem; background-color: var(--danger); color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-size: 0.85rem; font-weight: 600;" id="reset-product">
                    Clear All Layers
                </button>
                <div id="layer-controls" style="display: none; margin-top: 1.5rem; padding: 1rem; background-color: var(--white); border: 2px solid var(--border-color); border-radius: 0.5rem;">
                    <h6 style="margin-bottom: 1rem; font-weight: 700; color: var(--primary-red);">Edit Selected Layer</h6>
                    
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.5rem;">Quick Placement</label>
                    <div style="display: flex; gap: 5px; margin-bottom: 1.5rem;">
                        <button type="button" onclick="snapToPosition('front')" style="flex: 1; padding: 5px; font-size: 0.75rem; background: var(--dark-grey); color: white; border: none; border-radius: 4px; cursor: pointer;">Front</button>
                        <button type="button" onclick="snapToPosition('back')" style="flex: 1; padding: 5px; font-size: 0.75rem; background: var(--dark-grey); color: white; border: none; border-radius: 4px; cursor: pointer;">Back</button>
                    </div>

                    <label style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">
                        <span>Move Left / Right</span>
                        <span style="color: var(--primary-red); font-weight: bold;">X: <span id="display-x">512</span></span>
                    </label>
                    <input type="range" id="ctrl-x" min="-200" max="1200" value="512" style="width: 100%; margin-bottom: 1rem; cursor: pointer;" oninput="updateActiveLayer()">
                    
                    <label style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">
                        <span>Move Up / Down</span>
                        <span style="color: var(--primary-red); font-weight: bold;">Y: <span id="display-y">512</span></span>
                    </label>
                    <input type="range" id="ctrl-y" min="-200" max="1200" value="512" style="width: 100%; margin-bottom: 1rem; cursor: pointer;" oninput="updateActiveLayer()">
                    
                    <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">Adjust Size</label>
                    <input type="range" id="ctrl-size" min="10" max="1000" value="300" style="width: 100%; cursor: pointer;" oninput="updateActiveLayer()">
                </div>
            </div>
        </div>

        <div style="flex: 1; background-color: var(--light-gray); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; min-height: 600px; position: relative; overflow: hidden;">
            <div id="preview-container" style="width: 100%; height: 100%; position: relative; background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);">
                <canvas id="preview-canvas" style="display: block; width: 100%; height: 100%;"></canvas>
                <div id="preview-rotate-hint" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); background-color: rgba(0,0,0,0.7); color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.85rem; text-align: center;">
                    💡 Drag to rotate the product
                </div>
            </div>
        </div>

        <div style="width: 300px; background-color: var(--white); border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.5rem; height: fit-content; max-height: 90vh; overflow-y: auto;">
            <h4 style="margin-bottom: 1.5rem;">Order Summary</h4>

            <div style="margin-bottom: 1.5rem; padding: 1rem; background-color: var(--light-gray); border-radius: 0.5rem;">
                <small style="color: var(--text-light);">Product</small>
                <div style="font-weight: 600; margin-bottom: 0.5rem;"><?php echo $product['name']; ?></div>
                <small style="color: var(--text-light);">Base Price</small>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-red);" id="base-price">$<?php echo number_format($product['price'], 2); ?></div>
            </div>

            <?php if (!empty($product['sizes']) && $product['sizes'] !== 'One Size'): ?>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Size</label>
                    <select id="product-size" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; cursor: pointer;">
                        <?php foreach ($product['sizes'] as $size): ?>
                            <option value="<?php echo $size; ?>"><?php echo $size; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Quantity</label>
                <div style="display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 0.5rem; overflow: hidden;">
                    <button class="qty-adjust-prod" data-value="-1" style="flex: 0 0 40px; height: 40px; background-color: var(--light-gray); border: none; cursor: pointer; font-weight: 600;">−</button>
                    <input type="number" id="product-quantity" value="1" min="1" style="flex: 1; border: none; text-align: center; font-weight: 600;"/>
                    <button class="qty-adjust-prod" data-value="+1" style="flex: 0 0 40px; height: 40px; background-color: var(--light-gray); border: none; cursor: pointer; font-weight: 600;">+</button>
                </div>
            </div>

            <div style="background-color: var(--light-gray); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
                <small style="color: var(--text-light);">Total Price</small>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary-red);" id="product-total">$<?php echo number_format($product['price'], 2); ?></div>
            </div>

            <button style="width: 100%; padding: 1rem; background-color: var(--primary-red); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; margin-bottom: 0.75rem; transition: background-color 0.3s;" id="add-product-cart" onmouseover="this.style.backgroundColor='#c41219'" onmouseout="this.style.backgroundColor='var(--primary-red)'">
                Add to Cart
            </button>

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

    <script src="https://unpkg.com/three@0.128.0/build/three.min.js"></script>
<script src="https://unpkg.com/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <script>
        // ==================== FALLBACK APP OBJECT ====================
        const app = {
            showNotification: (msg, type) => console.log(`[${type}] ${msg}`),
            addToCart: (id, name, price, details) => console.log(`Added to cart: ${name}`)
        };

        // ==================== CUSTOMIZER STATE ====================
        const customizer = {
            productType: '<?php echo $type; ?>',
            basePrice: <?php echo $product['price']; ?>,
            currentColor: '<?php echo $product['colors'][0]; ?>',
            textLayers: [],
            uploadedImage: null,
            textStyles: {
                bold: false,
                italic: false
            },
            rotation: { x: 0, y: 0, z: 0 },
            scene: null,
            camera: null,
            renderer: null,
            productMesh: null,
            productGroup: null,
            textureCanvas: null,
            textureContext: null,
            raycaster: new THREE.Raycaster(),
            mouse: new THREE.Vector2(),
            isDragging: false,
            isDraggingLayer: false,
            isDraggingCorner: false,
            previousMousePosition: { x: 0, y: 0 },
            selectedLayerId: null,
            layerDragMode: false,
            zoom: 1
        };

        // ==================== INITIALIZE SCENE ====================
        function initializeScene() {
            const container = document.getElementById('preview-container');
            const width = container.clientWidth;
            const height = container.clientHeight;

            customizer.scene = new THREE.Scene();
            customizer.scene.background = new THREE.Color(0xf8f8f8);

            customizer.camera = new THREE.PerspectiveCamera(60, width / height, 0.1, 1000);
            customizer.camera.position.set(0, 0.3, 3.2);
            customizer.camera.lookAt(0, 0, 0);

            customizer.renderer = new THREE.WebGLRenderer({ 
                canvas: document.getElementById('preview-canvas'), 
                antialias: true, 
                alpha: true 
            });
            customizer.renderer.setSize(width, height);
            customizer.renderer.setPixelRatio(window.devicePixelRatio);
            customizer.renderer.shadowMap.enabled = true;
            customizer.renderer.shadowMap.type = THREE.PCFShadowShadowMap;
            customizer.renderer.toneMappingExposure = 1;

            // Enhanced lighting setup for photorealism
            // Ambient light - soft overall illumination
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
            customizer.scene.add(ambientLight);

            // Key light - main directional light with shadows
            const keyLight = new THREE.DirectionalLight(0xffffff, 1);
            keyLight.position.set(4, 6, 5);
            keyLight.castShadow = true;
            keyLight.shadow.mapSize.width = 2048;
            keyLight.shadow.mapSize.height = 2048;
            keyLight.shadow.camera.left = -5;
            keyLight.shadow.camera.right = 5;
            keyLight.shadow.camera.top = 5;
            keyLight.shadow.camera.bottom = -5;
            keyLight.shadow.camera.near = 0.5;
            keyLight.shadow.camera.far = 50;
            
            keyLight.shadow.bias = -0.001;
            keyLight.shadow.normalBias = 0.05;

            customizer.scene.add(keyLight);

            // Fill light - subtle light from opposite side
            const fillLight = new THREE.DirectionalLight(0xffffff, 0.4);
            fillLight.position.set(-3, 2, -4);
            customizer.scene.add(fillLight);

            // Rim light - highlights edges for depth
            const rimLight = new THREE.DirectionalLight(0xffffff, 0.3);
            rimLight.position.set(2, 3, -6);
            customizer.scene.add(rimLight);

            // Create product mesh
            createProductMesh();

            // Event listeners
            setupMouseControls();
            setupColorButtons();
            setupTextEditor();
            setupImageUpload();
            setupCartButtons();

            // Start animation loop
            animate();

            window.addEventListener('resize', onWindowResize);
        }

        // ==================== CREATE PRODUCT MESH FROM GLB ====================
        function createProductMesh() {
            if (customizer.productGroup) {
                customizer.scene.remove(customizer.productGroup);
            }

            customizer.productGroup = new THREE.Group();
            customizer.scene.add(customizer.productGroup);

            const loader = new THREE.GLTFLoader();
            const modelPath = `assets/models/${customizer.productType}.glb`;

            loader.load(
                modelPath,
                function(gltf) {
                    const model = gltf.scene;
                    
                    // Calculate initial bounding box
                    const box3 = new THREE.Box3().setFromObject(model);
                    const size = box3.getSize(new THREE.Vector3());
                    const maxDim = Math.max(size.x, size.y, size.z);
                    
                    // Scale to make max dimension exactly 2.5
                    const scale = 2.5 / maxDim;
                    model.scale.multiplyScalar(scale);
                    
                    // Recalculate bounding box after scaling
                    const scaledBox3 = new THREE.Box3().setFromObject(model);
                    const center = scaledBox3.getCenter(new THREE.Vector3());
                    
                    // Center the model in camera view
                    model.position.sub(center);
                    
                    // Apply to all meshes in the model
                    model.traverse((child) => {
                        if (child.isMesh) {
                            child.castShadow = true;
                            child.receiveShadow = true;
                            
                            // Ensure material is MeshStandardMaterial
                            if (!child.material.isMeshStandardMaterial) {
                                const oldMaterial = child.material;
                                child.material = new THREE.MeshStandardMaterial({
                                    color: customizer.currentColor,
                                    roughness: 0.4,
                                    metalness: 0.05,
                                    map: oldMaterial.map
                                });
                            } else {
                                child.material.color.set(customizer.currentColor);
                            }
                        }
                    });
                    
                    customizer.productGroup.add(model);
                    console.log(`Model ${customizer.productType} loaded successfully`);
                },
                function(progress) {
                    console.log(`Loading ${customizer.productType}: ${(progress.loaded / progress.total * 100).toFixed(0)}%`);
                },
                function(error) {
                    console.error(`Error loading model ${modelPath}:`, error);
                    app.showNotification(`Failed to load ${customizer.productType} model`, 'error');
                }
            );
        }

        // ==================== CREATE TEXT TEXTURE (DUAL CANVAS) ====================
        function createTextTexture() {
            // 1. Create Canvas for the FRONT
            const canvasFront = document.createElement('canvas');
            canvasFront.width = 1024; canvasFront.height = 1024;
            const ctxFront = canvasFront.getContext('2d');
            ctxFront.fillStyle = customizer.currentColor;
            ctxFront.fillRect(0, 0, 1024, 1024);

            // 2. Create Canvas for the BACK
            const canvasBack = document.createElement('canvas');
            canvasBack.width = 1024; canvasBack.height = 1024;
            const ctxBack = canvasBack.getContext('2d');
            ctxBack.fillStyle = customizer.currentColor;
            ctxBack.fillRect(0, 0, 1024, 1024);

            // Helper to draw text on the correct canvas
            function drawTexts(ctx, zone) {
                customizer.textLayers.forEach(layer => {
                    if (layer.placement === zone || (!layer.placement && zone === 'front')) {
                        ctx.font = `${layer.italic ? 'italic ' : ''}${layer.bold ? 'bold ' : ''}${layer.size}px ${layer.font}`;
                        ctx.fillStyle = layer.color;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(layer.content, layer.x !== undefined ? layer.x : 512, layer.y !== undefined ? layer.y : 512);
                    }
                });
            }

            // Helper to apply the finished canvases to the 3D model
            function applyTextures() {
                const texFront = new THREE.CanvasTexture(canvasFront);
                texFront.flipY = true;
                const texBack = new THREE.CanvasTexture(canvasBack);
                texBack.flipY = true;

                if (customizer.productGroup) {
                    customizer.productGroup.traverse((child) => {
                        if (child.isMesh && child.material) {
                            if (customizer.productType === 'shirt') {
                                // Maps directly to your 3D file structure!
                                if (child.name === 'Material1718') { // FRONT PIECE
                                    child.material.color.set('#ffffff');
                                    child.material.map = texFront;
                                } else if (child.name === 'Material1722') { // BACK PIECE
                                    child.material.color.set('#ffffff');
                                    child.material.map = texBack;
                                } else { 
                                    // Sleeves and Collar stay base color
                                    child.material.color.set(customizer.currentColor);
                                    child.material.map = null;
                                }
                            } else {
                                // Fallback for mugs and caps
                                child.material.color.set('#ffffff');
                                child.material.map = texFront;
                            }
                            child.material.needsUpdate = true;
                        }
                    });
                }
            }

            // Process Image and Text
            if (customizer.uploadedImage) {
                const img = new Image();
                img.onload = function() {
                    const imgWidth = customizer.uploadedImage.width || 300;
                    const imgHeight = customizer.uploadedImage.height || imgWidth;
                    const x = (customizer.uploadedImage.x || 512) - (imgWidth / 2);
                    const y = (customizer.uploadedImage.y || 512) - (imgHeight / 2);
                    
                    // Route image to correct canvas
                    if (customizer.uploadedImage.placement === 'back') {
                        ctxBack.drawImage(img, x, y, imgWidth, imgHeight);
                    } else {
                        ctxFront.drawImage(img, x, y, imgWidth, imgHeight);
                    }
                    
                    drawTexts(ctxFront, 'front');
                    drawTexts(ctxBack, 'back');
                    applyTextures();
                };
                img.src = customizer.uploadedImage.data || customizer.uploadedImage;
            } else {
                drawTexts(ctxFront, 'front');
                drawTexts(ctxBack, 'back');
                applyTextures();
            }
        }


        // ==================== UPDATE PRODUCT COLOR ====================
        function updateProductColor() {
            if (customizer.productGroup) {
                customizer.productGroup.traverse((child) => {
                    if (child.isMesh && child.material) {
                        child.material.color.set(customizer.currentColor);
                        child.material.needsUpdate = true;
                    }
                });
            }
        }

        // ==================== COLOR BUTTONS ====================
        function setupColorButtons() {
            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const color = btn.dataset.color;
                    customizer.currentColor = color;

                    // Update button border
                    document.querySelectorAll('.color-btn').forEach(b => {
                        b.style.borderColor = 'transparent';
                        b.style.boxShadow = 'none';
                    });
                    btn.style.borderColor = '#E31E24';
                    btn.style.boxShadow = '0 0 0 2px white, 0 0 0 4px #E31E24';

                    // Update product color
                    if (customizer.uploadedImage) {
                        // Re-render texture with new color background
                        createTextTexture();
                    } else {
                        // Update all parts when no image
                        updateProductColor();
                    }

                    // Update non-texture parts (handle, rim, bottom)
                    customizer.productGroup.traverse((child) => {
                        if (child !== customizer.productMesh && child.isMesh && child.material) {
                            child.material.color.set(color);
                            child.material.needsUpdate = true;
                        }
                    });

                    app.showNotification(`Color changed to ${btn.title}`, 'info');
                });
            });

            // Set initial color button as selected
            document.querySelector('.color-btn').style.borderColor = '#E31E24';
            document.querySelector('.color-btn').style.boxShadow = '0 0 0 2px white, 0 0 0 4px #E31E24';
        }

        // ==================== TEXT EDITOR ====================
        function setupTextEditor() {
            document.getElementById('text-size').addEventListener('input', (e) => {
                document.getElementById('size-value').textContent = e.target.value;
            });

            document.getElementById('text-bold').addEventListener('click', function() {
                customizer.textStyles.bold = !customizer.textStyles.bold;
                this.style.backgroundColor = customizer.textStyles.bold ? '#E31E24' : 'var(--white)';
                this.style.color = customizer.textStyles.bold ? 'white' : 'var(--text-dark)';
            });

            document.getElementById('text-italic').addEventListener('click', function() {
                customizer.textStyles.italic = !customizer.textStyles.italic;
                this.style.backgroundColor = customizer.textStyles.italic ? '#E31E24' : 'var(--white)';
                this.style.color = customizer.textStyles.italic ? 'white' : 'var(--text-dark)';
            });

            document.getElementById('add-text').addEventListener('click', addTextLayer);
        }

        function addTextLayer() {
            const textContent = document.getElementById('text-content').value.trim();
            if (!textContent) {
                app.showNotification('Please enter text', 'warning');
                return;
            }

            const textSize = parseInt(document.getElementById('text-size').value);
            const textFont = document.getElementById('text-font').value;
            const textColor = document.getElementById('text-color').value;

            // Read the placement from the dropdown (or hidden input for mugs)
            const placementElement = document.getElementById('text-placement');
            const placement = placementElement ? placementElement.value : 'front';
            const startCoords = getStartingCoordinates(placement);

            const textLayer = {
                id: Date.now(),
                content: textContent,
                size: textSize,
                font: textFont,
                color: textColor,
                bold: customizer.textStyles.bold,
                italic: customizer.textStyles.italic,
                placement: placement,
                x: startCoords.x,
                y: startCoords.y + (customizer.textLayers.length * 40) // Offset vertically if multiple texts
            };

            customizer.textLayers.push(textLayer);
            updateLayersList();
            document.getElementById('text-content').value = '';

            app.showNotification('Text added to product! ✓ Click layer to reposition', 'success');
            
            // Update texture with new text
            createTextTexture();
        }

        function updateLayersList() {
            const layersList = document.getElementById('layers-list');
            
            if (customizer.textLayers.length === 0 && !customizer.uploadedImage) {
                layersList.innerHTML = '<div style="color: var(--text-light); font-size: 0.85rem; text-align: center; padding: 1rem;">No layers yet</div>';
                return;
            }

            let html = '';

            // Image layer
            if (customizer.uploadedImage) {
                html += `
                    <div style="padding: 0.75rem; margin-bottom: 0.5rem; background-color: #e8f5e9; border-left: 3px solid #4caf50; border-radius: 0.25rem; cursor: pointer;" class="layer-item" data-type="image" onclick="selectLayer('image')">
                        <div style="font-weight: 600; font-size: 0.9rem;">📸 Image</div>
                        <div style="font-size: 0.75rem; color: var(--text-light);">Click to reposition</div>
                        <button onclick="removeImageLayer(event)" style="padding: 0.25rem 0.5rem; background-color: #ff6b6b; color: white; border: none; border-radius: 0.25rem; cursor: pointer; font-size: 0.75rem; margin-top: 0.5rem; width: 100%;">Remove</button>
                    </div>
                `;
            }

            // Text layers
            html += customizer.textLayers.map((layer, idx) => `
                <div style="padding: 0.75rem; margin-bottom: 0.5rem; background-color: #f9f9f9; border-left: 3px solid ${layer.color}; border-radius: 0.25rem; cursor: pointer;" class="layer-item" data-type="text" data-id="${layer.id}" onclick="selectLayer(${layer.id})">
                    <div style="font-weight: 600; font-size: 0.9rem; word-break: break-word;">${layer.content.substring(0, 25)}</div>
                    <div style="font-size: 0.75rem; color: var(--text-light);">${layer.size}px • ${layer.font}</div>
                    <div style="font-size: 0.75rem; color: #666; margin-top: 0.25rem;">Click to reposition on product</div>
                    <button onclick="removeTextLayer(${layer.id}, event)" style="padding: 0.25rem 0.5rem; background-color: #ff6b6b; color: white; border: none; border-radius: 0.25rem; cursor: pointer; font-size: 0.75rem; margin-top: 0.5rem; width: 100%;">Remove</button>
                </div>
            `).join('');

            layersList.innerHTML = html;
        }

        function selectLayer(layerId) {
            customizer.selectedLayerId = layerId;
            
            // Show the control panel
            document.getElementById('layer-controls').style.display = 'block';
            
            const ctrlX = document.getElementById('ctrl-x');
            const ctrlY = document.getElementById('ctrl-y');
            const ctrlSize = document.getElementById('ctrl-size');

            // Set current values to the sliders
            if (layerId === 'image' && customizer.uploadedImage) {
                ctrlX.value = customizer.uploadedImage.x || 512;
                ctrlY.value = customizer.uploadedImage.y || 512;
                ctrlSize.value = customizer.uploadedImage.width || 300;
            } else {
                const layer = customizer.textLayers.find(l => l.id === layerId);
                if (layer) {
                    ctrlX.value = layer.x || 512;
                    ctrlY.value = layer.y || 512;
                    ctrlSize.value = layer.size * 5; // Math to map font size to slider scale
                }
            }

            // Update function when slider moves
            const updateCurrentLayer = () => {
                if (customizer.selectedLayerId === 'image' && customizer.uploadedImage) {
                    customizer.uploadedImage.x = parseInt(ctrlX.value);
                    customizer.uploadedImage.y = parseInt(ctrlY.value);
                    customizer.uploadedImage.width = parseInt(ctrlSize.value);
                    customizer.uploadedImage.height = parseInt(ctrlSize.value);
                } else {
                    const layer = customizer.textLayers.find(l => l.id === customizer.selectedLayerId);
                    if (layer) {
                        layer.x = parseInt(ctrlX.value);
                        layer.y = parseInt(ctrlY.value);
                        layer.size = parseInt(ctrlSize.value) / 5;
                    }
                }
                createTextTexture(); // Redraw immediately
            };

            // Attach listeners (removes old ones first to prevent duplicates)
            ctrlX.oninput = updateCurrentLayer;
            ctrlY.oninput = updateCurrentLayer;
            ctrlSize.oninput = updateCurrentLayer;
        }

        // ==================== GET STARTING COORDINATES ====================
        function getStartingCoordinates(placementZone) {
            return { x: 512, y: 512 }; // Dead center for both front and back!
        }

       // ==================== QUICK POSITION SNAP ====================
        function snapToPosition(zone) {
            if (!customizer.selectedLayerId) return;

            // Instantly snap to perfect center
            document.getElementById('ctrl-x').value = 512;
            document.getElementById('ctrl-y').value = 512;
            
            // Sync the displays
            document.getElementById('display-x').textContent = 512;
            document.getElementById('display-y').textContent = 512;
            
            updateActiveLayer(); 
        }

        // ==================== UPDATE LAYER FROM SLIDERS ====================
        function updateActiveLayer() {
            if (!customizer.selectedLayerId) return;

            const ctrlX = document.getElementById('ctrl-x').value;
            const ctrlY = document.getElementById('ctrl-y').value;
            const ctrlSize = document.getElementById('ctrl-size').value;

            // UPDATE THE VISUAL TRACKER ON SCREEN
            document.getElementById('display-x').textContent = ctrlX;
            document.getElementById('display-y').textContent = ctrlY;

            if (customizer.selectedLayerId === 'image' && customizer.uploadedImage) {
                customizer.uploadedImage.x = parseInt(ctrlX);
                customizer.uploadedImage.y = parseInt(ctrlY);
                customizer.uploadedImage.width = parseInt(ctrlSize);
                customizer.uploadedImage.height = parseInt(ctrlSize);
            } else {
                const layer = customizer.textLayers.find(l => l.id === customizer.selectedLayerId);
                if (layer) {
                    layer.x = parseInt(ctrlX);
                    layer.y = parseInt(ctrlY);
                    layer.size = parseInt(ctrlSize) / 5;
                }
            }
            createTextTexture(); // Force Three.js to redraw the shirt immediately
        }

        function removeTextLayer(id, event) {
            if (event) event.stopPropagation();
            customizer.textLayers = customizer.textLayers.filter(l => l.id !== id);
            updateLayersList();
            
            // Update texture - remove text
            createTextTexture();
            
            app.showNotification('Text layer removed', 'info');
        }

        function removeImageLayer(event) {
            if (event) event.stopPropagation();
            customizer.uploadedImage = null;
            document.getElementById('upload-status').style.display = 'none';
            updateLayersList();
            createTextTexture();
            app.showNotification('Image removed', 'info');
        }

        // ==================== IMAGE UPLOAD ====================
        function setupImageUpload() {
            document.getElementById('product-image-upload').addEventListener('change', handleImageUpload);
        }

        function handleImageUpload(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Validate file
            if (file.size > 5 * 1024 * 1024) {
                app.showNotification('File too large (max 5MB)', 'error');
                return;
            }

            if (!file.type.match('image.*')) {
                app.showNotification('Please upload an image file', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                // Read the placement from the dropdown (or hidden input for mugs)
                const placementElement = document.getElementById('image-placement');
                const placement = placementElement ? placementElement.value : 'front';
                const startCoords = getStartingCoordinates(placement);

                customizer.uploadedImage = {
                    data: event.target.result,
                    placement: placement,
                    x: startCoords.x,  
                    y: startCoords.y,
                    width: 300, // Safe default size
                    height: 300
                };
                
                const statusDiv = document.getElementById('upload-status');
                statusDiv.textContent = '✓ Image uploaded! Click layer to reposition';
                statusDiv.className = 'success';
                statusDiv.style.display = 'block';

                app.showNotification('Image added to product! ✓', 'success');
                updateLayersList();
                createTextTexture();

                // Clear input
                e.target.value = '';
            };
            reader.readAsDataURL(file);
        }

        // ==================== RENDER PREVIEW ====================
        function renderPreview() {
            // Handled by animation loop
        }

        // ==================== MOUSE CONTROLS ====================
        function setupMouseControls() {
            const canvas = document.getElementById('preview-canvas');

            canvas.addEventListener('mousedown', (e) => {
                if (customizer.layerDragMode) {
                    // Allow dragging layers to reposition
                    customizer.isDraggingLayer = true;
                    return;
                }
                customizer.isDragging = true;
                customizer.previousMousePosition = { x: e.clientX, y: e.clientY };
            });

            canvas.addEventListener('mousemove', (e) => {
                const rect = canvas.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 2 - 1;
                const y = -((e.clientY - rect.top) / rect.height) * 2 + 1;

                if (customizer.isDraggingLayer && customizer.selectedLayerId) {
                    // Reposition or resize layer based on mouse position
                    if (customizer.selectedLayerId === 'image' && customizer.uploadedImage) {
                        const deltaX = e.clientX - (customizer.previousMousePosition?.x || e.clientX);
                        const deltaY = e.clientY - (customizer.previousMousePosition?.y || e.clientY);
                        
                        // Check if dragging from corner (for resizing)
                        const isNearCorner = Math.abs(deltaX) > 50 || Math.abs(deltaY) > 50;
                        if (isNearCorner && customizer.isDraggingCorner) {
                            // Resize mode
                            customizer.uploadedImage.width = Math.max(50, (customizer.uploadedImage.width || 300) + deltaX);
                            customizer.uploadedImage.height = Math.max(50, (customizer.uploadedImage.height || 300) + deltaY);
                        } else {
                            // Move mode
                            customizer.uploadedImage.x = 512 + (x * 500);
                            customizer.uploadedImage.y = 512 + (y * 500);
                        }
                    } else {
                        const layer = customizer.textLayers.find(l => l.id === customizer.selectedLayerId);
                        if (layer) {
                            layer.x = 512 + (x * 400);
                            layer.y = 512 + (y * 400);
                        }
                    }
                    createTextTexture();
                    customizer.previousMousePosition = { x: e.clientX, y: e.clientY };
                    return;
                }

                if (!customizer.isDragging) return;

                const deltaX = e.clientX - customizer.previousMousePosition.x;
                const deltaY = e.clientY - customizer.previousMousePosition.y;

                customizer.rotation.y += deltaX * 0.01;
                customizer.rotation.x += deltaY * 0.01;

                if (customizer.productGroup) {
                    customizer.productGroup.rotation.y = customizer.rotation.y;
                    customizer.productGroup.rotation.x = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, customizer.rotation.x));
                }

                customizer.previousMousePosition = { x: e.clientX, y: e.clientY };
            });

            canvas.addEventListener('mouseup', () => {
                customizer.isDragging = false;
                customizer.isDraggingLayer = false;
                if (customizer.layerDragMode) {
                    customizer.layerDragMode = false;
                    customizer.selectedLayerId = null;
                    app.showNotification('Layer updated! ✓', 'success');
                }
            });

            canvas.addEventListener('mouseleave', () => {
                customizer.isDragging = false;
                customizer.isDraggingLayer = false;
            });

            // Mouse wheel zoom
            canvas.addEventListener('wheel', (e) => {
                e.preventDefault();
                const zoomSpeed = 0.1;
                if (e.deltaY < 0) {
                    // Zoom in
                    customizer.zoom *= (1 + zoomSpeed);
                } else {
                    // Zoom out
                    customizer.zoom *= (1 - zoomSpeed);
                }
                customizer.zoom = Math.max(0.5, Math.min(3, customizer.zoom));
                if (customizer.camera) {
                    customizer.camera.position.z = 2.5 / customizer.zoom;
                }
            }, { passive: false });
        }

        // ==================== CART BUTTONS ====================
        function setupCartButtons() {
            // Quantity adjustment and price update
            document.querySelectorAll('.qty-adjust-prod').forEach(btn => {
                btn.addEventListener('click', () => {
                    const input = document.getElementById('product-quantity');
                    const change = parseInt(btn.dataset.value);
                    input.value = Math.max(1, parseInt(input.value) + change);
                    updateProductTotal();
                });
            });

            // Listen to direct quantity input changes
            document.getElementById('product-quantity').addEventListener('change', updateProductTotal);
            document.getElementById('product-quantity').addEventListener('input', updateProductTotal);

            document.getElementById('add-product-cart').addEventListener('click', () => {
                const quantity = parseInt(document.getElementById('product-quantity').value) || 1;
                const price = customizer.basePrice;
                
                for (let i = 0; i < quantity; i++) {
                    app.addToCart(
                        customizer.productType + '-' + Date.now() + '-' + i,
                        '<?php echo $product['name']; ?>',
                        price,
                        null
                    );
                }
                
                app.showNotification(quantity + ' item(s) added to cart! ✓', 'success');
                document.getElementById('product-quantity').value = 1;
                updateProductTotal();
            });

            document.getElementById('reset-product').addEventListener('click', () => {
                if (confirm('Clear all layers and designs?')) {
                    customizer.textLayers = [];
                    customizer.uploadedImage = null;
                    customizer.selectedLayerId = null;
                    updateLayersList();
                    document.getElementById('upload-status').style.display = 'none';
                    
                    // Clear text texture
                    if (customizer.productMesh && customizer.productMesh.material) {
                        customizer.productMesh.material.map = null;
                        customizer.productMesh.material.needsUpdate = true;
                    }
                    
                    app.showNotification('Design cleared!', 'info');
                }
            });
        }

        function updateProductTotal() {
            const quantity = parseInt(document.getElementById('product-quantity').value) || 1;
            const total = customizer.basePrice * quantity;
            document.getElementById('product-total').textContent = '$' + total.toFixed(2);
        }

        // ==================== ANIMATION LOOP ====================
        function animate() {
            requestAnimationFrame(animate);
            
            customizer.renderer.render(customizer.scene, customizer.camera);
        }

        function onWindowResize() {
            const container = document.getElementById('preview-container');
            const width = container.clientWidth;
            const height = container.clientHeight;

            customizer.camera.aspect = width / height;
            customizer.camera.updateProjectionMatrix();
            customizer.renderer.setSize(width, height);
        }

        // ==================== INITIALIZE ====================
        document.addEventListener('DOMContentLoaded', initializeScene);
    </script>

    <style>
        #preview-canvas {
            cursor: grab;
        }

        #preview-canvas:active {
            cursor: grabbing;
        }

        #upload-status {
            font-size: 0.9rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-top: 0.75rem;
            border-left: 4px solid;
        }

        #upload-status.success {
            background-color: #d4edda;
            color: #155724;
            border-left-color: #155724;
        }

        #upload-status.error {
            background-color: #f8d7da;
            color: #721c24;
            border-left-color: #721c24;
        }

        .color-btn {
            transition: all 0.3s ease;
        }

        .color-btn:hover {
            transform: scale(1.05);
        }

        @media (max-width: 1200px) {
            section > div {
                width: 100% !important;
            }

            section {
                flex-direction: column !important;
                gap: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            #preview-container {
                min-height: 400px;
            }

            section > div {
                width: 100% !important;
                max-height: none !important;
            }
        }
    </style>

<?php include 'includes/footer.php'; ?>