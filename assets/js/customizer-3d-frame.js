/**
 * Advanced 3D Frame Customizer Engine (ISOLATED)
 * Triangle Printing Solutions
 */

let canvas, fabricCanvas;
let currentImage = null;
let designData = {
    backgroundColor: '#ffffff',
    frameStyle: 'minimal',
    posterSize: '8x10',
    resolution: 300
};

const sizePrices = { '8x10': 25, '11x14': 35, '16x20': 45, '18x24': 55, '24x36': 75 };

// Three.js Variables
let scene, camera, renderer, frameModel, canvasTexture;

document.addEventListener('DOMContentLoaded', () => {
    initializeFabric();
    initializeThreeJSPreview();
    
    if(document.getElementById('image-upload')) {
        setupFrameEventListeners();
        updatePrice();
    }
});

// ========== 1. INITIALIZE 2D FABRIC CANVAS ==========
function initializeFabric() {
    fabricCanvas = new fabric.Canvas('canvas', {
        width: 600,
        height: 600,
        backgroundColor: designData.backgroundColor,
        selection: true,
        preserveObjectStacking: true
    });
    
    drawSafePrintArea();

    fabricCanvas.on('after:render', () => {
        if (canvasTexture) canvasTexture.needsUpdate = true;
    });
}

function drawSafePrintArea() {
    const safeMargin = 20;
    fabricCanvas.add(new fabric.Rect({
        left: safeMargin, top: safeMargin,
        width: fabricCanvas.width - (safeMargin * 2),
        height: fabricCanvas.height - (safeMargin * 2),
        fill: 'transparent', stroke: '#E31E24',
        strokeDashArray: [5, 5], strokeWidth: 2,
        selectable: false, evented: false
    }));
    fabricCanvas.renderAll();
}

// ========== 2. INITIALIZE 3D THREE.JS PREVIEW ==========
function initializeThreeJSPreview() {
    const container = document.getElementById('preview-container');
    if(!container) return;
    
    container.innerHTML = ''; 

    scene = new THREE.Scene();
    scene.background = new THREE.Color('#f4f7f6');

    camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 100);
    camera.position.set(0, 0, 5);

    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.outputEncoding = THREE.sRGBEncoding;
    renderer.shadowMap.enabled = true;
    container.appendChild(renderer.domElement);

    // Boosted Lighting so the frame is never completely black
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.9);
    scene.add(ambientLight);
    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.6);
    directionalLight.position.set(5, 5, 5);
    scene.add(directionalLight);

    // Enable Zoom and Pan explicitly
    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    controls.enableZoom = true;
    controls.enablePan = true;
    controls.minDistance = 1;
    controls.maxDistance = 15;

    canvasTexture = new THREE.CanvasTexture(document.getElementById('canvas'));
    canvasTexture.encoding = THREE.sRGBEncoding;
    canvasTexture.flipY = true;

    const loader = new THREE.GLTFLoader();
    
    loader.load('/triangle-ecommerce/assets/models/photo_frame.glb', function(gltf) {
        frameModel = gltf.scene;

        // SMART AUTO-SCALING: Mathematically fits the model to the screen
        const box = new THREE.Box3().setFromObject(frameModel);
        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3());

        // Center it
        frameModel.position.x += (frameModel.position.x - center.x);
        frameModel.position.y += (frameModel.position.y - center.y);
        frameModel.position.z += (frameModel.position.z - center.z);

        // Scale it down safely
        const maxDim = Math.max(size.x, size.y, size.z);
        const targetSize = 3; // Fits nicely inside the camera view
        const scale = targetSize / maxDim;
        frameModel.scale.set(scale, scale, scale);

        // Apply texture
        frameModel.traverse((child) => {
            if (child.isMesh) {
                child.material.map = canvasTexture;
                // Ensures material renders on both sides, fixing black spots
                child.material.side = THREE.DoubleSide; 
                child.material.needsUpdate = true;
            }
        });
        scene.add(frameModel);
    }, undefined, function (error) {
        console.error('Error loading 3D model:', error);
    });

    function animate() {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    }
    animate();

    // Handle window resize for 3D canvas
    window.addEventListener('resize', () => {
        camera.aspect = container.clientWidth / container.clientHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(container.clientWidth, container.clientHeight);
    });
}

// ========== 3. CONTROLS & LOGIC ==========
function setupFrameEventListeners() {
    document.getElementById('image-upload').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file || !file.type.match('image.*')) return;
        
        const reader = new FileReader();
        reader.onload = (event) => {
            fabric.Image.fromURL(event.target.result, (fabricImg) => {
                fabricCanvas.getObjects('image').forEach(obj => fabricCanvas.remove(obj));
                fabricImg.scaleToWidth(300);
                fabricCanvas.centerObject(fabricImg);
                fabricCanvas.add(fabricImg);
                fabricCanvas.setActiveObject(fabricImg);
                fabricCanvas.renderAll();
                
                currentImage = fabricImg;
                if(canvasTexture) canvasTexture.needsUpdate = true; 
            });
        };
        reader.readAsDataURL(file);
    });

    document.getElementById('bg-color').addEventListener('change', (e) => {
        designData.backgroundColor = e.target.value;
        fabricCanvas.setBackgroundColor(designData.backgroundColor, () => {
            fabricCanvas.renderAll();
            if(canvasTexture) canvasTexture.needsUpdate = true;
        });
    });

    document.getElementById('zoom-slider').addEventListener('input', (e) => {
        document.getElementById('zoom-value').textContent = e.target.value;
        if (currentImage) {
            currentImage.scaleToWidth((300 * e.target.value) / 100);
            fabricCanvas.centerObject(currentImage);
            fabricCanvas.renderAll();
            if(canvasTexture) canvasTexture.needsUpdate = true;
        }
    });

    document.getElementById('rotation-slider').addEventListener('input', (e) => {
        document.getElementById('rotation-value').textContent = e.target.value;
        if (currentImage) {
            currentImage.rotate(e.target.value);
            fabricCanvas.renderAll();
            if(canvasTexture) canvasTexture.needsUpdate = true;
        }
    });

    document.getElementById('poster-size').addEventListener('change', (e) => {
        designData.posterSize = e.target.value;
        updatePrice();
    });
}

function updatePrice() {
    const price = sizePrices[designData.posterSize] || 25;
    const priceDisplay = document.getElementById('price-display');
    if(priceDisplay) priceDisplay.textContent = '$' + price.toFixed(2);
}

    // ========== 4. ADD TO CART & QUANTITY LOGIC ==========

    // 1. Make the + and - quantity buttons work
    document.querySelectorAll('.qty-adjust').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const input = document.getElementById('quantity');
            let val = parseInt(input.value) + parseInt(e.target.dataset.value);
            if (val < 1) val = 1; // Don't allow less than 1
            input.value = val;
        });
    });

    // 2. Make the Add to Cart button work
    document.getElementById('add-to-cart').addEventListener('click', () => {
        
        // Grab the user's selections
        const size = document.getElementById('poster-size').value;
        const styleSelect = document.getElementById('frame-style');
        const styleName = styleSelect.options[styleSelect.selectedIndex].text;
        const quantity = parseInt(document.getElementById('quantity').value) || 1;

        // Calculate the exact price from your price list
        const price = sizePrices[size] || 25;
        
        // Create a detailed product name so the receipt looks professional
        const productName = `Custom Frame (${size} - ${styleName})`;

        // Use the high-quality Unsplash image we assigned to frames earlier
        const frameImage = 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=800&auto=format&fit=crop';
        
        // Create a unique ID for this specific size and style so they stack correctly in the cart
        const productId = 'frame_' + size + '_' + styleSelect.value;

        // Send it to the global cart system!
        if (typeof window.app !== 'undefined' && typeof window.app.addToCart === 'function') {
            // Loop it so if they chose Quantity: 3, it adds 3 to the cart
            for (let i = 0; i < quantity; i++) {
                window.app.addToCart(productId, productName, price, frameImage);
            }
        } else {
            console.error("Cart system is missing on this page!");
        }
    });