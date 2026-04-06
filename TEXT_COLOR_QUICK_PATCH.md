# 🔧 TEXT COLOR CHANGER - Quick Integration Patch

**Time to implement: ~10 minutes**

Quick copy-paste patches for `customizer-product.php`

---

## ✅ Implementation Checklist

- [ ] **PATCH 1**: Add HTML UI for selected text options
- [ ] **PATCH 2**: Add JavaScript functions for color change
- [ ] **PATCH 3**: Update selectLayer() function
- [ ] **PATCH 4**: Initialize on page load
- [ ] **PATCH 5**: Update color buttons
- [ ] **Test**: Try adding text and changing color

---

## 📝 PATCH 1: HTML UI (Around line 130)

**Location**: After the "Add Text to Product" button in `customizer-product.php`

**Find this**:
```html
                <button style="width: 100%; padding: 0.75rem; background-color: var(--primary-red); color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;" id="add-text">
                    Add Text to Product
                </button>
            </div>
```

**Replace with**:
```html
                <button style="width: 100%; padding: 0.75rem; background-color: var(--primary-red); color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;" id="add-text">
                    Add Text to Product
                </button>
            </div>

            <!-- ========== SELECTED TEXT OPTIONS (NEW) ========== -->
            <div style="margin-top: 2rem; padding: 1rem; background-color: #f0f4ff; border: 2px solid #667eea; border-radius: 0.5rem; display: none;" id="selected-text-options">
                <div style="font-weight: 600; margin-bottom: 1rem; color: #333;">✎ Edit Selected Text</div>
                
                <div id="selected-text-preview" style="padding: 0.75rem; background: white; border-radius: 0.5rem; margin-bottom: 1rem; font-weight: 600; word-break: break-word; border-left: 4px solid;"></div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem;">Change Text Color</label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="color" id="selected-text-color" value="#000000" style="width: 50px; height: 50px; cursor: pointer; border: none; border-radius: 0.5rem;">
                        <div id="selected-color-display" style="flex: 1; padding: 0.75rem; background-color: var(--light-gray); border-radius: 0.5rem; font-size: 0.85rem; font-family: 'Courier New', monospace; font-weight: 600;">#000000</div>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem;">Change Font Size</label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="range" id="selected-text-size" min="8" max="120" value="40" style="flex: 1;">
                        <span style="font-size: 0.85rem; font-weight: 600; min-width: 40px;"><span id="selected-size-value">40</span>px</span>
                    </div>
                </div>

                <button style="width: 100%; padding: 0.75rem; background-color: #ff6b6b; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600;" id="delete-text-btn">🗑 Delete</button>
            </div>
```

---

## 📝 PATCH 2: JavaScript Functions (Around line 710)

**Location**: In the `<script>` section, add right after the `setupTextEditor()` function

**Add this code**:
```javascript
        // ==================== SELECTED TEXT COLOR CHANGE (NEW) ====================
        function setupSelectedTextControls() {
            // Color picker for selected text
            document.getElementById('selected-text-color').addEventListener('input', function(e) {
                const newColor = e.target.value;
                document.getElementById('selected-color-display').textContent = newColor.toUpperCase();

                if (customizer.selectedLayerId !== null) {
                    const layer = customizer.textLayers.find(l => l.id === customizer.selectedLayerId);
                    if (layer) {
                        layer.color = newColor;
                        createTextTexture();
                        updateLayersList();
                        app.showNotification(`✓ Color changed to ${newColor}`, 'success');
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
                        layer.size = newSize;
                        createTextTexture();
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
                    app.showNotification('✓ Text deleted', 'success');
                }
            });
        }
```

---

## 📝 PATCH 3: Update selectLayer() Function

**Location**: Find the existing `selectLayer()` function (around line 750)

**Find this**:
```javascript
        function selectLayer(layerId) {
            customizer.selectedLayerId = layerId;
            customizer.layerDragMode = true;
            if (layerId === 'image') {
                app.showNotification('💡 Drag to move image • Drag from corners to resize', 'info');
            } else {
                app.showNotification('💡 Drag on the 3D product to reposition this text', 'info');
            }
        }
```

**Replace with**:
```javascript
        function selectLayer(layerId) {
            customizer.selectedLayerId = layerId;
            customizer.layerDragMode = true;

            if (layerId === 'image') {
                document.getElementById('selected-text-options').style.display = 'none';
                app.showNotification('💡 Drag to move image • Drag from corners to resize', 'info');
            } else {
                const layer = customizer.textLayers.find(l => l.id === layerId);
                if (layer) {
                    const panel = document.getElementById('selected-text-options');
                    panel.style.display = 'block';

                    document.getElementById('selected-text-preview').textContent = layer.content;
                    document.getElementById('selected-text-preview').style.color = layer.color;
                    document.getElementById('selected-text-preview').style.borderLeftColor = layer.color;

                    document.getElementById('selected-text-color').value = layer.color;
                    document.getElementById('selected-color-display').textContent = layer.color.toUpperCase();

                    document.getElementById('selected-text-size').value = layer.size;
                    document.getElementById('selected-size-value').textContent = layer.size;

                    app.showNotification(`✎ Editing: "${layer.content.substring(0, 30)}"`, 'info');
                }
            }
        }
```

---

## 📝 PATCH 4: Initialize on Page Load

**Location**: In `initializeScene()` function (around end of function, line 240)

**Find this**:
```javascript
            // Start animation loop
            animate();

            window.addEventListener('resize', onWindowResize);
        }
```

**Replace with**:
```javascript
            // Start animation loop
            animate();
            
            // NEW: Setup selected text color controls
            setupSelectedTextControls();

            window.addEventListener('resize', onWindowResize);
        }
```

---

## 📝 PATCH 5: Update Color Buttons Handler

**Location**: In `setupColorButtons()` function (around line 710)

**Find this**:
```javascript
        function setupColorButtons() {
            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const color = btn.dataset.color;
```

**Replace with**:
```javascript
        function setupColorButtons() {
            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    // NEW: Hide selected text options when product color changes
                    document.getElementById('selected-text-options').style.display = 'none';
                    customizer.selectedLayerId = null;

                    const color = btn.dataset.color;
```

---

## 🧪 Testing

### Test 1: Add Text
1. Enter text in "Text Content" textarea
2. Click "Add Text to Product"
3. ✅ Text appears on 3D mug

### Test 2: Select Text
1. Click text in "Design Layers" panel
2. ✅ "Edit Selected Text" panel appears
3. ✅ Color picker shows current color
4. ✅ Text preview shows with color

### Test 3: Change Color
1. Select any text layer
2. Click color picker
3. Choose new color
4. ✅ Text on mug changes immediately
5. ✅ Color display shows hex code

### Test 4: Change Font Size
1. Select any text layer
2. Adjust font size slider
3. ✅ Text on mug resizes immediately

### Test 5: Delete Text
1. Select any text layer
2. Click "🗑 Delete" button
3. ✅ Text removed from mug
4. ✅ Panel hides
5. ✅ Layer list updates

---

## ⚠️ Common Issues

### Issue: Panel doesn't appear when selecting text
**Fix**: Check that `selectLayer()` was updated correctly (Patch 3)

### Issue: Color doesn't update on mug
**Fix**: Make sure `createTextTexture()` is called (check Patch 2)

### Issue: No notification message appears
**Fix**: Your `app.showNotification()` function might differ. Replace with:
```javascript
console.log('Text color changed!');  // Or your notification method
```

---

## 📱 Browser Test Checklist

- [ ] Works on Chrome
- [ ] Works on Firefox
- [ ] Works on Safari (Mac)
- [ ] Works on Edge
- [ ] Color picker opens
- [ ] Real-time update works
- [ ] No console errors

---

## 🎯 Full Flow Summary

**User adds text:**
```
User enters text → Clicks "Add Text" → Text appears in "Design Layers" → Creates layer object
```

**User selects and edits text:**
```
User clicks layer in "Design Layers" → selectLayer() called → Panel appears → User sees color/size controls
```

**User changes color:**
```
User picks new color → Input event → layer.color updated → createTextTexture() → Mug updates instantly
```

**User deletes text:**
```
User clicks "Delete" → Layer filtered out → UI refreshed → Texture re-rendered
```

---

## 📚 Reference Files

- **Working Example**: `text-color-changer-example.html` (standalone, double-click to run)
- **Full Guide**: `TEXT_COLOR_INTEGRATION_GUIDE.md` (detailed docs)
- **Production Code**: `customizer-product.php` (your main file)

---

## ✨ You're Done!

All 5 patches applied correctly = Full text color changer functionality!

**Next time:** Try adding more features like:
- Copy/duplicate text layer
- Text shadow effects
- Font italics/bold while editing
- Batch color operations

**Share feedback**: Open issues or suggestions welcome! 🎨
