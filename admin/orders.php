<?php
/**
 * Admin Orders Management
 */
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /triangle-ecommerce/login.php');
    exit();
}

// Handle status update
if ($_POST['action'] === 'update_status' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $status = sanitize($_POST['status']);
    
    $statuses = ['pending', 'processing', 'printing', 'prepared', 'shipped', 'delivered', 'cancelled'];
    if (in_array($status, $statuses)) {
        executeQuery("UPDATE orders SET status = '$status' WHERE id = $order_id");
    }
}

// Get all orders
$ordersResult = executeQuery("SELECT o.*, u.email, u.first_name, u.last_name FROM orders o 
                            JOIN users u ON o.user_id = u.id 
                            ORDER BY o.created_at DESC");
$orders = $ordersResult ? $ordersResult->fetch_all(MYSQLI_ASSOC) : [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders - Admin Panel</title>
    <link rel="stylesheet" href="/triangle-ecommerce/assets/css/style.css">
    <link rel="stylesheet" href="/triangle-ecommerce/assets/css/responsive.css">
    <style>
        .admin-container { display: flex; height: 100vh; background-color: var(--light-gray); }
        .admin-sidebar { width: 250px; background-color: var(--dark-gray); color: var(--white); padding: 2rem 0; overflow-y: auto; }
        .admin-sidebar-item { padding: 1rem 1.5rem; border-left: 4px solid transparent; cursor: pointer; transition: var(--transition); }
        .admin-sidebar-item:hover, .admin-sidebar-item.active { background-color: rgba(255, 255, 255, 0.1); border-left-color: var(--primary-red); }
        .admin-sidebar a { color: var(--white); text-decoration: none; display: block; }
        .admin-content { flex: 1; overflow-y: auto; }
        .admin-header { background-color: var(--white); padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); }
        .admin-main { padding: 2rem; }
        .admin-table { background-color: var(--white); border-radius: 0.75rem; overflow: auto; box-shadow: var(--shadow-light); }
        .admin-table table { width: 100%; border-collapse: collapse; }
        .admin-table th { background-color: var(--light-gray); padding: 1rem; text-align: left; font-weight: 600; border-bottom: 2px solid var(--border-color); }
        .admin-table td { padding: 1rem; border-bottom: 1px solid var(--border-color); }
        .admin-table tr:hover { background-color: rgba(227, 30, 36, 0.03); }
    </style>
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div style="padding: 0 1.5rem; margin-bottom: 2rem;">
                <h3 style="color: var(--primary-red);">△ ADMIN</h3>
            </div>
            <a href="/triangle-ecommerce/admin/" class="admin-sidebar-item">Dashboard</a>
            <a href="/triangle-ecommerce/admin/orders.php" class="admin-sidebar-item active">Orders</a>
            <a href="/triangle-ecommerce/admin/customers.php" class="admin-sidebar-item">Customers</a>
            <a href="/triangle-ecommerce/admin/products.php" class="admin-sidebar-item">Products</a>
            <a href="/triangle-ecommerce/admin/designs.php" class="admin-sidebar-item">Designs</a>
            <a href="/triangle-ecommerce/admin/settings.php" class="admin-sidebar-item">Settings</a>
            <hr style="border: none; border-top: 1px solid rgba(255, 255, 255, 0.1); margin: 2rem 0;">
            <a href="/triangle-ecommerce/logout.php" class="admin-sidebar-item" style="color: #E74C3C;">Logout</a>
        </aside>

        <div class="admin-content">
            <div class="admin-header">
                <h2>Orders Management</h2>
            </div>

            <div class="admin-main">
                <div class="admin-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong><?php echo substr($order['order_number'], -8); ?></strong></td>
                                    <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" style="padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 0.25rem; cursor: pointer;">
                                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="printing" <?php echo $order['status'] === 'printing' ? 'selected' : ''; ?>>Printing</option>
                                                <option value="prepared" <?php echo $order['status'] === 'prepared' ? 'selected' : ''; ?>>Prepared</option>
                                                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="#order-<?php echo $order['id']; ?>" onclick="alert('Order details coming soon!')" style="color: var(--primary-red); font-weight: 600; text-decoration: none; cursor: pointer;">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
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
