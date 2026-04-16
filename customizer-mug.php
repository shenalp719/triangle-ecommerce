<?php
/**
 * Product Customizer - Mug Preview
 * Triangle Printing Solutions
 */
session_start();
require_once 'db.php';

// Hardcoded for the mug file
$type = 'mug';
$page_title = 'Mug Customizer';

$product = [
    'name' => 'Custom Mug',
    'price' => 12,
    'colors' => ['#FFFFFF', '#000000', '#E31E24', '#0066CC', '#00AA00'],
    'colorNames' => ['White', 'Black', 'Red', 'Blue', 'Green'],
    'sizes' => ['11oz', '15oz'],
    'description' => 'Create your perfect custom mug with text and images',
    '3d' => true
];

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
                    Add Text to Mug
                </button>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Add Image to Mug</label>
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

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Size</label>
                <select id="product-size" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem; cursor: pointer;">
                    <?php foreach ($product['sizes'] as $size): ?>
                        <option value="<?php echo $size; ?>"><?php echo $size; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

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

            <button style="width: 100%; padding: 1rem; background-color: var(--primary-red); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; margin-bottom: 0.75rem; transition: background-color 0.3s;" id="add-product-cart">
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
        const app = {
            showNotification: (msg, type) => console.log(`[${type}] ${msg}`),
            addToCart: (id, name, price, details) => console.log(`Added to cart: ${name}`)
        };

        const customizer = {
            productType: 'mug',
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
                antialias: true, alpha: true 
            });
            customizer.renderer.setSize(width, height);
            customizer.renderer.setPixelRatio(window.devicePixelRatio);
            customizer.renderer.shadowMap.enabled = true;
            customizer.renderer.shadowMap.type = THREE.PCFShadowShadowMap;

            const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
            customizer.scene.add(ambientLight);

            const keyLight = new THREE.DirectionalLight(0xffffff, 1);
            keyLight.position.set(4, 6, 5);
            keyLight.castShadow = true;
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

        function createProductMesh() {
            if (customizer.productGroup) customizer.scene.remove(customizer.productGroup);

            customizer.productGroup = new THREE.Group();
            customizer.scene.add(customizer.productGroup);

            const loader = new THREE.GLTFLoader();
            loader.load(
                `assets/models/mug.glb`,
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

        // Clean, single-canvas engine for Mugs
        function createTextTexture() {
            const canvas = document.createElement('canvas');
            canvas.width = 1024;
            canvas.height = 1024;
            const ctx = canvas.getContext('2d');

            ctx.fillStyle = customizer.currentColor;
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            function drawTexts() {
                customizer.textLayers.forEach(layer => {
                    ctx.font = `${layer.italic ? 'italic ' : ''}${layer.bold ? 'bold ' : ''}${layer.size}px ${layer.font}`;
                    ctx.fillStyle = layer.color;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText(layer.content, layer.x !== undefined ? layer.x : 512, layer.y !== undefined ? layer.y : 512);
                });
            }

            function applyTexture() {
                const texture = new THREE.CanvasTexture(canvas);
                texture.flipY = true;
                texture.needsUpdate = true;

                if (customizer.productGroup) {
                    customizer.productGroup.traverse((child) => {
                        if (child.isMesh && child.material) {
                            child.material.color.set('#ffffff');
                            child.material.map = texture;
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
                    ctx.drawImage(img, x, y, imgWidth, imgHeight);
                    drawTexts();
                    applyTexture();
                };
                img.src = customizer.uploadedImage.data || customizer.uploadedImage;
            } else {
                drawTexts();
                applyTexture();
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

            const textLayer = {
                id: Date.now(),
                content: textContent,
                size: parseInt(document.getElementById('text-size').value),
                font: document.getElementById('text-font').value,
                color: document.getElementById('text-color').value,
                bold: customizer.textStyles.bold,
                italic: customizer.textStyles.italic,
                x: 512,
                y: 512 + (customizer.textLayers.length * 40)
            };

            customizer.textLayers.push(textLayer);
            updateLayersList();
            document.getElementById('text-content').value = '';
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
                        <div style="font-weight: 600; font-size: 0.9rem;">📸 Image</div>
                        <button onclick="removeImageLayer(event)" style="padding: 0.25rem 0.5rem; background-color: #ff6b6b; color: white; border: none; border-radius: 0.25rem; cursor: pointer; font-size: 0.75rem; margin-top: 0.5rem; width: 100%;">Remove</button>
                    </div>`;
            }

            html += customizer.textLayers.map((layer) => `
                <div style="padding: 0.75rem; margin-bottom: 0.5rem; background-color: #f9f9f9; border-left: 3px solid ${layer.color}; border-radius: 0.25rem; cursor: pointer;" class="layer-item" onclick="selectLayer(${layer.id})">
                    <div style="font-weight: 600; font-size: 0.9rem; word-break: break-word;">${layer.content.substring(0, 25)}</div>
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
        }

        function removeImageLayer(event) {
            if (event) event.stopPropagation();
            customizer.uploadedImage = null;
            updateLayersList();
            createTextTexture();
        }

        function setupImageUpload() {
            document.getElementById('product-image-upload').addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(event) {
                    customizer.uploadedImage = {
                        data: event.target.result,
                        x: 512, y: 512, width: 300, height: 300
                    };
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
                    updateLayersList();
                    updateProductColor();
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
        .color-btn { transition: all 0.3s ease; }
        .color-btn:hover { transform: scale(1.05); }
        @media (max-width: 1200px) {
            section > div { width: 100% !important; }
            section { flex-direction: column !important; gap: 1.5rem; }
        }
    </style>

<?php include 'includes/footer.php'; ?>