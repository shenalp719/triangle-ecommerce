/**
 * Fabric.js Customizer Engine
 * Frame Customizer - Triangle Printing Solutions
 */

let canvas, ctx;
let fabricCanvas;
let currentImage = null;
let designData = {
    backgroundColor: '#ffffff',
    frameStyle: 'minimal',
    posterSize: '8x10',
    resolution: 300,
    image: null
};

// Price mapping for sizes
const sizePrices = {
    '8x10': 25,
    '11x14': 35,
    '16x20': 45,
    '18x24': 55,
    '24x36': 75
};

document.addEventListener('DOMContentLoaded', () => {
    initializeCustomizer();
    setupEventListeners();
});

// ========== INITIALIZE CANVAS ==========
function initializeCustomizer() {
    const canvasElement = document.getElementById('canvas');
    
    fabricCanvas = new fabric.Canvas('canvas', {
        width: 600,
        height: 600,
        backgroundColor: designData.backgroundColor,
        selection: true,
        preserveObjectStacking: true
    });
    
    // Initialize preview canvas
    const previewCanvas = document.getElementById('preview-canvas');
    previewCanvas.width = 280;
    previewCanvas.height = 280;
    previewCanvas.style.backgroundColor = designData.backgroundColor;
    
    // Draw safe print area boundary (red dashed line)
    drawSafePrintArea();
}

function drawSafePrintArea() {
    // Safe print area (usually 0.5" from edge)
    const safeMargin = 20;
    fabricCanvas.add(
        new fabric.Rect({
            left: safeMargin,
            top: safeMargin,
            width: fabricCanvas.width - (safeMargin * 2),
            height: fabricCanvas.height - (safeMargin * 2),
            fill: 'transparent',
            stroke: '#E31E24',
            strokeDashArray: [5, 5],
            strokeWidth: 2,
            selectable: false,
            evented: false
        })
    );
    
    fabricCanvas.renderAll();
}

// ========== IMAGE UPLOAD & HANDLING ==========
document.getElementById('image-upload').addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;
    
    // Validate file
    if (!file.type.match('image.*')) {
        app.showNotification('Please select a valid image file', 'error');
        return;
    }
    
    if (file.size > 10 * 1024 * 1024) {
        app.showNotification('File size must be less than 10MB', 'error');
        return;
    }
    
    // Check DPI when file loads
    const reader = new FileReader();
    reader.onload = (event) => {
        const img = new Image();
        img.onload = () => {
            // Estimate DPI based on file size and dimensions
            const estimatedDPI = Math.sqrt((file.size * 8) / (img.width * img.height));
            
            if (estimatedDPI < 150) {
                app.showNotification('⚠️ Low DPI detected. Consider using a higher resolution image for best print quality.', 'warning');
            }
            
            // Add to canvas
            fabric.Image.fromURL(event.target.result, (fabricImg) => {
                // Remove previous image
                fabricCanvas.getObjects('image').forEach(obj => fabricCanvas.remove(obj));
                
                // Scale image to fit canvas
                fabricImg.scaleToWidth(300);
                fabricCanvas.centerObject(fabricImg);
                fabricCanvas.add(fabricImg);
                fabric Canvas.setActiveObject(fabricImg);
                fabricCanvas.renderAll();
                
                currentImage = fabricImg;
                updatePreview();
                
                app.showNotification('Image uploaded successfully!', 'success');
            });
        };
        img.src = event.target.result;
    };
    reader.readAsDataURL(file);
});

// ========== FRAME STYLE CONTROLS ==========
document.getElementById('frame-style').addEventListener('change', (e) => {
    designData.frameStyle = e.target.value;
    updateFrameStyle();
    updatePreview();
});

function updateFrameStyle() {
    const styles = {
        'minimal': { color: '#111111', label: 'Minimal' },
        'wood': { color: '#8B4513', label: 'Wood' },
        'gold': { color: '#FFD700', label: 'Gold' },
        'white': { color: '#FFFFFF', label: 'White' },
        'none': { color: 'transparent', label: 'No Frame' }
    };
    
    const style = styles[designData.frameStyle];
    // Update visual frame representation
    console.log('Frame style updated:', style.label);
}

// ========== BACKGROUND COLOR ==========
document.getElementById('bg-color').addEventListener('change', (e) => {
    designData.backgroundColor = e.target.value;
    fabricCanvas.setBackgroundColor(designData.backgroundColor, () => {
        fabricCanvas.renderAll();
        updatePreview();
    });
});

// ========== ZOOM & ROTATION CONTROLS ==========
document.getElementById('zoom-slider').addEventListener('input', (e) => {
    const zoomValue = e.target.value;
    document.getElementById('zoom-value').textContent = zoomValue;
    
    if (currentImage) {
        currentImage.scaleToWidth((300 * zoomValue) / 100);
        fabricCanvas.centerObject(currentImage);
        fabricCanvas.renderAll();
        updatePreview();
    }
});

document.getElementById('rotation-slider').addEventListener('input', (e) => {
    const rotation = e.target.value;
    document.getElementById('rotation-value').textContent = rotation;
    
    if (currentImage) {
        currentImage.rotate(rotation);
        fabricCanvas.renderAll();
        updatePreview();
    }
});

// ========== AUTO-FIT BUTTON ==========
document.getElementById('auto-fit').addEventListener('click', () => {
    if (!currentImage) {
        app.showNotification('Please upload an image first', 'warning');
        return;
    }
    
    // Reset zoom and rotation
    document.getElementById('zoom-slider').value = 100;
    document.getElementById('rotation-slider').value = 0;
    document.getElementById('zoom-value').textContent = '100';
    document.getElementById('rotation-value').textContent = '0';
    
    currentImage.scaleToWidth(300);
    currentImage.rotate(0);
    fabricCanvas.centerObject(currentImage);
    fabricCanvas.renderAll();
    updatePreview();
    
    app.showNotification('Image auto-fitted!', 'success');
});

// ========== RESOLUTION & SIZE ==========
document.getElementById('resolution').addEventListener('change', (e) => {
    designData.resolution = parseInt(e.target.value);
    console.log('Resolution set to:', designData.resolution, 'DPI');
});

document.getElementById('poster-size').addEventListener('change', (e) => {
    designData.posterSize = e.target.value;
    updatePrice();
});

function updatePrice() {
    const price = sizePrices[designData.posterSize] || 25;
    document.getElementById('price-display').textContent = '$' + price.toFixed(2);
}

// ========== QUANTITY CONTROLS ==========
document.querySelectorAll('.qty-adjust').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById('quantity');
        const change = parseInt(btn.dataset.value);
        input.value = Math.max(1, parseInt(input.value) + change);
    });
});

// ========== RESET DESIGN ==========
document.getElementById('reset-design').addEventListener('click', () => {
    if (confirm('Are you sure you want to reset your design?')) {
        fabricCanvas.getObjects().forEach(obj => {
            if (obj !== fabricCanvas.getObjects()[0]) { // Don't delete safe area
                fabricCanvas.remove(obj);
            }
        });
        
        fabricCanvas.setBackgroundColor('#ffffff', () => fabricCanvas.renderAll());
        designData.backgroundColor = '#ffffff';
        document.getElementById('bg-color').value = '#ffffff';
        document.getElementById('zoom-slider').value = 100;
        document.getElementById('rotation-slider').value = 0;
        
        updatePreview();
        app.showNotification('Design reset!', 'info');
    }
});

// ========== PREVIEW UPDATE ==========
function updatePreview() {
    const previewCanvas = document.getElementById('preview-canvas');
    const previewCtx = previewCanvas.getContext('2d');
    
    // Take screenshot of main canvas
    previewCtx.fillStyle = designData.backgroundColor;
    previewCtx.fillRect(0, 0, previewCanvas.width, previewCanvas.height);
    
    // Redraw thumbnail
    fabricCanvas.renderOnAddRemove = false;
    const imageData = fabricCanvas.toDataURL('image/jpeg', 0.8);
    const img = new Image();
    img.onload = () => {
        previewCtx.drawImage(img, 0, 0, previewCanvas.width, previewCanvas.height);
    };
    img.src = imageData;
    fabricCanvas.renderOnAddRemove = true;
}

// ========== SAVE DESIGN ==========
document.getElementById('save-design') && document.getElementById('save-design').addEventListener('click', async () => {
    if (!currentImage) {
        app.showNotification('Please upload an image before saving', 'warning');
        return;
    }
    
    const designName = prompt('Enter design name:', 'My Design');
    if (!designName) return;
    
    // Prepare design data
    const canvasJSON = fabricCanvas.toJSON();
    const designPreview = fabricCanvas.toDataURL('image/jpeg', 0.9);
    
    // Save to database
    const result = await app.fetchAPI('/triangle-ecommerce/api/save-design.php', {
        method: 'POST',
        body: JSON.stringify({
            name: designName,
            product_id: 1,
            canvas_json: JSON.stringify(canvasJSON),
            preview_image: designPreview,
            resolution_dpi: designData.resolution
        })
    });
    
    if (result && result.success) {
        app.showNotification('Design saved successfully!', 'success');
    }
});

// ========== EXPORT DESIGN ==========
document.getElementById('export-design').addEventListener('click', () => {
    if (!currentImage) {
        app.showNotification('Please create a design before exporting', 'warning');
        return;
    }
    
    // Export high resolution PNG
    const link = document.createElement('a');
    link.href = fabricCanvas.toDataURL({
        format: 'png',
        quality: 1,
        width: fabricCanvas.width * 2,
        height: fabricCanvas.height * 2
    });
    link.download = 'frame-design-' + Date.now() + '.png';
    link.click();
    
    app.showNotification('Design exported! Check your downloads folder.', 'success');
});

// ========== ADD TO CART ==========
document.getElementById('add-to-cart').addEventListener('click', () => {
    if (!currentImage) {
        app.showNotification('Please upload and customize your image first', 'warning');
        return;
    }
    
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    const price = sizePrices[designData.posterSize] || 25;
    const thumbnail = fabricCanvas.toDataURL('image/jpeg', 0.7);
    
    // Add all items
    for (let i = 0; i < quantity; i++) {
        app.addToCart(
            'frame-' + designData.posterSize + '-' + Date.now(),
            'Frame Poster ' + designData.posterSize,
            price,
            thumbnail
        );
    }
    
    app.showNotification(quantity + ' item(s) added to cart!', 'success');
    
    // Reset quantity
    document.getElementById('quantity').value = 1;
});

// ========== KEYBOARD SHORTCUTS ==========
document.addEventListener('keydown', (e) => {
    if (e.key === 'Delete' && fabricCanvas.getActiveObject()) {
        fabricCanvas.remove(fabricCanvas.getActiveObject());
        fabricCanvas.renderAll();
        updatePreview();
    }
});

// Initialize price display
updatePrice();
