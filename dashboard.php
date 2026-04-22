<?php
/**
 * User Dashboard - Triangle Printing Solutions
 */
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /triangle-ecommerce/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$userResult = executeQuery("SELECT * FROM users WHERE id = $user_id");
$user = $userResult->fetch_assoc();

// Get user's orders
$ordersResult = executeQuery("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
$orders = $ordersResult ? $ordersResult->fetch_all(MYSQLI_ASSOC) : [];

// Get user's saved designs
$designsResult = executeQuery("SELECT * FROM designs WHERE user_id = $user_id ORDER BY created_at DESC");
$designs = $designsResult ? $designsResult->fetch_all(MYSQLI_ASSOC) : [];

// Fetch the user's order history
$user_id = $_SESSION['user_id'];
$orders_query = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");

$page_title = 'My Dashboard';
include 'includes/header.php';
?>

    <section style="background-color: var(--light-gray); padding: 2rem; margin-bottom: 3rem;">
        <div class="container-md">
            <h1>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>! 👋</h1>
            <p style="color: var(--text-light);">Manage your account, orders, and designs</p>
        </div>
    </section>

    <section class="container-md">
        <!-- Stats Cards -->
        <div class="grid grid-4" style="margin-bottom: 3rem;">
            <div class="card" style="text-align: center;">
                <div class="card-body">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-red); margin-bottom: 0.5rem;">
                        <?php echo count($orders); ?>
                    </div>
                    <p style="margin: 0;">Orders</p>
                </div>
            </div>

            <div class="card" style="text-align: center;">
                <div class="card-body">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-red); margin-bottom: 0.5rem;">
                        <?php echo count($designs); ?>
                    </div>
                    <p style="margin: 0;">Saved Designs</p>
                </div>
            </div>

            <div class="card" style="text-align: center;">
                <div class="card-body">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-red); margin-bottom: 0.5rem;">
                        $<?php 
                            $totalSpent = 0;
                            foreach ($orders as $order) {
                                if (in_array($order['status'], ['shipped', 'delivered'])) {
                                    $totalSpent += $order['total_amount'];
                                }
                            }
                            echo number_format($totalSpent, 2);
                        ?>
                    </div>
                    <p style="margin: 0;">Spent</p>
                </div>
            </div>

            <div class="card" style="text-align: center;">
                <div class="card-body">
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-red); margin-bottom: 0.5rem;">
                        <?php 
                            $delivered = count(array_filter($orders, fn($o) => $o['status'] === 'delivered'));
                            echo $delivered;
                        ?>
                    </div>
                    <p style="margin: 0;">Delivered</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div style="margin-bottom: 3rem;">
            <h3 style="margin-bottom: 1rem;">Quick Actions</h3>
            <div class="grid grid-3">
                <a href="customizer-frame.php" class="card" style="text-decoration: none;">
                    <div class="card-body" style="text-align: center;">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem;">🎨</div>
                        <h5>Start Customizing</h5>
                        <p style="font-size: 0.9rem;">Create a new design</p>
                    </div>
                </a>

                <a href="products.php" class="card" style="text-decoration: none;">
                    <div class="card-body" style="text-align: center;">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem;">📦</div>
                        <h5>Browse Products</h5>
                        <p style="font-size: 0.9rem;">View all products</p>
                    </div>
                </a>

                <a href="#account" class="card" style="text-decoration: none;">
                    <div class="card-body" style="text-align: center;">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem;">⚙️</div>
                        <h5>Account Settings</h5>
                        <p style="font-size: 0.9rem;">Update profile</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Orders -->
        <div style="margin-bottom: 3rem;">
            <h3 style="margin-bottom: 1rem;">Recent Orders</h3>
            <?php if (count($orders) > 0): ?>
                <div class="grid" style="gap: 1rem;">
                    <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                        <div class="card">
                            <div class="card-body" style="display: grid; grid-template-columns: auto 1fr auto; gap: 1.5rem; align-items: center;">
                                <div style="font-size: 2rem;">📦</div>
                                <div>
                                    <h5 style="margin-bottom: 0.25rem;">Order #<?php echo substr($order['order_number'], -8); ?></h5>
                                    <small style="color: var(--text-light);">
                                        Placed on <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                    </small>
                                    <div style="margin-top: 0.5rem;">
                                        <span class="badge" style="background-color: <?php 
                                            echo match($order['status']) {
                                                'pending' => '#F39C12',
                                                'processing' => '#3498DB',
                                                'printing' => '#9B59B6',
                                                'prepared' => '#1ABC9C',
                                                'shipped' => '#16A085',
                                                'delivered' => '#27AE60',
                                                default => '#95A5A6'
                                            };
                                        ?>;">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary-red);">
                                        $<?php echo number_format($order['total_amount'], 2); ?>
                                    </div>
                                    <a href="#order-<?php echo $order['id']; ?>" style="font-size: 0.85rem;">View Details →</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    No orders yet. <a href="products.php">Start shopping!</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Saved Designs -->
        <div style="margin-bottom: 3rem;">
            <h3 style="margin-bottom: 1rem;">Saved Designs</h3>
            <?php if (count($designs) > 0): ?>
                <div class="grid grid-4">
                    <?php foreach (array_slice($designs, 0, 4) as $design): ?>
                        <div class="card">
                            <div style="height: 200px; background-color: var(--light-gray); display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--border-color); overflow: hidden;">
                                <?php if (!empty($design['preview_image'])): ?>
                                    <img src="data:image/png;base64,<?php echo base64_encode($design['preview_image']); ?>" alt="Design" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <span style="color: var(--text-light);">No Preview</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($design['name']); ?></h5>
                                <small style="color: var(--text-light);">
                                    <?php echo date('M d, Y', strtotime($design['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($designs) > 4): ?>
                    <div style="text-align: center; margin-top: 1.5rem;">
                        <a href="#all-designs" class="btn btn-secondary">View All Designs</a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    No saved designs yet. <a href="products.php">Create your first design!</a>
                </div>
            <?php endif; ?>
        </div>
        <div style="margin-top: 3rem; margin-bottom: 3rem;">
            <h3 style="margin-bottom: 1rem;">My Order History</h3>
            
            <?php if ($orders_query && $orders_query->num_rows > 0): ?>
                <div class="card" style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                        <tr>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #eee; background-color: #f9f9f9; color: #555;">Order #</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #eee; background-color: #f9f9f9; color: #555;">Date</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #eee; background-color: #f9f9f9; color: #555;">Total</th>
                            <th style="padding: 1rem; text-align: left; border-bottom: 2px solid #eee; background-color: #f9f9f9; color: #555;">Live Status</th>
                        </tr>
                        <?php while($order = $orders_query->fetch_assoc()): ?>
                            <tr style="transition: background-color 0.2s;">
                                <td style="padding: 1rem; border-bottom: 1px solid #eee; font-family: monospace; font-size: 1.1rem;">
                                    <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                                </td>
                                <td style="padding: 1rem; border-bottom: 1px solid #eee;">
                                    <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                                </td>
                                <td style="padding: 1rem; border-bottom: 1px solid #eee; font-weight: bold;">
                                    $<?php echo number_format($order['total_amount'], 2); ?>
                                </td>
                                <td style="padding: 1rem; border-bottom: 1px solid #eee;">
                                    <?php 
                                        // Dynamic color coding based on the status the admin sets!
                                        $status = strtolower($order['status']);
                                        $bg_color = '#f1f4f6'; $text_color = '#555';
                                        if ($status == 'pending') { $bg_color = '#fdf3d8'; $text_color = '#f39c12'; }
                                        if ($status == 'printing') { $bg_color = '#d6eaf8'; $text_color = '#3498db'; }
                                        if ($status == 'ready') { $bg_color = '#d4edda'; $text_color = '#2ecc71'; }
                                    ?>
                                    <span style="background-color: <?php echo $bg_color; ?>; color: <?php echo $text_color; ?>; padding: 6px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </div>
            <?php else: ?>
                <div style="background-color: var(--light-gray); color: var(--text-light); padding: 2rem; border-radius: 8px; text-align: center; border: 1px dashed var(--border-color);">
                    <svg style="width: 48px; height: 48px; color: #ccc; margin-bottom: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <p style="margin: 0; font-size: 1.1rem;">You haven't placed any orders yet.</p>
                    <a href="index.php" style="color: var(--primary-red); text-decoration: none; font-weight: bold; display: inline-block; margin-top: 0.5rem;">Start customizing &rarr;</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Account Settings Section -->
        <div id="account" style="margin-bottom: 3rem;">
            <h3 style="margin-bottom: 1rem;">Account Settings</h3>
            <div class="grid grid-2">
                <div class="card">
                    <div class="card-header">
                        <h5>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo $user['phone'] ?: 'Not set'; ?></p>
                        <p><strong>Company:</strong> <?php echo $user['company'] ?: 'Not set'; ?></p>
                        <button class="btn btn-secondary btn-sm" onclick="alert('Edit profile coming soon!')">Edit Profile</button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5>Security</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Member Since:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                        <button class="btn btn-secondary btn-sm" onclick="alert('Change password coming soon!')">Change Password</button>
                        <button class="btn btn-danger btn-sm" style="background-color: #E74C3C; color: white; margin-top: 0.5rem;" onclick="if(confirm('Are you sure?')) window.location.href='logout.php'">Logout</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
