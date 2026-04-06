# Fabric.js + Three.js Integration - Code Reference Guide

## Quick Start Examples

### 1. Initialize Fabric Canvas

```javascript
const fabricCanvas = new fabric.Canvas('fabric-canvas', {
    width: 600,
    height: 600,
    backgroundColor: '#ffffff',
    selection: true,
    preserveObjectStacking: true
});

// Listen to changes
fabricCanvas.on('object:added', updateTexture);
fabricCanvas.on('object:modified', updateTexture);
fabricCanvas.on('object:removed', updateTexture);
```

---

## Text Operations

### Add Text
```javascript
const text = new fabric.Text('Hello World', {
    left: 100,
    top: 100,
    fontSize: 40,
    fontFamily: 'Arial',
    fill: '#000000',
    originX: 'center',
    originY: 'center'
});
fabricCanvas.add(text);
fabricCanvas.renderAll();
```

### Update Text Properties
```javascript
const activeObject = fabricCanvas.getActiveObject();

if (activeObject instanceof fabric.Text) {
    activeObject.set({
        fontSize: 50,
        fontFamily: 'Georgia',
        fill: '#FF0000',
        fontWeight: 'bold'
    });
    fabricCanvas.renderAll();
}
```

### Get Text Content
```javascript
const activeObject = fabricCanvas.getActiveObject();
if (activeObject instanceof fabric.Text) {
    console.log(activeObject.text);
}
```

### Delete Text
```javascript
const activeObject = fabricCanvas.getActiveObject();
if (activeObject) {
    fabricCanvas.remove(activeObject);
    fabricCanvas.renderAll();
}
```

---

## Image Operations

### Add Image
```javascript
fabric.Image.fromURL('path/to/image.jpg', (img) => {
    img.scale(0.5);
    img.set({
        left: 300,
        top: 300,
        originX: 'center',
        originY: 'center'
    });
    fabricCanvas.add(img);
    fabricCanvas.renderAll();
});
```

### Upload Image (Browser)
```javascript
const fileInput = document.getElementById('image-upload');
fileInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    const reader = new FileReader();
    
    reader.onload = (event) => {
        fabric.Image.fromURL(event.target.result, (img) => {
            // Scale to fit
            const maxWidth = 400;
            const maxHeight = 400;
            const scale = Math.min(maxWidth / img.width, maxHeight / img.height);
            
            img.scale(scale);
            img.set({
                left: canvas.width / 2,
                top: canvas.height / 2,
                originX: 'center',
                originY: 'center'
            });
            
            fabricCanvas.add(img);
            fabricCanvas.renderAll();
        });
    };
    reader.readAsDataURL(file);
});
```

### Resize Image (Programmatically)
```javascript
const activeObject = fabricCanvas.getActiveObject();
if (activeObject instanceof fabric.Image) {
    activeObject.scaleToWidth(200);
    fabricCanvas.renderAll();
}
```

---

## Layer Management

### Select Object
```javascript
const object = fabricCanvas.getObjects()[0]; // Get first object
fabricCanvas.setActiveObject(object);
fabricCanvas.renderAll();
```

### Get Selected Object
```javascript
const selected = fabricCanvas.getActiveObject();
if (selected) {
    console.log('Selected:', selected.type, selected.text || selected.src);
}
```

### Deselect All
```javascript
fabricCanvas.discardActiveObject();
fabricCanvas.renderAll();
```

### Bring to Front
```javascript
const activeObject = fabricCanvas.getActiveObject();
if (activeObject) {
    fabricCanvas.bringToFront(activeObject);
    fabricCanvas.renderAll();
}
```

### Send to Back
```javascript
const activeObject = fabricCanvas.getActiveObject();
if (activeObject) {
    fabricCanvas.sendToBack(activeObject);
    fabricCanvas.renderAll();
}
```

### Bring Forward
```javascript
const activeObject = fabricCanvas.getActiveObject();
if (activeObject) {
    fabricCanvas.bringForward(activeObject);
    fabricCanvas.renderAll();
}
```

### Send Backward
```javascript
const activeObject = fabricCanvas.getActiveObject();
if (activeObject) {
    fabricCanvas.sendBackwards(activeObject);
    fabricCanvas.renderAll();
}
```

### Delete Object
```javascript
const activeObject = fabricCanvas.getActiveObject();
if (activeObject) {
    fabricCanvas.remove(activeObject);
    fabricCanvas.renderAll();
}
```

### Get All Objects
```javascript
const allObjects = fabricCanvas.getObjects();
allObjects.forEach((obj, index) => {
    console.log(`${index}: ${obj.type}`);
});
```

### Clear Canvas
```javascript
fabricCanvas.clear();
```

---

## Transformations

### Rotate Object
```javascript
const activeObject = fabricCanvas.getActiveObject();
if (activeObject) {
    activeObject.rotate(45); // 45 degrees
    fabricCanvas.renderAll();
}
```

### Scale Object
```javascript
const activeObject = fabricCanvas.getActiveObject();
if (activeObject) {
    activeObject.scale(1.5); // 150% size
    fabricCanvas.renderAll();
}
```

### Move Object
```javascript
const activeObject = fabricCanvas.getActiveObject();
if (activeObject) {
    activeObject.set({
        left: 200,
        top: 200
    });
    fabricCanvas.renderAll();
}
```

---

## Export & Import

### Export as PNG
```javascript
const dataURL = fabricCanvas.toDataURL({
    format: 'png',
    quality: 0.9,
    multiplier: 2 // 2x resolution
});

// Download
const link = document.createElement('a');
link.href = dataURL;
link.download = 'design.png';
link.click();
```

### Export as JSON
```javascript
const json = fabricCanvas.toJSON();
console.log(JSON.stringify(json));

// Save to server
fetch('/api/save-design', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(json)
});
```

### Import from JSON
```javascript
const json = {
    // Previously exported JSON data
};

fabricCanvas.loadFromJSON(json, () => {
    fabricCanvas.renderAll();
});
```

---

## Three.js Canvas Texture Sync

### Convert Fabric Canvas to Three.js Texture
```javascript
function syncDesignToMug() {
    // Export Fabric canvas as PNG
    const fabricPNG = fabricCanvas.toDataURL({
        format: 'png',
        quality: 1,
        multiplier: 2
    });

    // Create image from PNG
    const img = new Image();
    img.onload = function() {
        // Create Three.js canvas
        const canvas = document.createElement('canvas');
        canvas.width = 1024;
        canvas.height = 1024;
        const ctx = canvas.getContext('2d');

        // Draw and scale image
        ctx.drawImage(img, 0, 0, 1024, 1024);

        // Create texture
        const texture = new THREE.CanvasTexture(canvas);
        texture.needsUpdate = true;

        // Apply to material
        productMesh.material.map = texture;
        productMesh.material.needsUpdate = true;

        // Render
        renderer.render(scene, camera);
    };
    img.src = fabricPNG;
}

// Auto-sync on any change
fabricCanvas.on('object:modified', syncDesignToMug);
fabricCanvas.on('object:added', syncDesignToMug);
fabricCanvas.on('object:removed', syncDesignToMug);
```

---

## Event Handling

### Selection Events
```javascript
// Object selected
fabricCanvas.on('selection:created', (e) => {
    console.log('Selected:', e.selected);
});

// Object deselected
fabricCanvas.on('selection:cleared', () => {
    console.log('Nothing selected');
});

// Selection updated
fabricCanvas.on('selection:updated', (e) => {
    console.log('Updated selection:', e.selected);
});
```

### Modification Events
```javascript
// Object added
fabricCanvas.on('object:added', (e) => {
    console.log('Added:', e.target.type);
});

// Object modified
fabricCanvas.on('object:modified', (e) => {
    console.log('Modified:', e.target);
});

// Object removed
fabricCanvas.on('object:removed', (e) => {
    console.log('Removed:', e.target.type);
});

// Object moved
fabricCanvas.on('object:moving', (e) => {
    console.log('Moving:', e.target.left, e.target.top);
});

// Object rotated
fabricCanvas.on('object:rotating', (e) => {
    console.log('Angle:', e.target.angle);
});

// Object scaled
fabricCanvas.on('object:scaling', (e) => {
    console.log('Scale:', e.target.scaleX, e.target.scaleY);
});
```

---

## Advanced: Custom Config

### Enhanced Fabric Canvas
```javascript
const fabricCanvas = new fabric.Canvas('fabric-canvas', {
    width: 800,
    height: 600,
    backgroundColor: '#ffffff',
    
    // Selection settings
    selection: true,
    selectionColor: 'rgba(0, 0, 255, 0.1)',
    selectionBorderColor: '#0066cc',
    selectionLineWidth: 2,
    
    // Object defaults
    centeredScaling: true,
    centeredRotation: true,
    preserveObjectStacking: true,
    
    // Interaction
    renderOnAddRemove: false, // Improves performance
    statefullCache: true
});

fabricCanvas.renderAll();
```

### Enhanced Three.js Material
```javascript
const material = new THREE.MeshStandardMaterial({
    color: 0xffffff,
    roughness: 0.4,
    metalness: 0.05,
    envMapIntensity: 1,
    side: THREE.FrontSide,
    
    // Texture
    map: texture,
    mapIntensity: 1
});
```

---

## Performance Tips

### Disable Render on Add
```javascript
fabricCanvas.renderOnAddRemove = false;

// Add multiple objects
fabricCanvas.add(text1);
fabricCanvas.add(text2);
fabricCanvas.add(image1);

// Render once
fabricCanvas.renderAll();
```

### Optimize Events
```javascript
let updateTimeout;
fabricCanvas.on('object:moving', () => {
    clearTimeout(updateTimeout);
    updateTimeout = setTimeout(() => {
        syncDesignToMug();
    }, 100); // Debounce
});
```

### Scale Canvas for Web
```javascript
// Use smaller design canvas, scale up for export
const designCanvas = 600;    // Display size
const exportSize = 1024;     // Export quality

const scale = exportSize / designCanvas;
const dataURL = fabricCanvas.toDataURL({
    multiplier: scale,
    quality: 1
});
```

---

## Error Handling

### Safe Object Access
```javascript
const activeObject = fabricCanvas.getActiveObject();

if (!activeObject) {
    console.warn('No object selected');
    return;
}

if (!(activeObject instanceof fabric.Text)) {
    console.warn('Selected object is not text');
    return;
}

// Safe to use
activeObject.set({ fontSize: 50 });
```

### Image Load Error
```javascript
fabric.Image.fromURL('image.jpg', (img) => {
    fabricCanvas.add(img);
    fabricCanvas.renderAll();
}, {}, {
    crossOrigin: 'anonymous',
    // Handle error
    error: (error) => {
        console.error('Image load failed:', error);
        alert('Failed to load image');
    }
});
```

---

## Common Patterns

### Undo/Redo System
```javascript
const history = [];
let historyStep = -1;

function saveHistory() {
    historyStep++;
    history.length = historyStep;
    history.push(fabricCanvas.toJSON());
}

function undo() {
    if (historyStep > 0) {
        historyStep--;
        fabricCanvas.loadFromJSON(history[historyStep], () => {
            fabricCanvas.renderAll();
        });
    }
}

function redo() {
    if (historyStep < history.length - 1) {
        historyStep++;
        fabricCanvas.loadFromJSON(history[historyStep], () => {
            fabricCanvas.renderAll();
        });
    }
}

fabricCanvas.on('object:added', saveHistory);
fabricCanvas.on('object:modified', saveHistory);
fabricCanvas.on('object:removed', saveHistory);
```

### Lock/Unlock Objects
```javascript
function lockObject(obj) {
    obj.set({
        selectable: false,
        evented: false
    });
}

function unlockObject(obj) {
    obj.set({
        selectable: true,
        evented: true
    });
}

// Lock all except selected
fabricCanvas.getObjects().forEach(obj => {
    if (obj !== fabricCanvas.getActiveObject()) {
        lockObject(obj);
    }
});
```

### Group Objects
```javascript
const selection = fabricCanvas.getActiveObjects();
if (selection.length > 1) {
    const group = new fabric.Group(selection);
    fabricCanvas.add(group);
    fabricCanvas.renderAll();
}
```

---

## Resources

- **Fabric.js Docs**: http://fabricjs.com/docs/
- **Three.js Docs**: https://threejs.org/docs/
- **CDN Links**:
  - Fabric.js: `https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js`
  - Three.js: `https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js`

---

## License & Credits

This integration combines:
- Fabric.js - HTML5 Canvas manipulation library
- Three.js - JavaScript 3D library
- Custom implementation for product customization

