<?php
/**
 * Frame Customizer - Advanced Design Tool
 * Triangle Printing Solutions
 * Using Fabric.js
 */
session_start();
require_once 'db.php';

$page_title = 'Frame Customizer';
include 'includes/header.php';
?>

    <section style="background-color: var(--light-gray); padding: 2rem; margin-bottom: 0;">
        <div class="container-md">
            <h1 style="margin-bottom: 0.5rem;">Frame Customizer</h1>
            <p style="color: var(--text-light);">Upload your image and customize your perfect frame poster</p>
        </div>
    </section>

    <section class="customizer-container" style="display: flex; gap: 2rem; padding: 2rem; min-height: calc(100vh - 200px);">
        <!-- Left Panel - Tools -->
        <div class="tools-panel" style="width: 280px; background-color: var(--white); border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.5rem; overflow-y: auto;">
            <h4 style="margin-bottom: 1.5rem;">Tools</h4>

            <!-- Upload Image -->
            <div class="tool-section">
                <label class="tool-label" style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Upload Image</label>
                <input type="file" id="image-upload" accept="image/*" style="width: 100%; padding: 0.75rem; border: 2px dashed var(--primary-red); border-radius: 0.5rem; cursor: pointer;">
                <small style="color: var(--text-light);">JPG, PNG (max 10MB)</small>
            </div>

            <!-- Frame Style -->
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

            <!-- Background Color -->
            <div class="tool-section" style="margin-top: 1.5rem;">
                <label class="tool-label" style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Background Color</label>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <input type="color" id="bg-color" value="#ffffff" style="width: 100%; height: 45px; cursor: pointer; border: none; border-radius: 0.5rem;">
                </div>
            </div>

            <!-- Image Controls -->
            <div class="tool-section" style="margin-top: 1.5rem;">
                <label class="tool-label" style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Image Position</label>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.25rem; font-size: 0.85rem;">Zoom: <span id="zoom-value">100</span>%</label>
                    <input type="range" id="zoom-slider" min="50" max="200" value="100" style="width: 100%; cursor: pointer;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.25rem; font-size: 0.85rem;">Rotation: <span id="rotation-value">0</span>°</label>
                    <input type="range" id="rotation-slider" min="0" max="360" value="0" style="width: 100%; cursor: pointer;">
                </div>

                <button class="btn btn-secondary btn-sm" id="auto-fit" style="width: 100%; margin-top: 0.5rem;">
                    Auto-Fit Image
                </button>
            </div>

            <!-- Quality Settings -->
            <div class="tool-section" style="margin-top: 1.5rem; padding: 1rem; background-color: var(--light-gray); border-radius: 0.5rem;">
                <label class="tool-label" style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Resolution</label>
                <select id="resolution" class="tool-input" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                    <option value="72">Web (72 DPI)</option>
                    <option value="150">Medium (150 DPI)</option>
                    <option value="300" selected>Print (300 DPI) ✓</option>
                </select>
                <small style="color: var(--success); display: block; margin-top: 0.5rem;">✓ High resolution recommended for print</small>
            </div>

            <!-- Size Options -->
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

            <!-- Reset -->
            <button class="btn btn-dark btn-sm btn-block" id="reset-design" style="margin-top: 1.5rem;">
                Reset Design
            </button>
        </div>

        <!-- Center - Canvas -->
        <div class="canvas-wrapper" style="flex: 1;">
            <div style="height: 100%; background-color: var(--light-gray); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0.5rem; left: 0.5rem; font-size: 0.75rem; background-color: rgba(0,0,0,0.5); color: white; padding: 0.5rem; border-radius: 0.25rem;">
                    Drag to move • Scroll to zoom
                </div>
                <canvas id="canvas" style="border: 2px solid var(--primary-red); border-radius: 0.5rem;"></canvas>
            </div>
        </div>

        <!-- Right Panel - Properties & Preview -->
        <div class="properties-panel" style="width: 300px; background-color: var(--white); border: 1px solid var(--border-color); border-radius: 0.75rem; padding: 1.5rem; overflow-y: auto;">
            <h4 style="margin-bottom: 1.5rem;">Preview & Order</h4>

            <!-- Preview -->
            <div style="margin-bottom: 1.5rem; border-radius: 0.5rem; overflow: hidden; background-color: var(--light-gray); height: 250px; display: flex; align-items: center; justify-content: center;">
                <div id="preview-container" style="width: 100%; height: 100%; position: relative;">
                    <canvas id="preview-canvas" style="display: block;"></canvas>
                </div>
            </div>

            <!-- Price Display -->
            <div style="background-color: var(--light-gray); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
                <small style="color: var(--text-light);">Price</small>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary-red);" id="price-display">$25.00</div>
                <small style="color: var(--text-light);">Includes frame & printing</small>
            </div>

            <!-- Quantity -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Quantity</label>
                <div style="display: flex; align-items: center; border: 1px solid var(--border-color); border-radius: 0.5rem; overflow: hidden;">
                    <button class="qty-adjust" data-value="-1" style="flex: 0 0 40px; height: 40px; background-color: var(--light-gray); border: none; cursor: pointer; font-weight: 600;">−</button>
                    <input type="number" id="quantity" value="1" min="1" style="flex: 1; border: none; text-align: center; font-weight: 600;" />
                    <button class="qty-adjust" data-value="+1" style="flex: 0 0 40px; height: 40px; background-color: var(--light-gray); border: none; cursor: pointer; font-weight: 600;">+</button>
                </div>
            </div>

            <!-- Add to Cart -->
            <button class="btn btn-primary btn-block" id="add-to-cart" style="padding: 1rem; margin-bottom: 0.75rem;">
                Add to Cart
            </button>

            <!-- Save Design -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <button class="btn btn-secondary btn-block" id="save-design" style="padding: 1rem;">
                    Save Design
                </button>
            <?php else: ?>
                <a href="login.php" class="btn btn-secondary btn-block" style="padding: 1rem; text-align: center;">
                    Login to Save
                </a>
            <?php endif; ?>

            <!-- Export -->
            <button class="btn btn-dark btn-sm btn-block" id="export-design" style="margin-top: 0.75rem;">
                Download PNG
            </button>

            <!-- Safe Print Area Indicator -->
            <div style="margin-top: 1.5rem; padding: 1rem; background-color: #fff3cd; border-radius: 0.5rem; border-left: 4px solid var(--warning);">
                <h6 style="margin-bottom: 0.5rem;">📋 Print Area</h6>
                <small style="color: var(--text-dark);">Red outline shows safe print boundary</small>
            </div>
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

            .tools-panel, .properties-panel {
                width: 100% !important;
            }

            .canvas-wrapper {
                min-height: 400px;
            }
        }

        @media (max-width: 767px) {
            .customizer-container {
                padding: 1rem;
            }

            .tools-panel {
                order: 3;
                width: 100%;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                border-radius: 1rem 1rem 0 0;
                max-height: 50vh;
                z-index: 100;
            }

            .canvas-wrapper {
                order: 1;
                flex: 1;
                min-height: calc(100vh - 300px);
            }

            .properties-panel {
                order: 2;
                width: 100%;
                max-height: none;
            }
        }
    </style>

    <script src="/triangle-ecommerce/assets/js/customizer.js"></script>

<?php include 'includes/footer.php'; ?>
