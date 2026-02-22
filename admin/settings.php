<?php
/**
 * Admin Settings
 */
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /triangle-ecommerce/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <link rel="stylesheet" href="/triangle-ecommerce/assets/css/style.css">
    <link rel="stylesheet" href="/triangle-ecommerce/assets/css/responsive.css">
    <style>
        .admin-container { display: flex; min-height: 100vh; background-color: var(--light-gray); }
        .admin-sidebar { width: 250px; background-color: var(--dark-gray); color: var(--white); padding: 2rem 0; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .admin-sidebar-item { padding: 1rem 1.5rem; border-left: 4px solid transparent; cursor: pointer; transition: var(--transition); }
        .admin-sidebar-item:hover, .admin-sidebar-item.active { background-color: rgba(255, 255, 255, 0.1); border-left-color: var(--primary-red); }
        .admin-sidebar a { color: var(--white); text-decoration: none; display: block; }
        .admin-content { flex: 1; overflow-y: auto; }
        .admin-header { background-color: var(--white); padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); }
        .admin-main { padding: 2rem max-width: 1000px; margin: 0 auto; }
        .settings-card { background-color: var(--white); padding: 2rem; border-radius: 0.75rem; margin-bottom: 1.5rem; box-shadow: var(--shadow-light); }
        .settings-card h3 { margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div style="padding: 0 1.5rem; margin-bottom: 2rem;">
                <h3 style="color: var(--primary-red);">△ ADMIN</h3>
            </div>
            <a href="/triangle-ecommerce/admin/" class="admin-sidebar-item">Dashboard</a>
            <a href="/triangle-ecommerce/admin/orders.php" class="admin-sidebar-item">Orders</a>
            <a href="/triangle-ecommerce/admin/customers.php" class="admin-sidebar-item">Customers</a>
            <a href="/triangle-ecommerce/admin/products.php" class="admin-sidebar-item">Products</a>
            <a href="/triangle-ecommerce/admin/designs.php" class="admin-sidebar-item">Designs</a>
            <a href="/triangle-ecommerce/admin/settings.php" class="admin-sidebar-item active">Settings</a>
            <hr style="border: none; border-top: 1px solid rgba(255, 255, 255, 0.1); margin: 2rem 0;">
            <a href="/triangle-ecommerce/logout.php" class="admin-sidebar-item" style="color: #E74C3C;">Logout</a>
        </aside>

        <div class="admin-content">
            <div class="admin-header">
                <h2>Settings</h2>
            </div>

            <div class="admin-main">
                <div class="settings-card">
                    <h3>Shipping Rates</h3>
                    <form onsubmit="alert('Shipping rates updated!'); return false;">
                        <div class="grid grid-2">
                            <div class="form-group">
                                <label>Standard Shipping Cost</label>
                                <input type="number" step="0.01" value="15" required>
                            </div>
                            <div class="form-group">
                                <label>Express Shipping Cost</label>
                                <input type="number" step="0.01" value="30" required>
                            </div>
                            <div class="form-group">
                                <label>Free Shipping Threshold</label>
                                <input type="number" step="0.01" value="150" required>
                            </div>
                            <div class="form-group">
                                <label>TAX Rate (%)</label>
                                <input type="number" step="0.01" value="8" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>

                <div class="settings-card">
                    <h3>Product Pricing</h3>
                    <p style="margin-bottom: 1rem; color: var(--text-light);">Manage base prices for products</p>
                    <form onsubmit="alert('Prices updated!'); return false;">
                        <div class="grid grid-3">
                            <div class="form-group">
                                <label>Frame Poster (8x10)</label>
                                <input type="number" step="0.01" value="25" required>
                            </div>
                            <div class="form-group">
                                <label>Coffee Mug (11oz)</label>
                                <input type="number" step="0.01" value="12" required>
                            </div>
                            <div class="form-group">
                                <label>T-Shirt</label>
                                <input type="number" step="0.01" value="18" required>
                            </div>
                            <div class="form-group">
                                <label>Custom Cap</label>
                                <input type="number" step="0.01" value="15" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Prices</button>
                    </form>
                </div>

                <div class="settings-card">
                    <h3>Quality Standards</h3>
                    <form onsubmit="alert('Quality standards updated!'); return false;">
                        <div class="form-group">
                            <label>Minimum Resolution (DPI)</label>
                            <input type="number" value="300" required>
                        </div>
                        <div class="form-group">
                            <label>Resolution Warning Threshold (DPI)</label>
                            <input type="number" value="150" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>

                <div class="settings-card">
                    <h3>System Maintenance</h3>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <button class="btn btn-secondary" onclick="alert('Cache cleared!')">Clear Cache</button>
                        <button class="btn btn-secondary" onclick="alert('Database optimized!')">Optimize Database</button>
                        <button class="btn btn-secondary" onclick="alert('Backup started!')">Create Backup</button>
                        <button class="btn btn-dark" onclick="alert('Pending...')">View Logs</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
?>
