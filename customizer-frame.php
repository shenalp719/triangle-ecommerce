<?php
/**
 * Frame Customizer - Advanced 3D Design Tool
 * Triangle Printing Solutions
 * Using Fabric.js & Three.js
 */
session_start();
require_once 'db.php';

$page_title = 'Frame Customizer';
include 'includes/header.php';
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

    <section style="background-color: var(--light-gray); padding: 2rem; margin-bottom: 0;">
        <div class="container-md">
            <h1 style="margin-bottom: 0.5rem;">Frame Customizer</h1>
            <p style="color: var(--text-light);">Upload your image and visualize your design on a realistic 3D model</p>
        </div>
    </section>

    <section class="customizer-container" style="display: flex; gap: 2rem; padding: 2rem; min-height: calc(100vh - 200px);">
        <div class="tools-panel" style="width: 280px; background-color: var(--white); border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.5rem; overflow-y: auto;">
            <h4 style="margin-bottom: 1.5rem;">Tools</h4>

            <div class="tool-section">
                <label class="tool-label" style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Upload Image</label>
                <input type="file" id="image-upload" accept="image/*" style="width: 100%; padding: 0.75rem; border: 2px dashed var(--primary-red); border-radius: 0.5rem; cursor: pointer;">
                <small style="color: var(--text-light);">JPG, PNG (max 10MB)</small>
            </div>

            <div class="tool-section" style="margin-top: 1.5rem;">
                <label class="tool-label" style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Frame Style</label>
                <select id="frame-style" class="tool-input" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                    <option value="minimal">Minimal (Black)</option>
                    <option value="wood">Wood Frame</option>
                    <option value="gold">Gold Frame</option>
                    <option value="white">White Frame</option>
                    <option value="none">No Frame</option>
                </select>
            </div>

            <div class="tool-section" style="margin-top: 1.5rem;">
                <label class="tool-label" style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Background Color</label>
                <input type="color" id="bg-color" value="#ffffff" style="width: 100%; height: 45px; cursor: pointer; border: none; border-radius: 0.5rem;">
            </div>

            <div class="tool-section" style="margin-top: 1.5rem;">
                <label class="tool-label" style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Image Position</label>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.25rem; font-size: 0.85rem;">Horizontal (X): <span id="pos-x-value">0</span></label>
                    <input type="range" id="pos-x-slider" min="-300" max="300" value="0" style="width: 100%; cursor: pointer;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.25rem; font-size: 0.85rem;">Vertical (Y): <span id="pos-y-value">0</span></label>
                    <input type="range" id="pos-y-slider" min="-300" max="300" value="0" style="width: 100%; cursor: pointer;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.25rem; font-size: 0.85rem;">Zoom: <span id="zoom-value">100</span>%</label>
                    <input type="range" id="zoom-slider" min="50" max="200" value="100" style="width: 100%; cursor: pointer;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.25rem; font-size: 0.85rem;">Rotation: <span id="rotation-value">0</span>°</label>
                    <input type="range" id="rotation-slider" min="0" max="360" value="0" style="width: 100%; cursor: pointer;">
                </div>

                <button class="btn btn-secondary btn-sm" id="auto-fit" style="width: 100%; margin-top: 0.5rem;">
                    Auto-Fit / Reset Image
                </button>
            </div>

            <div class="tool-section" style="margin-top: 1.5rem;">
                <label class="tool-label" style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Poster Size</label>
                <select id="poster-size" class="tool-input" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                    <option value="8x10">8x10 ($25)</option>
                    <option value="11x14">11x14 ($35)</option>
                    <option value="16x20">16x20 ($45)</option>
                    <option value="18x24">18x24 ($55)</option>
                    <option value="24x36">24x36 ($75)</option>
                </select>
            </div>

            <button class="btn btn-dark btn-sm btn-block" id="reset-design" style="margin-top: 1.5rem;">
                Reset Design
            </button>
        </div>

        <div class="canvas-wrapper" style="flex: 1;">
            <div style="height: 100%; background-color: var(--light-gray); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; border: 1px solid var(--border-color);">
                <div style="position: absolute; top: 0.5rem; left: 0.5rem; font-size: 0.75rem; background-color: rgba(0,0,0,0.6); color: white; padding: 0.5rem 1rem; border-radius: 2rem; z-index: 10; pointer-events: none;">
                    Left Click: Rotate • Scroll: Zoom • Right Click: Pan
                </div>
                <div id="preview-container" style="width: 100%; height: 100%; min-height: 550px;"></div>
            </div>
        </div>

        <div class="properties-panel" style="width: 300px; background-color: var(--white); border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.5rem; overflow-y: auto;">
            <h4 style="margin-bottom: 1.5rem;">Print Minimap</h4>

            <div id="minimap-wrapper">
                <canvas id="canvas"></canvas>
            </div>
            <small style="color: var(--text-light); display: block; margin-bottom: 1.5rem; text-align: center;">Drag image to adjust print boundaries</small>

            <div style="background-color: var(--light-gray); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
                <small style="color: var(--text-light);">Price</small>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary-red);" id="price-display">$25.00</div>
                <small style="color: var(--text-light);">Includes frame & printing</small>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Quantity</label>
                <div style="display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 0.5rem; overflow: hidden;">
                    <button class="qty-adjust" data-value="-1" style="flex: 0 0 40px; height: 40px; background-color: var(--light-gray); border: none; cursor: pointer; font-weight: 600;">−</button>
                    <input type="number" id="quantity" value="1" min="1" style="flex: 1; border: none; text-align: center; font-weight: 600;" />
                    <button class="qty-adjust" data-value="+1" style="flex: 0 0 40px; height: 40px; background-color: var(--light-gray); border: none; cursor: pointer; font-weight: 600;">+</button>
                </div>
            </div>

            <button class="btn btn-primary btn-block" id="add-to-cart" style="padding: 1rem; margin-bottom: 0.75rem;">
                Add to Cart
            </button>

            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="btn btn-secondary btn-block" id="save-design" style="padding: 1rem;">
                    Save Design
                </button>
            <?php else: ?>
                <a href="login.php" class="btn btn-secondary btn-block" style="padding: 1rem; text-align: center;">
                    Login to Save
                </a>
            <?php endif; ?>

            <button class="btn btn-dark btn-sm btn-block" id="export-design" style="margin-top: 0.75rem;">
                Download Mockup
            </button>
        </div>
    </section>

    <style>
        .customizer-container {
            display: flex;
            gap: 2rem;
            padding: 2rem;
            min-height: calc(100vh - 200px);
        }

        .tools-panel, .properties-panel {
            flex-shrink: 0;
            overflow-y: auto;
        }

        .canvas-wrapper {
            flex: 1;
            z-index: 1;
        }

        /* Minimap specific styling */
        #minimap-wrapper {
            height: 260px;
            overflow: hidden;
            border-radius: 0.5rem;
            background-color: var(--light-gray);
            border: 2px solid var(--primary-red);
            margin-bottom: 0.5rem;
            position: relative;
        }

        /* Visual scaling for the 600px minimap */
        .canvas-container {
            transform: scale(0.42);
            transform-origin: top left;
            pointer-events: auto;
        }

        #canvas {
            max-width: 100%;
            max-height: 100%;
            cursor: crosshair;
        }

        .qty-adjust:active {
            background-color: var(--primary-red) !important;
            color: white;
        }

        @media (max-width: 1200px) {
            .customizer-container {
                flex-direction: column;
                padding: 1rem;
                min-height: auto;
            }
            .tools-panel, .properties-panel { width: 100% !important; }
            .canvas-wrapper { min-height: 500px; }
        }
    </style>

    <script src="/triangle-ecommerce/assets/js/customizer-3d-frame.js"></script>

<?php include 'includes/footer.php'; ?>