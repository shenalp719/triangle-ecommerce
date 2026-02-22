<?php
/**
 * Admin Panel - Triangle Printing Solutions
 */
session_start();
require_once '../db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /triangle-ecommerce/login.php');
    exit();
}

// Get statistics
$statsQueries = [
    'total_orders' => "SELECT COUNT(*) as count FROM orders",
    'pending_orders' => "SELECT COUNT(*) as count FROM orders WHERE status IN ('pending', 'processing')",
    'revenue' => "SELECT SUM(total_amount) as total FROM orders WHERE status IN ('shipped', 'delivered')",
    'customers' => "SELECT COUNT(*) as count FROM users WHERE role = 'customer'",
    'designs' => "SELECT COUNT(*) as count FROM designs"
];

$stats = [];
foreach ($statsQueries as $key => $query) {
    $result = executeQuery($query);
    if ($result) {
        $stats[$key] = $result->fetch_assoc();
    }
}

// Get recent orders
$recentOrders = executeQuery("SELECT o.*, u.email, u.first_name FROM orders o 
                            JOIN users u ON o.user_id = u.id 
                            ORDER BY o.created_at DESC LIMIT 10");
$orders = $recentOrders ? $recentOrders->fetch_all(MYSQLI_ASSOC) : [];

$page_title = 'Admin Panel';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Triangle Printing Solutions</title>
    <link rel="stylesheet" href="/triangle-ecommerce/assets/css/style.css">
    <link rel="stylesheet" href="/triangle-ecommerce/assets/css/responsive.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-container {
            display: flex;
            height: 100vh;
            background-color: var(--light-gray);
        }
        
        .admin-sidebar {
            width: 250px;
            background-color: var(--dark-gray);
            color: var(--white);
            padding: 2rem 0;
            overflow-y: auto;
        }
        
        .admin-sidebar-item {
            padding: 1rem 1.5rem;
            border-left: 4px solid transparent;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .admin-sidebar-item:hover,
        .admin-sidebar-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--primary-red);
        }
        
        .admin-sidebar a {
            color: var(--white);
            text-decoration: none;
            display: block;
        }
        
        .admin-content {
            flex: 1;
            overflow-y: auto;
        }
        
        .admin-header {
            background-color: var(--white);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-main {
            padding: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background-color: var(--white);
            padding: 1.5rem;
            border-radius: 0.75rem;
            border-left: 4px solid var(--primary-red);
            box-shadow: var(--shadow-light);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-red);
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .admin-table {
            background-color: var(--white);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }
        
        .admin-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .admin-table th {
            background-color: var(--light-gray);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }
        
        .admin-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        
        .admin-table tr:hover {
            background-color: rgba(227, 30, 36, 0.03);
        }
        
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .admin-table {
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .admin-sidebar {
                width: 100%;
                height: auto;
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                padding-top: 4rem;
                z-index: 99;
            }
            
            .admin-sidebar.active {
                display: block;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="admin-sidebar">
            <div style="padding: 0 1.5rem; margin-bottom: 2rem;">
                <h3 style="color: var(--primary-red); margin-bottom: 0.5rem;">△ ADMIN</h3>
                <small style="color: rgba(255, 255, 255, 0.7);">Control Panel</small>
            </div>

            <a href="/triangle-ecommerce/admin/" class="admin-sidebar-item active">Dashboard</a>
            <a href="/triangle-ecommerce/admin/orders.php" class="admin-sidebar-item">Orders</a>
            <a href="/triangle-ecommerce/admin/customers.php" class="admin-sidebar-item">Customers</a>
            <a href="/triangle-ecommerce/admin/products.php" class="admin-sidebar-item">Products</a>
            <a href="/triangle-ecommerce/admin/designs.php" class="admin-sidebar-item">Designs</a>
            <a href="/triangle-ecommerce/admin/settings.php" class="admin-sidebar-item">Settings</a>
            
            <hr style="border: none; border-top: 1px solid rgba(255, 255, 255, 0.1); margin: 2rem 0;">
            
            <a href="/triangle-ecommerce/" class="admin-sidebar-item">Back to Store</a>
            <a href="/triangle-ecommerce/logout.php" class="admin-sidebar-item" style="color: #E74C3C;">Logout</a>
        </aside>

        <!-- Main Content -->
        <div class="admin-content">
            <!-- Header -->
            <div class="admin-header">
                <div>
                    <h2>Dashboard</h2>
                    <small style="color: var(--text-light);">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></small>
                </div>
                <button style="background: none; border: none; font-size: 1.5rem; cursor: pointer; display: none;" id="mobile-toggle">☰</button>
            </div>

            <!-- Main Content Area -->
            <div class="admin-main">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $stats['total_orders']['count'] ?? 0; ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-value" style="color: #F39C12;"><?php echo $stats['pending_orders']['count'] ?? 0; ?></div>
                        <div class="stat-label">Pending Orders</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-value" style="color: #27AE60;">$<?php echo number_format($stats['revenue']['total'] ?? 0, 0); ?></div>
                        <div class="stat-label">Revenue</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-value"><?php echo $stats['customers']['count'] ?? 0; ?></div>
                        <div class="stat-label">Customers</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-value"><?php echo $stats['designs']['count'] ?? 0; ?></div>
                        <div class="stat-label">Designs</div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <h3 style="margin-bottom: 1.5rem;">Recent Orders</h3>
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
                                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
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
                                        ?>; color: white; padding: 0.25rem 0.75rem; border-radius: 0.25rem; font-size: 0.75rem;">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td>
                                        <a href="orders.php?id=<?php echo $order['id']; ?>" style="color: var(--primary-red); font-weight: 600; text-decoration: none;">View →</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('mobile-toggle')?.addEventListener('click', function() {
            document.getElementById('admin-sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>
<?php
?>
