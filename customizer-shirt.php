<?php
/**
 * Product Customizer - T-Shirt 3D Engine
 * Triangle Printing Solutions
 * Developed by: Shenal Navinda Perera
 */
session_start();
require_once 'db.php';

// Hardcoded for the Shirt module
$type = 'shirt';
$page_title = 'T-Shirt Customizer';

// Product configuration with hex colors
$products = [
    'shirt' => [
        'name' => 'T-Shirt',
        'price' => 18,
        'colors' => ['#FFFFFF', '#001a4d', '#E31E24', '#000000', '#808080'],
        'colorNames' => ['White', 'Navy', 'Red', 'Black', 'Gray'],
        'sizes' => ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
        'description' => 'Design your custom printed t-shirt',
        '3d' => true
    ]
];

$product = $products['shirt'];

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
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; margin-top: 0.5rem;">Placement</label>
                <select id="text-placement" style="width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid var(--border-color); border-radius: 0.25rem; background-color: var(--dark-grey); color: white;">
                    <option value="front">Front of Shirt</option>
                    <option value="back">Back of Shirt</option>
                </select>
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
                    Add Text to Shirt
                </button>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Add Image to Shirt</label>
                
                <label style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem; margin-top: 0.5rem;">Placement</label>
                <select id="image-placement" style="width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid var(--border-color); border-radius: 0.25rem; background-color: var(--dark-grey); color: white;">
                    <option value="front">Front of Shirt</option>
                    <option value="back">Back of Shirt</option>
                </select>

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
                        <button type="button" onclick="snapToPosition('front')" style="flex: 1; padding: 5px; font-size: 0.75rem; background: var(--dark-grey); color: white; border: none; border-radius: 4px; cursor: pointer;">Center</button>
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
            productType: 'shirt',
            basePrice: <?php echo $product['price']; ?>,
            currentColor: '<?php echo $product['colors'][0]; ?>',
            textLayers: [],
            uploadedImage: null,
            textStyles: { bold: false, italic: false },
            rotation: { x: 0, y: 0, z: 0 },
            scene: null, camera: null, renderer: null,
            productMesh: null, productGroup: null,
            raycaster: new THREE.Raycaster(),
            mouse: new THREE.Vector2(),
            isDragging: false, isDraggingLayer: false, isDraggingCorner: false,
            previousMousePosition: { x: 0, y: 0 },
            selectedLayerId: null, layerDragMode: false, zoom: 1
        };

        // ==================== INITIALIZE SCENE ====================
        function initializeScene() {
            const container = document.getElementById('preview-container');
            const width = container.clientWidth;
            const height = container.clientHeight;

            customizer.scene = new THREE.Scene();
            customizer.scene.background = new THREE.Color(0xf8f8f8);

            // Shirt camera is closer (2.5) than the mug
            customizer.camera = new THREE.PerspectiveCamera(60, width / height, 0.1, 1000);
            customizer.camera.position.set(0, 0.3, 2.5);
            customizer.camera.lookAt(0, 0, 0);

            // 1. ENCODING & SOFT SHADOWS
            customizer.renderer = new THREE.WebGLRenderer({ 
                canvas: document.getElementById('preview-canvas'), 
                antialias: true, alpha: true 
            });
            customizer.renderer.setSize(width, height);
            customizer.renderer.setPixelRatio(window.devicePixelRatio);
            customizer.renderer.shadowMap.enabled = true;
            customizer.renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            customizer.renderer.outputEncoding = THREE.sRGBEncoding;

            const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
            customizer.scene.add(ambientLight);

            // 2. HIGH-RES SHADOWS & BIAS (Fixes jagged surface shadows)
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

            const fillLight = new THREE.DirectionalLight(0xffffff, 0.4);
            fillLight.position.set(-3, 2, -4);
            customizer.scene.add(fillLight);

            const rimLight = new THREE.DirectionalLight(0xffffff, 0.3);
            rimLight.position.set(2, 3, -6);
            customizer.scene.add(rimLight);

            createProductMesh();
            setupMouseControls();
            setupColorButtons();
            setupTextEditor();
            setupImageUpload();
            setupCartButtons();
            animate();

            window.addEventListener('resize', onWindowResize);
        }

        // ==================== CREATE PRODUCT MESH ====================
        function createProductMesh() {
            if (customizer.productGroup) {
                customizer.scene.remove(customizer.productGroup);
            }

            customizer.productGroup = new THREE.Group();
            customizer.scene.add(customizer.productGroup);

            const loader = new THREE.GLTFLoader();
            loader.load(
                `assets/models/shirt.glb`,
                function(gltf) {
                    const model = gltf.scene;
                    const box3 = new THREE.Box3().setFromObject(model);
                    const size = box3.getSize(new THREE.Vector3());
                    const maxDim = Math.max(size.x, size.y, size.z);
                    const scale = 2.5 / maxDim;
                    model.scale.multiplyScalar(scale);
                    
                    const scaledBox3 = new THREE.Box3().setFromObject(model);
                    const center = scaledBox3.getCenter(new THREE.Vector3());
                    model.position.sub(center);
                    
                    model.traverse((child) => {
                        if (child.isMesh) {
                            child.castShadow = true;
                            child.receiveShadow = true;
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
                }
            );
        }

        // ==================== CREATE TEXT TEXTURE (DUAL CANVAS) ====================
        function createTextTexture() {
            const canvasFront = document.createElement('canvas');
            canvasFront.width = 1024; canvasFront.height = 1024;
            const ctxFront = canvasFront.getContext('2d');
            ctxFront.fillStyle = customizer.currentColor;
            ctxFront.fillRect(0, 0, 1024, 1024);

            const canvasBack = document.createElement('canvas');
            canvasBack.width = 1024; canvasBack.height = 1024;
            const ctxBack = canvasBack.getContext('2d');
            ctxBack.fillStyle = customizer.currentColor;
            ctxBack.fillRect(0, 0, 1024, 1024);

            function drawTexts(ctx, zone) {
                customizer.textLayers.forEach(layer => {
                    if (layer.placement === zone) {
                        ctx.font = `${layer.italic ? 'italic ' : ''}${layer.bold ? 'bold ' : ''}${layer.size}px ${layer.font}`;
                        ctx.fillStyle = layer.color;
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(layer.content, layer.x !== undefined ? layer.x : 512, layer.y !== undefined ? layer.y : 512);
                    }
                });
            }

            function applyTextures() {
                // Configure FRONT texture
                const texFront = new THREE.CanvasTexture(canvasFront);
                texFront.flipY = true;
                texFront.anisotropy = customizer.renderer.capabilities.getMaxAnisotropy();
                texFront.generateMipmaps = false;
                texFront.minFilter = THREE.LinearFilter;
                texFront.magFilter = THREE.LinearFilter;
                texFront.encoding = THREE.sRGBEncoding;

                // Configure BACK texture
                const texBack = new THREE.CanvasTexture(canvasBack);
                texBack.flipY = true;
                texBack.anisotropy = customizer.renderer.capabilities.getMaxAnisotropy();
                texBack.generateMipmaps = false;
                texBack.minFilter = THREE.LinearFilter;
                texBack.magFilter = THREE.LinearFilter;
                texBack.encoding = THREE.sRGBEncoding;

                if (customizer.productGroup) {
                    customizer.productGroup.traverse((child) => {
                        if (child.isMesh && child.material) {
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
                            child.material.needsUpdate = true;
                        }
                    });
                }
            }

            if (customizer.uploadedImage) {
                const img = new Image();
                img.onload = function() {
                    const imgWidth = customizer.uploadedImage.width || 300;
                    const imgHeight = customizer.uploadedImage.height || imgWidth;
                    const x = (customizer.uploadedImage.x || 512) - (imgWidth / 2);
                    const y = (customizer.uploadedImage.y || 512) - (imgHeight / 2);
                    
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

        function setupColorButtons() {
            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const color = btn.dataset.color;
                    customizer.currentColor = color;

                    document.querySelectorAll('.color-btn').forEach(b => {
                        b.style.borderColor = 'transparent';
                        b.style.boxShadow = 'none';
                    });
                    btn.style.borderColor = '#E31E24';
                    btn.style.boxShadow = '0 0 0 2px white, 0 0 0 4px #E31E24';

                    if (customizer.uploadedImage || customizer.textLayers.length > 0) {
                        createTextTexture();
                    } else {
                        updateProductColor();
                    }
                });
            });
            document.querySelector('.color-btn').style.borderColor = '#E31E24';
            document.querySelector('.color-btn').style.boxShadow = '0 0 0 2px white, 0 0 0 4px #E31E24';
        }

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
            if (!textContent) return app.showNotification('Please enter text', 'warning');

            const placement = document.getElementById('text-placement').value;
            const startCoords = getStartingCoordinates();

            const textLayer = {
                id: Date.now(),
                content: textContent,
                size: parseInt(document.getElementById('text-size').value),
                font: document.getElementById('text-font').value,
                color: document.getElementById('text-color').value,
                bold: customizer.textStyles.bold,
                italic: customizer.textStyles.italic,
                placement: placement,
                x: startCoords.x,
                y: startCoords.y + (customizer.textLayers.length * 40)
            };

            customizer.textLayers.push(textLayer);
            updateLayersList();
            document.getElementById('text-content').value = '';
            app.showNotification('Text added! Click layer to reposition', 'success');
            createTextTexture();
        }

        function updateLayersList() {
            const layersList = document.getElementById('layers-list');
            
            if (customizer.textLayers.length === 0 && !customizer.uploadedImage) {
                layersList.innerHTML = '<div style="color: var(--text-light); font-size: 0.85rem; text-align: center; padding: 1rem;">No layers yet</div>';
                return;
            }

            let html = '';
            if (customizer.uploadedImage) {
                html += `
                    <div style="padding: 0.75rem; margin-bottom: 0.5rem; background-color: #e8f5e9; border-left: 3px solid #4caf50; border-radius: 0.25rem; cursor: pointer;" class="layer-item" onclick="selectLayer('image')">
                        <div style="font-weight: 600; font-size: 0.9rem;">📸 Image (${customizer.uploadedImage.placement})</div>
                        <button onclick="removeImageLayer(event)" style="padding: 0.25rem 0.5rem; background-color: #ff6b6b; color: white; border: none; border-radius: 0.25rem; cursor: pointer; font-size: 0.75rem; margin-top: 0.5rem; width: 100%;">Remove</button>
                    </div>`;
            }

            html += customizer.textLayers.map((layer) => `
                <div style="padding: 0.75rem; margin-bottom: 0.5rem; background-color: #f9f9f9; border-left: 3px solid ${layer.color}; border-radius: 0.25rem; cursor: pointer;" class="layer-item" onclick="selectLayer(${layer.id})">
                    <div style="font-weight: 600; font-size: 0.9rem; word-break: break-word;">${layer.content.substring(0, 25)} (${layer.placement})</div>
                    <button onclick="removeTextLayer(${layer.id}, event)" style="padding: 0.25rem 0.5rem; background-color: #ff6b6b; color: white; border: none; border-radius: 0.25rem; cursor: pointer; font-size: 0.75rem; margin-top: 0.5rem; width: 100%;">Remove</button>
                </div>
            `).join('');

            layersList.innerHTML = html;
        }

        function selectLayer(layerId) {
            customizer.selectedLayerId = layerId;
            document.getElementById('layer-controls').style.display = 'block';
            
            const ctrlX = document.getElementById('ctrl-x');
            const ctrlY = document.getElementById('ctrl-y');
            const ctrlSize = document.getElementById('ctrl-size');

            if (layerId === 'image' && customizer.uploadedImage) {
                ctrlX.value = customizer.uploadedImage.x || 512;
                ctrlY.value = customizer.uploadedImage.y || 512;
                ctrlSize.value = customizer.uploadedImage.width || 300;
            } else {
                const layer = customizer.textLayers.find(l => l.id === layerId);
                if (layer) {
                    ctrlX.value = layer.x || 512;
                    ctrlY.value = layer.y || 512;
                    ctrlSize.value = layer.size * 5;
                }
            }
        }

        function getStartingCoordinates() {
            return { x: 512, y: 512 }; 
        }

        function snapToPosition() {
            if (!customizer.selectedLayerId) return;
            document.getElementById('ctrl-x').value = 512;
            document.getElementById('ctrl-y').value = 512;
            document.getElementById('display-x').textContent = 512;
            document.getElementById('display-y').textContent = 512;
            updateActiveLayer(); 
        }

        function updateActiveLayer() {
            if (!customizer.selectedLayerId) return;

            const ctrlX = document.getElementById('ctrl-x').value;
            const ctrlY = document.getElementById('ctrl-y').value;
            const ctrlSize = document.getElementById('ctrl-size').value;

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
            createTextTexture(); 
        }

        function removeTextLayer(id, event) {
            if (event) event.stopPropagation();
            customizer.textLayers = customizer.textLayers.filter(l => l.id !== id);
            updateLayersList();
            createTextTexture();
            app.showNotification('Text removed', 'info');
        }

        function removeImageLayer(event) {
            if (event) event.stopPropagation();
            customizer.uploadedImage = null;
            document.getElementById('upload-status').style.display = 'none';
            updateLayersList();
            createTextTexture();
            app.showNotification('Image removed', 'info');
        }

        function setupImageUpload() {
            document.getElementById('product-image-upload').addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) return app.showNotification('File too large', 'error');

                const reader = new FileReader();
                reader.onload = function(event) {
                    const placement = document.getElementById('image-placement').value;
                    const startCoords = getStartingCoordinates();

                    customizer.uploadedImage = {
                        data: event.target.result,
                        placement: placement,
                        x: startCoords.x,  
                        y: startCoords.y,
                        width: 300, height: 300
                    };
                    
                    const statusDiv = document.getElementById('upload-status');
                    statusDiv.textContent = '✓ Image uploaded!';
                    statusDiv.className = 'success';
                    statusDiv.style.display = 'block';

                    updateLayersList();
                    createTextTexture();
                    e.target.value = '';
                };
                reader.readAsDataURL(file);
            });
        }

        function setupMouseControls() {
            const canvas = document.getElementById('preview-canvas');

            canvas.addEventListener('mousedown', (e) => {
                customizer.isDragging = true;
                customizer.previousMousePosition = { x: e.clientX, y: e.clientY };
            });

            canvas.addEventListener('mousemove', (e) => {
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

            canvas.addEventListener('mouseup', () => customizer.isDragging = false);
            canvas.addEventListener('mouseleave', () => customizer.isDragging = false);

            canvas.addEventListener('wheel', (e) => {
                e.preventDefault();
                const zoomSpeed = 0.1;
                if (e.deltaY < 0) customizer.zoom *= (1 + zoomSpeed);
                else customizer.zoom *= (1 - zoomSpeed);
                
                customizer.zoom = Math.max(0.5, Math.min(3, customizer.zoom));
                if (customizer.camera) customizer.camera.position.z = 2.5 / customizer.zoom;
            }, { passive: false });
        }

        function setupCartButtons() {
            document.querySelectorAll('.qty-adjust-prod').forEach(btn => {
                btn.addEventListener('click', () => {
                    const input = document.getElementById('product-quantity');
                    input.value = Math.max(1, parseInt(input.value) + parseInt(btn.dataset.value));
                    document.getElementById('product-total').textContent = '$' + (customizer.basePrice * input.value).toFixed(2);
                });
            });

            document.getElementById('reset-product').addEventListener('click', () => {
                if (confirm('Clear all layers and designs?')) {
                    customizer.textLayers = [];
                    customizer.uploadedImage = null;
                    customizer.selectedLayerId = null;
                    updateLayersList();
                    document.getElementById('upload-status').style.display = 'none';
                    createTextTexture();
                }
            });
        }

        function animate() {
            requestAnimationFrame(animate);
            customizer.renderer.render(customizer.scene, customizer.camera);
        }

        function onWindowResize() {
            const container = document.getElementById('preview-container');
            customizer.camera.aspect = container.clientWidth / container.clientHeight;
            customizer.camera.updateProjectionMatrix();
            customizer.renderer.setSize(container.clientWidth, container.clientHeight);
        }

        document.addEventListener('DOMContentLoaded', initializeScene);
    </script>

    <style>
        #preview-canvas { cursor: grab; }
        #preview-canvas:active { cursor: grabbing; }
        #upload-status { font-size: 0.9rem; padding: 0.75rem; border-radius: 0.5rem; margin-top: 0.75rem; border-left: 4px solid; }
        #upload-status.success { background-color: #d4edda; color: #155724; border-left-color: #155724; }
        #upload-status.error { background-color: #f8d7da; color: #721c24; border-left-color: #721c24; }
        .color-btn { transition: all 0.3s ease; }
        .color-btn:hover { transform: scale(1.05); }
        @media (max-width: 1200px) { section > div { width: 100% !important; } section { flex-direction: column !important; gap: 1.5rem; } }
        @media (max-width: 768px) { #preview-container { min-height: 400px; } section > div { max-height: none !important; } }
    </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {

                // Listen to ALL clicks on the webpage (This provides the 'async (e)' wrapper!)
                document.body.addEventListener('click', async (e) => {
                    
                    // ==========================================
                    // 1. ADD TO CART LOGIC (FIXED)
                    // ==========================================
                    if (e.target && e.target.id === 'add-product-cart') {
                        e.preventDefault();
                        console.log("🛒 Add to Cart clicked!");

                        const productType = (typeof customizer !== 'undefined') ? customizer.productType : 'product';
                        const basePrice = (typeof customizer !== 'undefined') ? customizer.basePrice : 12.00;
                        const productName = 'Custom ' + productType.charAt(0).toUpperCase() + productType.slice(1);

                        // FIX: Grab the default image URL for the mug
                            const defaultImage = 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?q=80&w=800&auto=format&fit=crop'; // (Shirt image)

                        if (window.app && window.app.addToCart) {
                            // We added 'defaultImage' as the 4th item here!
                            window.app.addToCart(productType + '_' + Date.now(), productName, basePrice, defaultImage);
                            window.app.showNotification('Design added to cart!', 'success');

                            e.target.style.display = 'none';

                            const actionsDiv = document.createElement('div');
                            actionsDiv.id = 'post-cart-actions';
                            actionsDiv.style.display = 'flex';
                            actionsDiv.style.gap = '10px';
                            actionsDiv.style.marginTop = '1rem';

                            actionsDiv.innerHTML = `
                                <a href="cart.php" class="btn btn-primary" style="flex: 1; text-align: center; background-color: #28a745; border: none; padding: 1rem; border-radius: 0.5rem; color: white; text-decoration: none; font-weight: bold;">
                                    Go to Cart 🛒
                                </a>
                                <button id="continue-shopping" class="btn btn-secondary" style="flex: 1; padding: 1rem; border-radius: 0.5rem; font-weight: bold; cursor: pointer;">
                                    Stay & Design
                                </button>
                            `;

                            e.target.parentNode.insertBefore(actionsDiv, e.target.nextSibling);

                            document.getElementById('continue-shopping').addEventListener('click', (ev) => {
                                ev.preventDefault();
                                actionsDiv.remove(); 
                                e.target.style.display = 'block'; 
                            });
                        }
                    }

                    // ==========================================
                    // 2. SAVE DESIGN (REAL SCREENSHOT MODE)
                    // ==========================================
                    if (e.target && e.target.id === 'save-product-design') {
                        e.preventDefault();
                        console.log("✅ Save Design clicked! Starting process...");

                        const saveBtn = e.target;
                        const originalText = saveBtn.innerHTML;
                        saveBtn.innerHTML = 'Saving to Profile... ⏳';
                        saveBtn.disabled = true;

                        try {
                            const productType = (typeof customizer !== 'undefined') ? customizer.productType : 'product';
                            const designName = 'My Custom ' + productType.charAt(0).toUpperCase() + productType.slice(1);
                            const canvasData = JSON.stringify({ color: (typeof customizer !== 'undefined') ? customizer.currentColor : '#ffffff' });

                            console.log("🚀 Taking a screenshot and sending data...");

                            // --- THE SCREENSHOT CAMERA ---
                            if (typeof customizer !== 'undefined' && customizer.renderer) {
                                customizer.renderer.render(customizer.scene, customizer.camera);
                            }
                            const canvasElement = document.getElementById('preview-canvas');
                            const imageScreenshot = canvasElement ? canvasElement.toDataURL('image/png') : null;
                            // ----------------------------------

                            const response = await fetch('api/save-design.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    name: designName,
                                    canvas_json: canvasData,
                                    preview_image: imageScreenshot
                                })
                            });

                            const rawText = await response.text();
                            console.log("📥 Raw Server Response:", rawText);

                            let result;
                            try {
                                result = JSON.parse(rawText);
                            } catch (parseErr) {
                                throw new Error("Server crashed. Look at the raw response in the Console.");
                            }

                            if (response.ok && result.success) {
                                console.log("🎉 Database saved successfully!");
                                if (window.app) window.app.showNotification('Design saved perfectly!', 'success');
                            } else {
                                throw new Error(result.message || 'Failed to save design');
                            }

                        } catch (error) {
                            console.error("❌ Save API Error:", error);
                            if (window.app) window.app.showNotification('Error: ' + error.message, 'error');
                        } finally {
                            saveBtn.innerHTML = originalText;
                            saveBtn.disabled = false;
                        }
                    }
                });
            });
            </script>

<?php include 'includes/footer.php'; ?>