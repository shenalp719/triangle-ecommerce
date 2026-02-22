<?php
/**
 * Admin Products Management
 */
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /triangle-ecommerce/login.php');
    exit();
}

// Get all products
$productsResult = executeQuery("SELECT * FROM products ORDER BY created_at DESC");
$products = $productsResult ? $productsResult->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin Panel</title>
    <link rel="stylesheet" href="/triangle-ecommerce/assets/css/style.css">
    <link rel="stylesheet" href="/triangle-ecommerce/assets/css/responsive.css">
    <style>
        .admin-container { display: flex; min-height: 100vh; background-color: var(--light-gray); }
        .admin-sidebar { width: 250px; background-color: var(--dark-gray); color: var(--white); padding: 2rem 0; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .admin-sidebar-item { padding: 1rem 1.5rem; border-left: 4px solid transparent; cursor: pointer; transition: var(--transition); }
        .admin-sidebar-item:hover, .admin-sidebar-item.active { background-color: rgba(255, 255, 255, 0.1); border-left-color: var(--primary-red); }
        .admin-sidebar a { color: var(--white); text-decoration: none; display: block; }
        .admin-content { flex: 1; overflow-y: auto; }
        .admin-header { background-color: var(--white); padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; }
        .admin-main { padding: 2rem; }
        .admin-table { background-color: var(--white); border-radius: 0.75rem; overflow: auto; box-shadow: var(--shadow-light); }
        .admin-table table { width: 100%; border-collapse: collapse; }
        .admin-table th { background-color: var(--light-gray); padding: 1rem; text-align: left; font-weight: 600; border-bottom: 2px solid var(--border-color); }
        .admin-table td { padding: 1rem; border-bottom: 1px solid var(--border-color); }
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
            <a href="/triangle-ecommerce/admin/products.php" class="admin-sidebar-item active">Products</a>
            <a href="/triangle-ecommerce/admin/designs.php" class="admin-sidebar-item">Designs</a>
            <a href="/triangle-ecommerce/admin/settings.php" class="admin-sidebar-item">Settings</a>
            <hr style="border: none; border-top: 1px solid rgba(255, 255, 255, 0.1); margin: 2rem 0;">
            <a href="/triangle-ecommerce/logout.php" class="admin-sidebar-item" style="color: #E74C3C;">Logout</a>
        </aside>

        <div class="admin-content">
            <div class="admin-header">
                <h2>Products Management</h2>
                <button class="btn btn-primary btn-sm" onclick="alert('Add product functionality coming soon!')">+ Add Product</button>
            </div>

            <div class="admin-main">
                <div class="admin-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($products) > 0): ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                                        <td><?php echo ucfirst($product['category']); ?></td>
                                        <td>$<?php echo number_format($product['base_price'], 2); ?></td>
                                        <td>
                                            <span class="badge" style="background-color: <?php echo $product['available'] ? '#27AE60' : '#E74C3C'; ?>; color: white;">
                                                <?php echo $product['available'] ? 'Available' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" onclick="alert('Edit functionality coming soon!')" style="color: var(--primary-red); margin-right: 1rem;">Edit</a>
                                            <a href="#" onclick="alert('Delete functionality coming soon!')" style="color: #E74C3C;">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 2rem;">No products found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
?>
