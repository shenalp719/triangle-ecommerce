# Advanced 3D Mug Customizer - Fabric.js + Three.js Integration Guide

## Overview

This advanced customizer combines:
- **Fabric.js**: For full 2D design manipulation (drag, resize, rotate, layer management)
- **Three.js**: For realistic 3D mug preview
- **Real-time syncing**: Canvas texture updates live on the 3D model

---

## Architecture

### Two-Canvas System

```
┌─────────────────────────────────────┐
│      Fabric.js Canvas (2D)          │
│  - User designs elements here       │
│  - Full drag/resize/rotate          │
│  - Layer management                 │
│  - Selection & editing              │
└────────────────┬────────────────────┘
                 │
         (Export to PNG)
                 │
                 ▼
┌─────────────────────────────────────┐
│     Three.js Canvas (3D)            │
│  - Fabric canvas mapped as texture  │
│  - Realistic mug with lighting      │
│  - Rotation & zoom controls         │
│  - Final preview                    │
└─────────────────────────────────────┘
```

---

## Features

### 1. **Text Management**
- Add unlimited text layers
- Customize font family (Arial, Helvetica, Georgia, Courier, Verdana)
- Adjust font size (10-100px)
- Change text color with color picker
- Drag and reposition text freely
- Rotate text at any angle

### 2. **Image Management**
- Upload images from computer (max 5MB)
- Drag to reposition
- Resize by dragging corners
- Rotate 360 degrees
- Layer above/below other elements

### 3. **Layer System**
```javascript
// Each object in Fabric canvas is a layer
// Objects can be:
// - fabric.Text (text layers)
// - fabric.Image (image layers)

// Layer operations:
canvas.bringForward(object)    // Move layer up
canvas.sendBackwards(object)   // Move layer down
canvas.remove(object)          // Delete layer
canvas.setActiveObject(object) // Select layer
```

### 4. **Sync Mechanism**
```javascript
// When Fabric canvas changes:
designState.fabricCanvas.on('object:modified', updateThreeTexture)
designState.fabricCanvas.on('object:added', updateThreeTexture)
designState.fabricCanvas.on('object:removed', updateThreeTexture)

// updateThreeTexture() converts Fabric canvas → PNG → Three.js texture
function updateThreeTexture() {
    const fabricCanvasData = designState.fabricCanvas.toDataURL();
    // Create image from PNG data
    // Scale to 1024x1024
    // Apply as Three.js CanvasTexture
}
```

---

## Code Structure

### State Management
```javascript
const designState = {
    // Fabric.js
    fabricCanvas: null,
    
    // Three.js
    threeScene: null,
    threeCamera: null,
    threeRenderer: null,
    
    // Product
    currentColor: '#FFFFFF',
    
    // Interaction
    isDraggingMug: false,
    mugRotation: { x: 0, y: 0 },
    zoom: 1
};
```

### Key Functions

#### Adding Text
```javascript
function addTextToCanvas() {
    const text = document.getElementById('text-input').value;
    const fabricText = new fabric.Text(text, {
        left: canvas.width / 2,
        top: canvas.height / 2,
        fontSize: 40,
        fontFamily: 'Arial',
        fill: '#000000',
        originX: 'center',
        originY: 'center'
    });
    
    designState.fabricCanvas.add(fabricText);
    designState.fabricCanvas.renderAll();
    updateThreeTexture();
}
```

#### Adding Images
```javascript
function handleImageUpload(e) {
    const file = e.target.files[0];
    const reader = new FileReader();
    
    reader.onload = function(event) {
        fabric.Image.fromURL(event.target.result, (img) => {
            // Scale image to fit canvas
            img.scale(Math.min(width / img.width, height / img.height));
            img.set({
                left: canvas.width / 2,
                top: canvas.height / 2,
                originX: 'center',
                originY: 'center'
            });
            
            designState.fabricCanvas.add(img);
            designState.fabricCanvas.renderAll();
            updateThreeTexture();
        });
    };
    
    reader.readAsDataURL(file);
}
```

#### Texture Sync
```javascript
function updateThreeTexture() {
    // Export Fabric canvas as PNG
    const fabricCanvasData = designState.fabricCanvas.toDataURL({
        format: 'png',
        quality: 1,
        multiplier: 2
    });
    
    // Create Three.js texture from PNG
    const img = new Image();
    img.onload = function() {
        const canvas = document.createElement('canvas');
        canvas.width = 1024;
        canvas.height = 1024;
        const ctx = canvas.getContext('2d');
        
        // Scale Fabric canvas to 1024x1024
        ctx.drawImage(img, 0, 0, 1024, 1024);
        
        // Apply to Three.js material
        const texture = new THREE.CanvasTexture(canvas);
        designState.productMesh.material.map = texture;
        designState.productMesh.material.needsUpdate = true;
    };
    img.src = fabricCanvasData;
}
```

---

## User Interactions

### Text Editing
1. Enter text in input field
2. Click "Add Text"
3. Text appears on canvas
4. Click on text to select
5. Drag to move
6. Use corner handles to resize
7. Rotate using object controls
8. Adjust font size slider
9. Pick color from color picker
10. Select font family

### Image Management
1. Click "Add Image" file input
2. Select image (max 5MB)
3. Image appears on canvas
4. Click to select
5. Drag to move
6. Corner handles to resize
7. Rotate freely
8. Use Forward/Backward buttons for layering

### Mug Customization
1. Select mug color from color options
2. All text/images update in real-time
3. 3D preview updates immediately

### 3D View Control
- **Drag**: Rotate mug in 3D space
- **Scroll**: Zoom in/out (0.5x - 3x)
- **Auto-rotate**: Gentle rotation when not dragging

---

## Fabric.js Event Listeners

```javascript
// Selection events
fabricCanvas.on('selection:created', updateThreeTexture);
fabricCanvas.on('selection:updated', updateThreeTexture);
fabricCanvas.on('selection:cleared', updateThreeTexture);

// Modification events
fabricCanvas.on('object:added', updateThreeTexture);
fabricCanvas.on('object:modified', updateThreeTexture);
fabricCanvas.on('object:removed', updateThreeTexture);

// All trigger texture updates in real-time
```

---

## Three.js Components

### Mug Geometry
```javascript
// Body (open-ended cylinder)
const bodyGeometry = new THREE.CylinderGeometry(0.7, 0.65, 1.2, 128, 32, true);

// Rim (torus)
const rimGeometry = new THREE.TorusGeometry(0.72, 0.035, 20, 128);

// Handle (tube along curve)
const handleCurve = new THREE.CatmullRomCurve3([...]);
const tubeGeometry = new THREE.TubeGeometry(handleCurve, 20, 0.08, 12, false);
```

### Lighting Setup
```javascript
// Ambient: Soft overall light
const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);

// Key Light: Main directional light with shadows
const keyLight = new THREE.DirectionalLight(0xffffff, 1);
keyLight.position.set(4, 6, 5);
keyLight.castShadow = true;

// Fill Light: Subtle light from opposite side
const fillLight = new THREE.DirectionalLight(0xffffff, 0.4);

// Rim Light: Edge highlights
const rimLight = new THREE.DirectionalLight(0xffffff, 0.3);
```

---

## Performance Optimization

1. **Canvas Size**: 600x600 Fabric, scaled to 1024x1024 for texture
2. **Update Throttling**: Updates only on actual modifications (not continuous)
3. **WebGL Optimization**:
   - High-quality shadow maps (2048x2048)
   - PCF shadow filtering
   - Proper geometry segmentation
   - Efficient material usage

4. **Memory Management**:
   - Single mug model
   - Reusable canvas texture
   - Auto-disposal of unused resources

---

## Customization Examples

### Add Custom Font
```javascript
// In font-family select
<option value="'Courier Prime', monospace">Courier Prime</option>

// Make sure font is loaded via Google Fonts
<link href="https://fonts.googleapis.com/css2?family=Courier+Prime&display=swap" rel="stylesheet">
```

### Change Mug Dimensions
```javascript
// In createMug() function
const bodyGeometry = new THREE.CylinderGeometry(
    0.7,    // radiusTop
    0.65,   // radiusBottom
    1.2,    // height
    128,    // radialSegments
    32,     // heightSegments
    true    // openEnded
);
```

### Add New Color Option
```php
// In customizer-advanced.php
'colors' => [
    '#FFFFFF', '#000000', '#E31E24', 
    '#0066CC', '#00AA00', '#FF00FF'  // Add new color
],
'colorNames' => [
    'White', 'Black', 'Red', 
    'Blue', 'Green', 'Magenta'       // Add name
]
```

---

## Troubleshooting

### Texture not updating
- Check browser console for errors
- Ensure Fabric canvas has valid objects
- Verify Three.js material has `needsUpdate = true`

### Blurry texture
- Increase `multiplier: 2` in toDataURL()
- Verify canvas size (1024x1024 recommended)

### Poor performance
- Reduce object count on Fabric canvas
- Lower shadow map resolution if needed
- Use smaller images

### Mug not rotating
- Check Three.js renderer initialization
- Verify mouse events are firing
- Ensure `isDraggingMug` flag is toggled

---

## Browser Compatibility

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

---

## File Locations

- **Main customizer**: `/customizer-advanced.php`
- **Original customizer**: `/customizer-product.php` (still available)

---

## Next Steps

1. Test with various text and images
2. Export design as image
3. Send design data to backend for order
4. Add undo/redo functionality
5. Implement design templates
6. Add more product types (shirt, cap)

---

## API Reference

### Fabric Canvas Methods

```javascript
// Add objects
canvas.add(object)

// Select/deselect
canvas.setActiveObject(object)
canvas.discardActiveObject()
canvas.getActiveObject()

// Layer management
canvas.bringToFront(object)
canvas.bringForward(object)
canvas.sendToBack(object)
canvas.sendBackwards(object)

// Remove/clear
canvas.remove(object)
canvas.clear()

// Rendering
canvas.renderAll()

// Export
canvas.toDataURL()
canvas.toJSON()
```

### Three.js Texture Update

```javascript
// Create texture from canvas
const texture = new THREE.CanvasTexture(canvas);

// Apply to material
material.map = texture;
material.needsUpdate = true;

// Update renderer
renderer.render(scene, camera);
```

---

## Version History

- **v1.0** (Current): Initial release with Fabric.js + Three.js integration
  - Fabric canvas for 2D design
  - Three.js 3D preview
  - Real-time texture sync
  - Layer management
  - Text & image support
  - Responsive design

