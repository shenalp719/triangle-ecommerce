<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
require_once '../db.php';

$message = '';

// Handle Status Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status']; 
    $update_stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $update_stmt->bind_param("si", $new_status, $order_id);
    if($update_stmt->execute()) {
        $message = "<div style='background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'>Order #$order_id status updated to " . ucfirst($new_status) . "!</div>";
    }
}

// Handle Stripe Refund Simulation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_refund'])) {
    $order_id = intval($_POST['order_id']);
    $stripe_id = $_POST['stripe_id'];
    
    $update_stmt = $conn->prepare("UPDATE orders SET status = 'refunded' WHERE id = ?");
    $update_stmt->bind_param("i", $order_id);
    if($update_stmt->execute()) {
        $message = "<div style='background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'>Refund successfully processed for Stripe ID: " . htmlspecialchars($stripe_id) . "</div>";
    }
}

// --- TABBED VIEW LOGIC ---
$view = isset($_GET['view']) ? $_GET['view'] : 'active';

if ($view === 'history') {
    // Show only finished business
    $orders = $conn->query("SELECT * FROM orders WHERE LOWER(status) IN ('completed', 'refunded') ORDER BY created_at DESC");
} else {
    // Show active production queue
    $orders = $conn->query("SELECT * FROM orders WHERE LOWER(status) NOT IN ('completed', 'refunded') ORDER BY created_at ASC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Triangle Admin</title>
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
        .status-ready { border-left-color: #9b59b6; }
        .status-completed { border-left-color: #2ecc71; opacity: 0.9; }
        .status-refunded { border-left-color: #e74c3c; opacity: 0.7; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f9f9f9; }
        .print-specs { background: #f8f9fa; padding: 1rem; border-radius: 5px; font-family: monospace; font-size: 0.9rem; color: #d63031; }
        
        /* Tab Styles */
        .tab-btn { padding: 0.5rem 1.5rem; text-decoration: none; border-radius: 4px; border: 2px solid var(--dark); font-weight: bold; transition: 0.2s; }
        .tab-active { background: var(--dark); color: white; }
        .tab-inactive { background: transparent; color: var(--dark); }
        .tab-inactive:hover { background: rgba(0,0,0,0.05); }
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
        <h1 style="margin-top: 0;">Order Processing & Reconciliation</h1>
        
        <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
            <a href="orders.php?view=active" class="tab-btn <?php echo $view === 'active' ? 'tab-active' : 'tab-inactive'; ?>">Active Production Queue</a>
            <a href="orders.php?view=history" class="tab-btn <?php echo $view === 'history' ? 'tab-active' : 'tab-inactive'; ?>">Order History & Archives</a>
        </div>

        <?php echo $message; ?>

        <?php if($orders->num_rows === 0): ?>
            <p style="padding: 2rem; background: white; border-radius: 8px; text-align: center; color: #666;">No orders found in this view.</p>
        <?php else: ?>
            <?php while($order = $orders->fetch_assoc()): 
                // Fix casing bugs by forcing lowercase for our checks
                $status_clean = strtolower($order['status']); 
            ?>
                <div class="order-card status-<?php echo $status_clean; ?>">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h3 style="margin: 0;">Order <?php echo htmlspecialchars($order['order_number']); ?></h3>
                            <small style="color: #666;">Placed on: <?php echo date('M d, Y', strtotime($order['created_at'])); ?> | Total: $<?php echo number_format($order['total_amount'], 2); ?></small>
                            <br>
                            <?php if(!empty($order['stripe_payment_id'])): ?>
                                <span style="background: #e1e1e1; font-family: monospace; font-size: 0.8rem; padding: 2px 6px; border-radius: 4px; display: inline-block; margin-top: 5px;">
                                    Stripe ID: <?php echo htmlspecialchars($order['stripe_payment_id']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div style="display: flex; gap: 1rem;">
                            <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status" style="padding: 0.5rem; border-radius: 4px; border: 1px solid #ccc;" <?php echo ($status_clean == 'refunded') ? 'disabled' : ''; ?>>
                                    <option value="pending" <?php echo $status_clean == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="printing" <?php echo $status_clean == 'printing' ? 'selected' : ''; ?>>Printing</option>
                                    <option value="ready" <?php echo $status_clean == 'ready' ? 'selected' : ''; ?>>Ready for Pickup</option>
                                    <option value="completed" <?php echo $status_clean == 'completed' ? 'selected' : ''; ?>>Completed / Delivered</option>
                                    <?php if($status_clean == 'refunded'): ?>
                                        <option value="refunded" selected>Refunded</option>
                                    <?php endif; ?>
                                </select>
                                
                                <?php if($status_clean != 'refunded'): ?>
                                    <button type="submit" name="update_status" style="padding: 0.5rem 1rem; background: var(--dark); color: white; border: none; border-radius: 4px; cursor: pointer;">Update</button>
                                <?php endif; ?>
                            </form>

                            <?php 
                            // THE RBAC LOCK: Only Admins can process Stripe Refunds
                            if($status_clean != 'refunded' && !empty($order['stripe_payment_id']) && $_SESSION['admin_role'] === 'admin'): 
                            ?>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to issue a full refund for this order?');">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <input type="hidden" name="stripe_id" value="<?php echo $order['stripe_payment_id']; ?>">
                                    <button type="submit" name="process_refund" style="padding: 0.5rem 1rem; background: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Refund</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                    $order_id = $order['id'];
                    $items = $conn->query("SELECT * FROM order_items WHERE order_id = $order_id");
                    ?>
                    <table>
                        <tr>
                            <th>Qty</th>
                            <th>Design Specifications</th>
                            <th>Price</th>
                        </tr>
                        <?php while($item = $items->fetch_assoc()): 
                            $design_data = json_decode($item['print_file'], true);
                        ?>
                            <tr>
                                <td><?php echo $item['quantity']; ?>x</td>
                                <td>
                                    <strong><?php echo isset($design_data['name']) ? htmlspecialchars($design_data['name']) : 'Custom Product'; ?></strong><br>
                                    <div class="print-specs">
                                        <?php 
                                        if (isset($design_data['color'])) echo "Base Color: <b>" . htmlspecialchars($design_data['color']) . "</b><br>";
                                        if (isset($design_data['textLayers']) && count($design_data['textLayers']) > 0) {
                                            echo "Text: ";
                                            foreach ($design_data['textLayers'] as $layer) {
                                                echo "'" . htmlspecialchars($layer['content']) . "' ";
                                            }
                                            echo "<br>";
                                        }
                                        ?>
                                    </div>
                                    
                                    <?php 
                                    if(!empty($item['print_file'])): 
                                        $base64_json = base64_encode($item['print_file']);
                                        $filename = "Order_" . $order['order_number'] . "_Item_" . $item['id'] . "_Assets.json";
                                    ?>
                                        <a href="data:application/json;base64,<?php echo $base64_json; ?>" 
                                           download="<?php echo $filename; ?>" 
                                           style="display: inline-block; margin-top: 8px; padding: 4px 8px; background: #0984e3; color: white; text-decoration: none; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">
                                           📥 Download Production Assets
                                        </a>
                                    <?php endif; ?>
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