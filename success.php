<?php
session_start();
require_once 'db.php';

$page_title = 'Payment Successful';
$order_saved = false;
$display_order_number = '';
$db_error = ''; // The new Error Catcher

if (isset($_SESSION['pending_order']) && isset($_SESSION['user_id'])) {
    $order = $_SESSION['pending_order'];
    $user_id = $_SESSION['user_id'];
    $total = $order['total'];
    
    $order_number = 'ORD-' . strtoupper(substr(uniqid(), -6));
    $display_order_number = $order_number;

    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_number, total_amount, status) VALUES (?, ?, ?, 'pending')");
    
    if (!$stmt) {
        $db_error = "Orders Prepare Error: " . $conn->error;
    } else {
        $stmt->bind_param("isd", $user_id, $order_number, $total);
        
        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;
            $stmt->close();

            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, quantity, unit_price, print_file) VALUES (?, ?, ?, ?)");
            
            if (!$item_stmt) {
                $db_error = "Items Prepare Error: " . $conn->error;
            } else {
                $items_successful = true;
                foreach ($order['items'] as $item) {
                    $qty = $item->quantity;
                    $price = $item->price;
                    $custom_data = json_encode($item); 

                    $item_stmt->bind_param("iids", $order_id, $qty, $price, $custom_data);
                    if (!$item_stmt->execute()) {
                        $items_successful = false;
                        $db_error = "Items Execute Error: " . $item_stmt->error;
                    }
                }
                $item_stmt->close();
                
                if ($items_successful) {
                    $order_saved = true;
                    unset($_SESSION['pending_order']);
                }
            }
        } else {
            $db_error = "Orders Execute Error: " . $stmt->error;
        }
    }
} else {
    $db_error = "Session Error: Could not find user_id or pending_order in memory.";
}

include 'includes/header.php';
?>

<section style="padding: 5rem 2rem; text-align: center; min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    <div style="background-color: #d4edda; color: #155724; padding: 2.5rem; border-radius: 1rem; max-width: 550px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h1 style="font-size: 3.5rem; margin-bottom: 1rem;">✅</h1>
        <h2 style="margin-bottom: 0.5rem;">Payment Successful!</h2>
        
        <?php if ($order_saved): ?>
            <p style="font-weight: bold; font-size: 1.2rem; margin-bottom: 1rem;">Order #: <?php echo $display_order_number; ?></p>
            <p style="margin-bottom: 1.5rem;">Your custom design has been securely sent to our printing queue.</p>
        <?php else: ?>
            <div style="color: #721c24; background-color: #f8d7da; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #f5c6cb;">
                <p style="font-weight: bold; margin-bottom: 0.5rem;">Database Save Failed!</p>
                <p style="font-family: monospace; font-size: 0.9rem; word-break: break-all;">
                    <?php echo $db_error ? $db_error : "Unknown error occurred."; ?>
                </p>
            </div>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: bold; margin-right: 0.5rem;">View Order History</a>
        <a href="index.php" class="btn btn-secondary" style="padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: bold;">Return to Home</a>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof app !== 'undefined' && typeof app.clearCart === 'function') {
            app.clearCart();
        }
    });
</script>

<?php include 'includes/footer.php'; ?>