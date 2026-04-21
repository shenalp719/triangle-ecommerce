<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
require_once '../db.php';

// Handle Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = sanitize($_POST['status']);
    $update_stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $order_id);
    $update_stmt->execute();
    header("Location: orders.php"); // Refresh page
    exit();
}

// Fetch all orders, newest first
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Triangle Printing</title>
    <style>
        :root { --primary: #E31E24; --dark: #333; --light: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light); display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--dark); color: white; padding: 2rem 0; display: flex; flex-direction: column; flex-shrink: 0;}
        .sidebar h2 { text-align: center; color: var(--primary); margin-bottom: 2rem; font-size: 1.5rem; }
        .sidebar a { color: white; text-decoration: none; padding: 1rem 2rem; display: block; border-left: 4px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); border-left-color: var(--primary); }
        
        .content { flex: 1; padding: 2rem; overflow-y: auto; }
        .order-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 1.5rem; border-left: 5px solid #ccc; }
        .status-pending { border-left-color: #f39c12; }
        .status-printing { border-left-color: #3498db; }
        .status-ready { border-left-color: #2ecc71; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f9f9f9; }
        
        .print-specs { background: #f8f9fa; padding: 1rem; border-radius: 5px; font-family: monospace; font-size: 0.9rem; color: #d63031; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Triangle Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="orders.php" class="active">Manage Orders</a>
        <a href="customers.php">Customers</a>
        <a href="logout.php" style="margin-top: auto; background-color: #c82333;">Logout</a>
    </div>

    <div class="content">
        <h1 style="margin-top: 0;">Order Processing Pipeline</h1>
        <p>Review custom designs and update tracking statuses for customers.</p>

        <?php if($orders->num_rows === 0): ?>
            <p>No orders found in the database yet.</p>
        <?php else: ?>
            <?php while($order = $orders->fetch_assoc()): ?>
                <div class="order-card status-<?php echo strtolower($order['status']); ?>">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0;">Order <?php echo htmlspecialchars($order['order_number']); ?></h3>
                            <small style="color: #666;">Placed on: <?php echo date('M d, Y', strtotime($order['created_at'])); ?> | Total: $<?php echo number_format($order['total_amount'], 2); ?></small>
                        </div>
                        
                        <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" style="padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;">
                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="printing" <?php echo $order['status'] == 'printing' ? 'selected' : ''; ?>>Printing</option>
                                <option value="ready" <?php echo $order['status'] == 'ready' ? 'selected' : ''; ?>>Ready for Pickup</option>
                            </select>
                            <button type="submit" name="update_status" style="padding: 0.5rem 1rem; background: var(--dark); color: white; border: none; border-radius: 4px; cursor: pointer;">Update</button>
                        </form>
                    </div>

                    <?php
                    $order_id = $order['id'];
                    $items = $conn->query("SELECT * FROM order_items WHERE order_id = $order_id");
                    ?>
                    <table>
                        <tr>
                            <th>Qty</th>
                            <th>Design Specifications (From 3D Studio)</th>
                            <th>Price</th>
                        </tr>
                        <?php while($item = $items->fetch_assoc()): 
                            // Decode the JSON design data we saved during checkout!
                            $design_data = json_decode($item['print_file'], true);
                        ?>
                            <tr>
                                <td><?php echo $item['quantity']; ?>x</td>
                                <td>
                                    <strong><?php echo htmlspecialchars($design_data['name']); ?></strong><br>
                                    <div class="print-specs">
                                        <?php 
                                        // This translates the JSON into human-readable instructions!
                                        if (isset($design_data['color'])) {
                                            echo "Base Color Hex: " . htmlspecialchars($design_data['color']) . "<br>";
                                        }
                                        if (isset($design_data['textLayers']) && count($design_data['textLayers']) > 0) {
                                            echo "Text to Print: ";
                                            foreach ($design_data['textLayers'] as $layer) {
                                                echo "'" . htmlspecialchars($layer['content']) . "' (Color: " . $layer['color'] . ")<br>";
                                            }
                                        } else {
                                            echo "No custom text added.";
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

</body>
</html>