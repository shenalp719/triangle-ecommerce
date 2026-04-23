<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch order
$orderResult = $conn->query("SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id");

if (!$orderResult) {
    die("Database Error in Orders: " . $conn->error); 
}

$order = $orderResult->fetch_assoc();

if (!$order) {
    header('Location: dashboard.php');
    exit();
}

// FIXED: Removed the JOIN. We just grab exactly what is saved in the order_items table!
$itemsResult = $conn->query("SELECT * FROM order_items WHERE order_id = $order_id");

if (!$itemsResult) {
    die("Database Error in Items: " . $conn->error);
}

$page_title = 'Order Details #' . ($order['order_number'] ?? $order['id']);
include 'includes/header.php';
?>

<section class="container-md" style="padding: 4rem 2rem;">
    <div style="margin-bottom: 2rem;">
        <a href="dashboard.php" style="text-decoration: none; color: var(--primary-red); font-weight: bold;">← Back to Dashboard</a>
        <h1 style="margin-top: 1rem;">Order Details</h1>
        <p style="color: var(--text-light);">Order ID: <strong>#<?php echo htmlspecialchars($order['order_number'] ?? $order['id']); ?></strong></p>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div>
            <div class="card" style="background: white; padding: 1.5rem; border-radius: 8px; border: 1px solid #eee;">
                <h4 style="margin-bottom: 1.5rem;">Items in this Order</h4>
                
                <?php if ($itemsResult && $itemsResult->num_rows > 0): ?>
                    <?php while($item = $itemsResult->fetch_assoc()): ?>
                        <div style="display: flex; gap: 1.5rem; padding: 1rem 0; border-bottom: 1px solid #eee; align-items: center;">
                            <div style="width: 80px; height: 80px; background-color: #f5f5f5; border-radius: 8px; overflow: hidden;">
                                <?php 
                                    // FIXED: Safely load the real image or a standard placeholder
                                    $imgSrc = !empty($item['image']) ? $item['image'] : (!empty($item['image_url']) ? $item['image_url'] : 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?q=80&w=200');
                                ?>
                                <img src="<?php echo htmlspecialchars($imgSrc); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="flex: 1;">
                                <h5 style="margin: 0;"><?php echo htmlspecialchars($item['product_name'] ?? $item['name'] ?? 'Custom Product'); ?></h5>
                                <p style="margin: 0.25rem 0; color: var(--text-light);">Quantity: <?php echo $item['quantity']; ?></p>
                            </div>
                            <div style="font-weight: 700; color: var(--primary-red);">$<?php echo number_format(($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity'], 2); ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: var(--text-light);">No item details found for this order.</p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <div class="card" style="background-color: #f9f9f9; padding: 1.5rem; border-radius: 8px; border: 1px solid #eee;">
                <h4 style="margin-bottom: 1rem;">Order Summary</h4>
                <div style="margin-bottom: 1rem;">
                    <small style="color: var(--text-light);">Current Status</small>
                    <div style="font-weight: bold; color: var(--primary-red); text-transform: uppercase;">
                        <?php echo htmlspecialchars($order['status']); ?>
                    </div>
                </div>
                
                <hr style="margin: 1.5rem 0; border: 0; border-top: 1px solid #ddd;">
                
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Total Amount:</span>
                    <strong style="font-size: 1.5rem; color: var(--primary-red);">$<?php echo number_format($order['total_amount'], 2); ?></strong>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>