<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// THE RBAC LOCK: Block staff from inventory management
if ($_SESSION['admin_role'] !== 'admin') {
    die("<div style='padding: 50px; text-align: center; font-family: sans-serif; color: #721c24; background: #f8d7da;'>
            <h1>Access Denied 🛑</h1>
            <p>You do not have Administrator privileges to manage inventory or pricing.</p>
            <a href='orders.php'>Return to Orders</a>
         </div>");
}

require_once '../db.php';

$message = '';

// 1. Handle Delete Operation
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id = $id");
    $message = "<div style='background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'>Product deleted successfully!</div>";
}

// 2. Handle Create & Update Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $base_price = floatval($_POST['base_price']);
    $available = isset($_POST['available']) ? 1 : 0;
    $description = trim($_POST['description']);
    // Setting default empty JSON for specifications to prevent database errors
    $specifications = '{}'; 

    if (!empty($_POST['product_id'])) {
        // UPDATE Existing Product
        $id = intval($_POST['product_id']);
        $stmt = $conn->prepare("UPDATE products SET name=?, category=?, base_price=?, available=?, description=? WHERE id=?");
        $stmt->bind_param("ssdisi", $name, $category, $base_price, $available, $description, $id);
        if($stmt->execute()) {
            $message = "<div style='background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'>Product updated successfully!</div>";
        }
    } else {
        // CREATE New Product
        $stmt = $conn->prepare("INSERT INTO products (name, category, base_price, available, description, specifications) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $name, $category, $base_price, $available, $description, $specifications);
        if($stmt->execute()) {
            $message = "<div style='background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'>New product added to inventory!</div>";
        }
    }
}

// Check if we are editing a specific product
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
}

// Fetch all products for the Read operation
$products = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory CRUD - Triangle Admin</title>
    <style>
        :root { --primary: #E31E24; --dark: #333; --light: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light); display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--dark); color: white; padding: 2rem 0; display: flex; flex-direction: column; flex-shrink: 0;}
        .sidebar h2 { text-align: center; color: var(--primary); margin-bottom: 2rem; font-size: 1.5rem; }
        .sidebar a { color: white; text-decoration: none; padding: 1rem 2rem; display: block; border-left: 4px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); border-left-color: var(--primary); }
        .content { flex: 1; padding: 2rem; overflow-y: auto; }
        .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 2rem;}
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f9f9f9; color: #555; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 0.9rem; display: inline-block;}
        .btn-primary { background: var(--dark); color: white; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        input, select, textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 1rem; box-sizing: border-box; font-family: inherit;}
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Triangle Admin</h2>
        
        <?php if($_SESSION['admin_role'] === 'admin'): ?>
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard & Analytics</a>
        <?php else: ?>
            <a href="staff_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'staff_dashboard.php' ? 'active' : ''; ?>">Staff Home</a>
        <?php endif; ?>
        
        <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">Manage Orders</a>
        
        <?php if($_SESSION['admin_role'] === 'admin'): ?>
            <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">Products (CRUD)</a>
            <a href="customers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>">User Management</a>
            <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">System Security</a>
        <?php endif; ?>
        
        <a href="logout.php" style="margin-top: auto; background-color: #c82333;">Logout</a>
    </div>

    <div class="content">
        <h1 style="margin-top: 0;">Inventory & Pricing Management</h1>
        <p>Control storefront items, categories, and adjust base pricing.</p>

        <?php echo $message; ?>

        <div class="card">
            <h3 style="margin-top: 0;"><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h3>
            <form method="POST" action="products.php">
                <input type="hidden" name="product_id" value="<?php echo $edit_product ? $edit_product['id'] : ''; ?>">
                
                <div style="display: flex; gap: 1rem;">
                    <div style="flex: 1;">
                        <label>Product Name</label>
                        <input type="text" name="name" required value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>">
                    </div>
                    <div style="flex: 1;">
                        <label>Category</label>
                        <input type="text" name="category" required value="<?php echo $edit_product ? htmlspecialchars($edit_product['category']) : ''; ?>">
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; align-items: center;">
                    <div style="flex: 1;">
                        <label>Base Price ($)</label>
                        <input type="number" step="0.01" name="base_price" required value="<?php echo $edit_product ? $edit_product['base_price'] : ''; ?>">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="available" value="1" style="width: auto; margin: 0;" <?php echo ($edit_product && $edit_product['available'] == 1) ? 'checked' : ''; ?>>
                            Available in Storefront
                        </label>
                    </div>
                </div>

                <label>Description</label>
                <textarea name="description" rows="3"><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>

                <button type="submit" name="save_product" class="btn btn-primary">
                    <?php echo $edit_product ? 'Update Inventory' : 'Add to Inventory'; ?>
                </button>
                <?php if($edit_product): ?>
                    <a href="products.php" class="btn" style="background: #ccc; color: #333;">Cancel Edit</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h3 style="margin-top: 0;">Current Storefront Inventory</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while($row = $products->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td>$<?php echo number_format($row['base_price'], 2); ?></td>
                        <td>
                            <?php if($row['available'] == 1): ?>
                                <span style="background: #d4edda; color: #155724; padding: 3px 8px; border-radius: 10px; font-size: 0.8rem;">Active</span>
                            <?php else: ?>
                                <span style="background: #f8d7da; color: #721c24; padding: 3px 8px; border-radius: 10px; font-size: 0.8rem;">Hidden</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="products.php?edit=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="products.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
</body>
</html>