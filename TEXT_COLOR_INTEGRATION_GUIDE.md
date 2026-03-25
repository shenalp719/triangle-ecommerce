# 🎨 TEXT COLOR CHANGER - Integration Guide

Complete guide to adding dynamic text color changing functionality to your 3D mug customizer.

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Quick Start](#quick-start)
3. [Core Architecture](#core-architecture)
4. [Step-by-Step Integration](#step-by-step-integration)
5. [Code Examples](#code-examples)
6. [API Reference](#api-reference)
7. [Troubleshooting](#troubleshooting)

---

## Overview

### What This Feature Does

✅ **Select** any text layer from your design  
✅ **Change color** using a color picker in real-time  
✅ **See updates** instantly on the 3D mug  
✅ **Edit font size** while text is selected  
✅ **Delete** individual text layers  
✅ **Preview** selected text with its new color  

### Key Architecture

```
User selects text layer
         ↓
Selected text options panel appears
         ↓
User adjusts color picker
         ↓
Text layer object updated with new color
         ↓
updateTextTexture() called
         ↓
Canvas re-rendered with new colors
         ↓
Three.js texture updated
         ↓
Mug instantly shows new color!
```

---

## Quick Start

### Option 1: Use Standalone Example (5 minutes)
```bash
1. Download: text-color-changer-example.html
2. Open in browser (double-click)
3. Done! Fully working example
```

### Option 2: Integrate Into Your Code (15 minutes)
See [Step-by-Step Integration](#step-by-step-integration) below.

---

## Core Architecture

### Data Structure: Text Layer

Each text layer stores:
```javascript
{
    id: 1234567890,           // Unique timestamp-based ID
    content: "Hello World",   // Text content
    size: 40,                 // Font size in pixels
    font: "Arial",            // Font family
    color: "#000000",         // TEXT COLOR (key property!)
    bold: false,              // Bold style flag
    italic: false,            // Italic style flag
    x: 512,                   // X position on 1024x1024 canvas
    y: 512                    // Y position on 1024x1024 canvas
}
```

### Key Functions

| Function | Purpose | When Called |
|----------|---------|------------|
| `addTextLayer()` | Create new text | User clicks "Add Text" |
| `selectTextLayer(id)` | Activate for editing | User clicks layer |
| `updateTextTexture()` | Render all texts to canvas | Any change to text |
| `updateLayersList()` | Refresh layer panel UI | Layer added/removed |
| `deleteSelectedText()` | Remove text from design | User clicks "Delete" |

---

## Step-by-Step Integration

### STEP 1: Add HTML Structure for Selected Text Options

Add this section to your left control panel (after the "Add Text" button):

```html
<!-- File: customizer-product.php -->
<!-- Add this AFTER the add-text button, around line 120 -->

<!-- ========== SELECTED TEXT OPTIONS (Dynamic) ========== -->
<div style="margin-top: 2rem; padding: 1rem; background-color: #f0f4ff; border: 2px solid #667eea; border-radius: 0.5rem; display: none;" id="selected-text-options">
    <div style="font-weight: 600; margin-bottom: 1rem; color: #333;">
        ✎ Edit Selected Text
    </div>

    <!-- Preview of selected text -->
    <div id="selected-text-preview" style="padding: 0.75rem; background: white; border-radius: 0.5rem; margin-bottom: 1rem; font-weight: 600; word-break: break-word; border-left: 4px solid;"></div>

    <!-- Change Color of Selected Text -->
    <div style="margin-bottom: 1rem;">
        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem;">Change Text Color</label>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="color" id="selected-text-color" value="#000000" style="width: 50px; height: 50px; cursor: pointer; border: none; border-radius: 0.5rem;">
            <div id="selected-color-display" style="flex: 1; padding: 0.75rem; background-color: var(--light-gray); border-radius: 0.5rem; font-size: 0.85rem; font-family: 'Courier New', monospace; font-weight: 600;">#000000</div>
        </div>
    </div>

    <!-- Change Font Size -->
    <div style="margin-bottom: 1rem;">
        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem;">Change Font Size</label>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="range" id="selected-text-size" min="8" max="120" value="40" style="flex: 1; cursor: pointer;">
            <span style="font-size: 0.85rem; font-weight: 600; min-width: 40px;"><span id="selected-size-value">40</span>px</span>
        </div>
    </div>

    <!-- Delete Button -->
    <button style="width: 100%; padding: 0.75rem; background-color: #ff6b6b; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;" id="delete-text-btn">
        🗑 Delete Selected Text
    </button>
</div>
```

### STEP 2: Add JavaScript - Color Change Handler

In your `customizer-product.php` JavaScript section, add this after the text editor setup:

```javascript
// File: customizer-product.php - Add around line 710 (after setupTextEditor function)

// ==================== SELECTED TEXT COLOR CHANGE ====================
function setupSelectedTextControls() {
    // Color picker for selected text
    document.getElementById('selected-text-color').addEventListener('input', function(e) {
        const newColor = e.target.value;
        document.getElementById('selected-color-display').textContent = newColor.toUpperCase();

        // Update selected layer's color
        if (customizer.selectedLayerId !== null) {
            const layer = customizer.textLayers.find(l => l.id === customizer.selectedLayerId);
            if (layer) {
                layer.color = newColor;  // ← KEY: Update color property
                createTextTexture();      // ← Re-render texture with new color
                updateLayersList();       // ← Refresh layer list UI
                app.showNotification(`Text color changed to ${newColor}`, 'success');
            }
        }
    });

    // Font size change for selected text
    document.getElementById('selected-text-size').addEventListener('input', function(e) {
        const newSize = parseInt(e.target.value);
        document.getElementById('selected-size-value').textContent = newSize;

        if (customizer.selectedLayerId !== null) {
            const layer = customizer.textLayers.find(l => l.id === customizer.selectedLayerId);
            if (layer) {
                layer.size = newSize;     // ← Update size property
                createTextTexture();      // ← Re-render texture
                updateLayersList();
            }
        }
    });

    // Delete button
    document.getElementById('delete-text-btn').addEventListener('click', function() {
        if (customizer.selectedLayerId !== null) {
            customizer.textLayers = customizer.textLayers.filter(l => l.id !== customizer.selectedLayerId);
            customizer.selectedLayerId = null;
            document.getElementById('selected-text-options').style.display = 'none';
            updateLayersList();
            createTextTexture();
            app.showNotification('Text deleted ✓', 'success');
        }
    });
}
```

### STEP 3: Modify selectLayer() Function

Replace your existing `selectLayer()` function (around line 760) with this enhanced version:

```javascript
// BEFORE: (Old function - replace this)
function selectLayer(layerId) {
    customizer.selectedLayerId = layerId;
    customizer.layerDragMode = true;
    if (layerId === 'image') {
        app.showNotification('💡 Drag to move image • Drag from corners to resize', 'info');
    } else {
        app.showNotification('💡 Drag on the 3D product to reposition this text', 'info');
    }
}

// AFTER: (New function - with color changer support)
function selectLayer(layerId) {
    customizer.selectedLayerId = layerId;
    customizer.layerDragMode = true;

    if (layerId === 'image') {
        // Image layer selected
        document.getElementById('selected-text-options').style.display = 'none';
        app.showNotification('💡 Drag to move image • Drag from corners to resize', 'info');
    } else {
        // Text layer selected
        const layer = customizer.textLayers.find(l => l.id === layerId);
        if (layer) {
            // Show selected text options panel
            const panel = document.getElementById('selected-text-options');
            panel.style.display = 'block';

            // Update preview
            document.getElementById('selected-text-preview').textContent = layer.content;
            document.getElementById('selected-text-preview').style.color = layer.color;
            document.getElementById('selected-text-preview').style.borderLeftColor = layer.color;

            // Update color picker
            document.getElementById('selected-text-color').value = layer.color;
            document.getElementById('selected-color-display').textContent = layer.color.toUpperCase();

            // Update font size
            document.getElementById('selected-text-size').value = layer.size;
            document.getElementById('selected-size-value').textContent = layer.size;

            app.showNotification(`✎ Editing: "${layer.content.substring(0, 30)}"`, 'info');
        }
    }
}
```

### STEP 4: Initialize on Page Load

Add this to the `initializeScene()` function (around line 230):

```javascript
// Add this line near the end of initializeScene() function
setupSelectedTextControls();  // ← Add this line
```

### STEP 5: Update Color Buttons Event Handler

Modify the color button click handler to hide selected text options when changing product color:

```javascript
// In setupColorButtons() function (around line 710)
// Add this at the START of the click handler:

btn.addEventListener('click', (e) => {
    // NEW: Hide selected text options when changing product color
    document.getElementById('selected-text-options').style.display = 'none';
    customizer.selectedLayerId = null;

    const color = btn.dataset.color;
    // ... rest of existing code ...
});
```

---

## Code Examples

### Example 1: Basic Text Color Change

```javascript
// Get the selected text layer
const layer = customizer.textLayers.find(l => l.id === customizer.selectedLayerId);

// Change its color
if (layer) {
    layer.color = '#FF0000';  // Change to red
    createTextTexture();       // Update 3D preview
}
```

### Example 2: Batch Color Change (All Text)

```javascript
// Change all text to the same color
customizer.textLayers.forEach(layer => {
    layer.color = '#0066CC';  // All blue
});
createTextTexture();  // Update once
```

### Example 3: Undo Last Color Change

```javascript
// Store original color before change
const lastColor = '#000000';

// Change color
layer.color = '#FF6B6B';
createTextTexture();

// Later, revert:
layer.color = lastColor;
createTextTexture();
```

### Example 4: Programmatically Select and Modify

```javascript
// Select first text layer and change its color
if (customizer.textLayers.length > 0) {
    const firstLayer = customizer.textLayers[0];
    selectLayer(firstLayer.id);  // Select it
    
    // Change color after short delay
    setTimeout(() => {
        firstLayer.color = '#667eea';
        createTextTexture();
    }, 100);
}
```

### Example 5: Export Selected Layer's Properties

```javascript
// Get current selected layer info
const layer = customizer.textLayers.find(l => l.id === customizer.selectedLayerId);

if (layer) {
    const layerData = {
        text: layer.content,
        color: layer.color,          // ← Text color
        fontSize: layer.size,
        fontFamily: layer.font,
        isBold: layer.bold,
        isItalic: layer.italic,
        position: { x: layer.x, y: layer.y }
    };

    console.log(layerData);
    // Use this to save to database, export, etc.
}
```

---

## API Reference

### Functions

#### `selectTextLayer(layerId)`
**Purpose**: Activate a text layer for editing  
**Parameters**: 
- `layerId` (number): The unique ID of text layer

**Example**:
```javascript
selectTextLayer(1234567890);
```

**What it does**:
1. Sets `customizer.selectedLayerId`
2. Shows the selected text options panel
3. Populates color picker with layer's current color
4. Updates preview with layer text and color

---

#### `updateTextColor(newColor)`
**Purpose**: Change the selected text's color  
**Parameters**:
- `newColor` (string): Hex color code (e.g., '#FF0000')

**Example**:
```javascript
updateTextColor('#FF0000');  // Red
```

**What it does**:
1. Updates the layer's `color` property
2. Re-renders the canvas texture with new color
3. Updates Three.js material
4. Refreshes the UI

---

#### `createTextTexture()`
**Purpose**: Re-render all text layers onto canvas (call after ANY text change)  
**Parameters**: None

**Example**:
```javascript
// After changing any text property:
layer.color = '#0000FF';
layer.size = 50;
createTextTexture();  // Re-render
```

---

### State Properties

#### `customizer.selectedLayerId`
**Type**: `number | null`  
**Value**: ID of currently selected text layer, or null  
**Used by**: All selected text editing functions

```javascript
if (customizer.selectedLayerId !== null) {
    const layer = customizer.textLayers.find(l => l.id === customizer.selectedLayerId);
    // Do something with layer
}
```

---

#### `customizer.textLayers`
**Type**: `Array of text layer objects`  
**Structure**:
```javascript
[
    {
        id: 1234567890,
        content: "Text",
        size: 40,
        font: "Arial",
        color: "#000000",  // ← Text color
        bold: false,
        italic: false,
        x: 512,
        y: 512
    },
    // ... more layers
]
```

---

### CSS Classes (For Styling)

#### `.layer-item.selected`
Applied to selected layer in the layers list

```css
.layer-item.selected {
    background: #e8f5ff;
    border-left-color: #667eea !important;
}
```

---

## Troubleshooting

### Problem: Color picker shows but doesn't update mug

**Solution**: Make sure `createTextTexture()` is called after color change
```javascript
layer.color = newColor;
createTextTexture();  // ← Don't forget this!
```

---

### Problem: Selected text options panel disappears when I click canvas

**Solution**: Add this to mouse down handler:
```javascript
canvas.addEventListener('mousedown', (e) => {
    // Don't hide panel if clicking on layer
    if (!e.target.classList.contains('layer-item')) {
        // ... existing code ...
    }
});
```

---

### Problem: Text color is correct but doesn't show on mug

**Solution**: Check if the mug body is using the texture:
```javascript
// Verify product mesh has texture
console.log(customizer.productMesh.material.map);  // Should not be null

// Force update
customizer.productMesh.material.needsUpdate = true;
```

---

### Problem: Multiple clicks required to change color

**Solution**: Debounce the color picker event
```javascript
let colorChangeTimeout;

document.getElementById('selected-text-color').addEventListener('input', function(e) {
    clearTimeout(colorChangeTimeout);
    colorChangeTimeout = setTimeout(() => {
        // Color change logic here
        const newColor = e.target.value;
        // ... update layer ...
        createTextTexture();
    }, 50);  // Wait 50ms before updating
});
```

---

### Problem: Canvas texture is blurry

**Solution**: Increase canvas resolution in `createTextTexture()`:
```javascript
// Change from 1024 to 2048
const canvas = document.createElement('canvas');
canvas.width = 2048;   // ← Increase
canvas.height = 2048;  // ← Increase
```

---

### Problem: Text colors don't save

**Solution**: When saving design, include color property:
```javascript
const designData = customizer.textLayers.map(layer => ({
    id: layer.id,
    content: layer.content,
    color: layer.color,      // ← Include this!
    size: layer.size,
    font: layer.font,
    bold: layer.bold,
    italic: layer.italic,
    x: layer.x,
    y: layer.y
}));

// Send to server
fetch('/api/save-design.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(designData)
});
```

---

## Performance Tips

### Tip 1: Debounce Large Redraws
```javascript
// Bad - redraws on every pixel change
input.addEventListener('input', updateTextTexture);

// Good - redraws only after user stops
let timeout;
input.addEventListener('input', () => {
    clearTimeout(timeout);
    timeout = setTimeout(updateTextTexture, 100);
});
```

### Tip 2: Cache Layer Lookups
```javascript
// Instead of searching every time:
const layer = customizer.textLayers.find(l => l.id === customizer.selectedLayerId);

// Use a reference:
let selectedLayer = layer;
selectedLayer.color = newColor;
```

### Tip 3: Batch Updates
```javascript
// Bad - multiple texture updates
layer1.color = '#FF0000';
createTextTexture();
layer2.size = 50;
createTextTexture();

// Good - single texture update
layer1.color = '#FF0000';
layer2.size = 50;
createTextTexture();  // Update once
```

---

## Browser Compatibility

| Feature | Chrome | Firefox | Safari | Edge |
|---------|--------|---------|--------|------|
| Color Input | ✅ 95+ | ✅ 96+ | ✅ 14+ | ✅ 95+ |
| Canvas Texture | ✅ All | ✅ All | ✅ All | ✅ All |
| Real-time Update | ✅ Yes | ✅ Yes | ✅ Yes | ✅ Yes |

---

## Next Steps

### To Add More Features:

1. **Undo/Redo**: Store `previousColors[]` array
2. **Color Presets**: Add quick-access color palette
3. **Text Effects**: Add shadow, outline, glow
4. **Font Upload**: Support custom fonts from Google Fonts
5. **Layer Grouping**: Group related text layers
6. **Batch Operations**: Change all text to one color

---

## Support

For questions or issues:
1. Check [Troubleshooting](#troubleshooting) section
2. Review code comments in your implementation
3. Test with `text-color-changer-example.html`
4. Compare your code with the working example

---

**Happy customizing! 🎨✨**
