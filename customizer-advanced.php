<?php
/**
 * Advanced Product Customizer - Fabric.js + Three.js Integration
 * Triangle Printing Solutions
 * 
 * Features:
 * - Fabric.js for 2D design with full drag/resize/rotate capabilities
 * - Three.js for 3D mug preview
 * - Real-time texture sync
 * - Layer management
 * - Full Canva-like editing experience
 */
session_start();
require_once 'db.php';

$type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : 'mug';
$page_title = ucfirst($type) . ' Customizer';

$products = [
    'mug' => [
        'name' => 'Custom Mug',
        'price' => 12,
        'colors' => ['#FFFFFF', '#000000', '#E31E24', '#0066CC', '#00AA00'],
        'colorNames' => ['White', 'Black', 'Red', 'Blue', 'Green'],
        'description' => 'Design with drag, resize, rotate - like Canva!'
    ],
];

$product = $products[$type] ?? $products['mug'];
include 'includes/header.php';
?>

<section style="background-color: var(--light-gray); padding: 2rem; margin-bottom: 0;">
    <div class="container-md">
        <h1 style="margin-bottom: 0.5rem;"><?php echo $product['name']; ?> Advanced Customizer</h1>
        <p style="color: var(--text-light);">Drag, resize, rotate text & images - then preview on your 3D mug</p>
    </div>
</section>

<section style="display: flex; gap: 1rem; padding: 2rem; max-width: 1600px; margin: 0 auto; min-height: 700px;">
    
    <!-- Left: 2D Design Canvas (Fabric.js) -->
    <div style="flex: 1; display: flex; flex-direction: column; gap: 1rem;">
        <div style="background: white; border: 1px solid #ddd; border-radius: 0.5rem; padding: 0.75rem;">
            <h4>2D Design Editor - Drag, Resize, Rotate</h4>
        </div>
        
        <canvas id="fabric-canvas" 
                style="border: 2px solid #E31E24; border-radius: 0.5rem; background: white; cursor: crosshair; 
                        width: 100%; height: 500px; max-width: 600px; display: block; margin: 0 auto;">
        </canvas>

        <!-- Tools Panel -->
        <div style="background: white; border: 1px solid #ddd; border-radius: 0.5rem; padding: 1rem;">
            
            <!-- Add Text -->
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Add Text</label>
                <div style="display: flex; gap: 0.5rem;">
                    <input id="text-input" type="text" placeholder="Enter text..." 
                           style="flex: 1; padding: 0.5rem; border: 1px solid #ddd; border-radius: 0.25rem;">
                    <button id="add-text-btn" 
                            style="padding: 0.5rem 1rem; background: #E31E24; color: white; border: none; 
                                   border-radius: 0.25rem; cursor: pointer; font-weight: 600;">
                        Add Text
                    </button>
                </div>
            </div>

            <!-- Add Image -->
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Add Image</label>
                <input id="image-upload" type="file" accept="image/*" 
                       style="display: block; width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 0.25rem;">
            </div>

            <!-- Text Styling -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.25rem;">Font Size</label>
                    <input id="font-size" type="range" min="10" max="100" value="40" 
                           style="width: 100%; cursor: pointer;">
                    <span id="font-size-display" style="font-size: 0.75rem;">40px</span>
                </div>
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.25rem;">Font Color</label>
                    <input id="font-color" type="color" value="#000000" style="width: 100%; height: 35px; cursor: pointer;">
                </div>
            </div>

            <!-- Font Family -->
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Font</label>
                <select id="font-family" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 0.25rem;">
                    <option value="Arial">Arial</option>
                    <option value="Helvetica">Helvetica</option>
                    <option value="Georgia">Georgia</option>
                    <option value="Courier New">Courier New</option>
                    <option value="Verdana">Verdana</option>
                </select>
            </div>

            <!-- Layer Controls -->
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Selected Layer</label>
                <div style="display: flex; gap: 0.25rem;">
                    <button id="delete-layer" 
                            style="flex: 1; padding: 0.5rem; background: #ff6b6b; color: white; border: none; 
                                   border-radius: 0.25rem; cursor: pointer; font-size: 0.85rem;">
                        Delete
                    </button>
                    <button id="forward-layer" 
                            style="flex: 1; padding: 0.5rem; background: #4CAF50; color: white; border: none; 
                                   border-radius: 0.25rem; cursor: pointer; font-size: 0.85rem;">
                        Forward
                    </button>
                    <button id="backward-layer" 
                            style="flex: 1; padding: 0.5rem; background: #2196F3; color: white; border: none; 
                                   border-radius: 0.25rem; cursor: pointer; font-size: 0.85rem;">
                        Backward
                    </button>
                </div>
            </div>

            <!-- Product Color -->
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Mug Color</label>
                <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.5rem;">
                    <?php foreach ($product['colors'] as $idx => $color): ?>
                        <button class="color-btn" data-color="<?php echo $color; ?>" 
                                style="width: 100%; height: 40px; border: 2px solid transparent; border-radius: 0.25rem; 
                                       background-color: <?php echo $color; ?>; cursor: pointer; transition: all 0.2s;"
                                title="<?php echo $product['colorNames'][$idx]; ?>">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: 3D Preview (Three.js) -->
    <div style="flex: 1; display: flex; flex-direction: column;">
        <div style="background: white; border: 1px solid #ddd; border-radius: 0.5rem; padding: 0.75rem;">
            <h4>3D Mug Preview - Rotate to view</h4>
        </div>
        
        <canvas id="three-canvas" 
                style="border: 2px solid #333; border-radius: 0.5rem; flex: 1; max-height: 600px; 
                       background: linear-gradient(135deg, #f5f5f5, #e0e0e0);">
        </canvas>
    </div>
</section>

<!-- Load Fabric.js from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
<!-- Load Three.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

<script>
// ============================================================================
// ADVANCED PRODUCT CUSTOMIZER - Fabric.js + Three.js
// ============================================================================

const designState = {
    // Fabric Canvas
    fabricCanvas: null,
    // Three.js
    threeScene: null,
    threeCamera: null,
    threeRenderer: null,
    mugGroup: null,
    productMesh: null,
    // Product
    currentColor: '<?php echo $product['colors'][0]; ?>',
    productType: '<?php echo $type; ?>',
    // Interaction
    isDraggingMug: false,
    mugRotation: { x: 0, y: 0 },
    previousMousePos: { x: 0, y: 0 },
    zoom: 1
};

// ============================================================================
// INITIALIZE FABRIC.JS CANVAS
// ============================================================================
function initFabricCanvas() {
    const container = document.getElementById('fabric-canvas');
    
    designState.fabricCanvas = new fabric.Canvas('fabric-canvas', {
        width: 600,
        height: 600,
        backgroundColor: '#ffffff',
        selection: true,
        preserveObjectStacking: true
    });

    // Add guides
    designState.fabricCanvas.renderOnAddRemove = false;

    // Event: Object selected
    designState.fabricCanvas.on('selection:created', updateThreeTexture);
    designState.fabricCanvas.on('selection:updated', updateThreeTexture);
    designState.fabricCanvas.on('selection:cleared', updateThreeTexture);
    designState.fabricCanvas.on('object:modified', updateThreeTexture);
    designState.fabricCanvas.on('object:added', updateThreeTexture);
    designState.fabricCanvas.on('object:removed', updateThreeTexture);
}

// ============================================================================
// ADD TEXT TO FABRIC CANVAS
// ============================================================================
function addTextToCanvas() {
    const text = document.getElementById('text-input').value.trim();
    if (!text) {
        alert('Please enter text');
        return;
    }

    const fontSize = parseInt(document.getElementById('font-size').value);
    const fontFamily = document.getElementById('font-family').value;
    const color = document.getElementById('font-color').value;

    const fabricText = new fabric.Text(text, {
        left: designState.fabricCanvas.width / 2,
        top: designState.fabricCanvas.height / 2,
        fontSize: fontSize,
        fontFamily: fontFamily,
        fill: color,
        fontWeight: 'normal',
        originX: 'center',
        originY: 'center'
    });

    designState.fabricCanvas.add(fabricText);
    designState.fabricCanvas.setActiveObject(fabricText);
    designState.fabricCanvas.renderAll();
    document.getElementById('text-input').value = '';
    
    updateThreeTexture();
}

// ============================================================================
// ADD IMAGE TO FABRIC CANVAS
// ============================================================================
function handleImageUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    if (file.size > 5 * 1024 * 1024) {
        alert('File too large (max 5MB)');
        return;
    }

    const reader = new FileReader();
    reader.onload = function(event) {
        fabric.Image.fromURL(event.target.result, (img) => {
            // Scale image to fit canvas
            const scale = Math.min(
                (designState.fabricCanvas.width * 0.6) / img.width,
                (designState.fabricCanvas.height * 0.6) / img.height
            );

            img.scale(scale);
            img.set({
                left: designState.fabricCanvas.width / 2,
                top: designState.fabricCanvas.height / 2,
                originX: 'center',
                originY: 'center'
            });

            designState.fabricCanvas.add(img);
            designState.fabricCanvas.setActiveObject(img);
            designState.fabricCanvas.renderAll();
            updateThreeTexture();
        });
    };
    reader.readAsDataURL(file);
}

// ============================================================================
// DELETE SELECTED LAYER
// ============================================================================
function deleteSelectedLayer() {
    const activeObject = designState.fabricCanvas.getActiveObject();
    if (!activeObject) {
        alert('Please select an object to delete');
        return;
    }
    designState.fabricCanvas.remove(activeObject);
    designState.fabricCanvas.renderAll();
    updateThreeTexture();
}

// ============================================================================
// LAYER CONTROLS (Forward/Backward)
// ============================================================================
function bringForward() {
    const activeObject = designState.fabricCanvas.getActiveObject();
    if (!activeObject) return;
    designState.fabricCanvas.bringForward(activeObject);
    designState.fabricCanvas.renderAll();
}

function sendBackward() {
    const activeObject = designState.fabricCanvas.getActiveObject();
    if (!activeObject) return;
    designState.fabricCanvas.sendBackwards(activeObject);
    designState.fabricCanvas.renderAll();
}

// ============================================================================
// UPDATE FONT SIZE
// ============================================================================
document.getElementById('font-size')?.addEventListener('input', (e) => {
    document.getElementById('font-size-display').textContent = e.target.value + 'px';
    const activeObject = designState.fabricCanvas?.getActiveObject();
    if (activeObject && activeObject instanceof fabric.Text) {
        activeObject.set({ fontSize: parseInt(e.target.value) });
        designState.fabricCanvas.renderAll();
        updateThreeTexture();
    }
});

// ============================================================================
// UPDATE FONT COLOR
// ============================================================================
document.getElementById('font-color')?.addEventListener('change', (e) => {
    const activeObject = designState.fabricCanvas?.getActiveObject();
    if (activeObject && activeObject instanceof fabric.Text) {
        activeObject.set({ fill: e.target.value });
        designState.fabricCanvas.renderAll();
        updateThreeTexture();
    }
});

// ============================================================================
// UPDATE FONT FAMILY
// ============================================================================
document.getElementById('font-family')?.addEventListener('change', (e) => {
    const activeObject = designState.fabricCanvas?.getActiveObject();
    if (activeObject && activeObject instanceof fabric.Text) {
        activeObject.set({ fontFamily: e.target.value });
        designState.fabricCanvas.renderAll();
        updateThreeTexture();
    }
});

// ============================================================================
// EVENT HANDLERS
// ============================================================================
document.getElementById('add-text-btn')?.addEventListener('click', addTextToCanvas);
document.getElementById('image-upload')?.addEventListener('change', handleImageUpload);
document.getElementById('delete-layer')?.addEventListener('click', deleteSelectedLayer);
document.getElementById('forward-layer')?.addEventListener('click', bringForward);
document.getElementById('backward-layer')?.addEventListener('click', sendBackward);

// ============================================================================
// INITIALIZE THREE.JS SCENE
// ============================================================================
function initThreeScene() {
    const container = document.getElementById('three-canvas');
    const width = container.clientWidth;
    const height = container.clientHeight;

    // Scene
    designState.threeScene = new THREE.Scene();
    designState.threeScene.background = new THREE.Color(0xf5f5f5);

    // Camera
    designState.threeCamera = new THREE.PerspectiveCamera(60, width / height, 0.1, 1000);
    designState.threeCamera.position.set(0, 0.3, 3.2);
    designState.threeCamera.lookAt(0, 0, 0);

    // Renderer
    designState.threeRenderer = new THREE.WebGLRenderer({
        canvas: container,
        antialias: true,
        alpha: true,
        powerPreference: 'high-performance'
    });
    designState.threeRenderer.setSize(width, height);
    designState.threeRenderer.setPixelRatio(window.devicePixelRatio);
    designState.threeRenderer.shadowMap.enabled = true;
    designState.threeRenderer.shadowMap.type = THREE.PCFShadowShadowMap;

    // Lighting
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
    designState.threeScene.add(ambientLight);

    const keyLight = new THREE.DirectionalLight(0xffffff, 1);
    keyLight.position.set(4, 6, 5);
    keyLight.castShadow = true;
    keyLight.shadow.mapSize.width = 2048;
    keyLight.shadow.mapSize.height = 2048;
    keyLight.shadow.camera.left = -5;
    keyLight.shadow.camera.right = 5;
    keyLight.shadow.camera.top = 5;
    keyLight.shadow.camera.bottom = -5;
    designState.threeScene.add(keyLight);

    const fillLight = new THREE.DirectionalLight(0xffffff, 0.4);
    fillLight.position.set(-3, 2, -4);
    designState.threeScene.add(fillLight);

    const rimLight = new THREE.DirectionalLight(0xffffff, 0.3);
    rimLight.position.set(2, 3, -6);
    designState.threeScene.add(rimLight);

    // Create mug
    createMug();

    // Mouse controls
    setupThreeMouseControls();

    // Start animation loop
    animateThree();

    // Handle window resize
    window.addEventListener('resize', () => {
        const w = container.clientWidth;
        const h = container.clientHeight;
        designState.threeCamera.aspect = w / h;
        designState.threeCamera.updateProjectionMatrix();
        designState.threeRenderer.setSize(w, h);
    });
}

// ============================================================================
// CREATE MUG MODEL
// ============================================================================
function createMug() {
    const group = new THREE.Group();

    const material = new THREE.MeshStandardMaterial({
        color: designState.currentColor,
        roughness: 0.4,
        metalness: 0.05
    });

    // Mug body
    const bodyGeometry = new THREE.CylinderGeometry(0.7, 0.65, 1.2, 128, 32, true);
    const body = new THREE.Mesh(bodyGeometry, material);
    body.castShadow = true;
    body.receiveShadow = true;
    group.add(body);

    // Rim
    const rimGeometry = new THREE.TorusGeometry(0.72, 0.035, 20, 128);
    const rim = new THREE.Mesh(rimGeometry, material);
    rim.position.y = 0.65;
    rim.rotation.x = Math.PI / 2;
    rim.castShadow = true;
    rim.receiveShadow = true;
    group.add(rim);

    // Bottom
    const bottomGeometry = new THREE.CylinderGeometry(0.65, 0.62, 0.08, 128);
    const bottom = new THREE.Mesh(bottomGeometry, material);
    bottom.position.y = -0.62;
    bottom.castShadow = true;
    bottom.receiveShadow = true;
    group.add(bottom);

    // Handle
    const handleCurve = new THREE.CatmullRomCurve3([
        new THREE.Vector3(0.65, 0.2, 0),
        new THREE.Vector3(0.85, 0.35, 0),
        new THREE.Vector3(0.95, 0.5, 0),
        new THREE.Vector3(0.9, 0.65, 0),
        new THREE.Vector3(0.75, 0.75, 0),
        new THREE.Vector3(0.55, 0.65, 0)
    ]);
    const tubeGeometry = new THREE.TubeGeometry(handleCurve, 20, 0.08, 12, false);
    const handle = new THREE.Mesh(tubeGeometry, material);
    handle.castShadow = true;
    handle.receiveShadow = true;
    group.add(handle);

    designState.productMesh = body;
    designState.mugGroup = group;
    designState.threeScene.add(group);

    group.rotation.x = 0.15;
    group.rotation.y = -0.35;
}

// ============================================================================
// UPDATE THREE.JS TEXTURE FROM FABRIC CANVAS
// ============================================================================
function updateThreeTexture() {
    if (!designState.fabricCanvas || !designState.productMesh) return;

    // Get Fabric canvas as data URL
    const fabricCanvasData = designState.fabricCanvas.toDataURL({
        format: 'png',
        quality: 1,
        multiplier: 2
    });

    // Create image and map to Three.js texture
    const img = new Image();
    img.onload = function() {
        const canvas = document.createElement('canvas');
        canvas.width = 1024;
        canvas.height = 1024;
        const ctx = canvas.getContext('2d');

        // Scale and draw Fabric canvas to Three.js canvas
        ctx.drawImage(img, 0, 0, 1024, 1024);

        const texture = new THREE.CanvasTexture(canvas);
        texture.needsUpdate = true;
        designState.productMesh.material.map = texture;
        designState.productMesh.material.color.set('#ffffff');
        designState.productMesh.material.needsUpdate = true;
    };
    img.src = fabricCanvasData;
}

// ============================================================================
// CHANGE MUG COLOR
// ============================================================================
function changeMugColor(color) {
    designState.currentColor = color;
    designState.mugGroup.traverse((child) => {
        if (child.isMesh && child instanceof THREE.Mesh) {
            child.material.color.set(color);
            child.material.needsUpdate = true;
        }
    });
}

document.querySelectorAll('.color-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const color = btn.dataset.color;
        changeMugColor(color);

        // Update button styling
        document.querySelectorAll('.color-btn').forEach(b => {
            b.style.borderColor = 'transparent';
        });
        btn.style.borderColor = '#E31E24';
    });
});

// ============================================================================
// THREE.JS MOUSE CONTROLS
// ============================================================================
function setupThreeMouseControls() {
    const canvas = designState.threeRenderer.domElement;

    canvas.addEventListener('mousedown', (e) => {
        designState.isDraggingMug = true;
        designState.previousMousePos = { x: e.clientX, y: e.clientY };
    });

    canvas.addEventListener('mousemove', (e) => {
        if (!designState.isDraggingMug || !designState.mugGroup) return;

        const deltaX = e.clientX - designState.previousMousePos.x;
        const deltaY = e.clientY - designState.previousMousePos.y;

        designState.mugRotation.y += deltaX * 0.005;
        designState.mugRotation.x += deltaY * 0.005;

        designState.mugGroup.rotation.y = designState.mugRotation.y;
        designState.mugGroup.rotation.x = Math.max(-Math.PI / 2, Math.min(Math.PI / 2, designState.mugRotation.x));

        designState.previousMousePos = { x: e.clientX, y: e.clientY };
    });

    canvas.addEventListener('mouseup', () => {
        designState.isDraggingMug = false;
    });

    canvas.addEventListener('mouseleave', () => {
        designState.isDraggingMug = false;
    });

    // Zoom with mouse wheel
    canvas.addEventListener('wheel', (e) => {
        e.preventDefault();
        designState.zoom += (e.deltaY < 0 ? 0.1 : -0.1);
        designState.zoom = Math.max(0.5, Math.min(3, designState.zoom));
        designState.threeCamera.position.z = 3.2 / designState.zoom;
    }, { passive: false });
}

// ============================================================================
// ANIMATION LOOP
// ============================================================================
function animateThree() {
    requestAnimationFrame(animateThree);

    // Gentle auto-rotation when not dragging
    if (!designState.isDraggingMug && designState.mugGroup) {
        designState.mugGroup.rotation.y += 0.002;
    }

    designState.threeRenderer.render(designState.threeScene, designState.threeCamera);
}

// ============================================================================
// INITIALIZATION
// ============================================================================
document.addEventListener('DOMContentLoaded', () => {
    initFabricCanvas();
    initThreeScene();
    updateThreeTexture();
});
</script>

<?php include 'includes/footer.php'; ?>
