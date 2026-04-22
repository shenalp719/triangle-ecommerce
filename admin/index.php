<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// THE RBAC LOCK: Bounce staff members directly to their custom dashboard
if ($_SESSION['admin_role'] === 'staff') {
    header("Location: staff_dashboard.php");
    exit();
}

require_once '../db.php';

// Analytics & Reporting (Per functionality requirements)
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch_assoc()['count'];

// Calculate Financials
$financials = $conn->query("SELECT SUM(total_amount) as gross FROM orders")->fetch_assoc();
$gross_revenue = $financials['gross'] ? $financials['gross'] : 0;

// Standard Stripe Fee Calculation (2.9% + $0.30 per successful order)
$stripe_fees = ($gross_revenue * 0.029) + ($total_orders * 0.30);
$net_revenue = $gross_revenue - $stripe_fees;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Triangle Printing</title>
    <style>
        :root { --primary: #E31E24; --dark: #333; --light: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light); display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--dark); color: white; padding: 2rem 0; display: flex; flex-direction: column; }
        .sidebar h2 { text-align: center; color: var(--primary); margin-bottom: 2rem; font-size: 1.5rem; }
        .sidebar a { color: white; text-decoration: none; padding: 1rem 2rem; display: block; border-left: 4px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); border-left-color: var(--primary); }
        .sidebar .logout { margin-top: auto; background-color: #c82333; }
        .content { flex: 1; padding: 2rem; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; background: white; padding: 1rem 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-top: 4px solid var(--primary); }
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
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h1>
            <span style="background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: bold;">
                Role: <?php echo strtoupper($_SESSION['admin_role']); ?>
            </span>
        </div>

        <h2 style="margin-top: 0;">Sales & Analytics Dashboard</h2>
        <div class="stat-grid">
            <div class="stat-card">
                <h3 style="margin: 0 0 0.5rem 0; color: #666;">Total Orders</h3>
                <p style="margin: 0; font-size: 2rem; font-weight: bold;"><?php echo $total_orders; ?></p>
            </div>
            <div class="stat-card" style="border-top-color: #2ecc71;">
                <h3 style="margin: 0 0 0.5rem 0; color: #666;">Gross Revenue</h3>
                <p style="margin: 0; font-size: 2rem; font-weight: bold; color: #2ecc71;">$<?php echo number_format($gross_revenue, 2); ?></p>
            </div>
            <div class="stat-card" style="border-top-color: #f39c12;">
                <h3 style="margin: 0 0 0.5rem 0; color: #666;">Stripe Fees (Estimated)</h3>
                <p style="margin: 0; font-size: 1.5rem; font-weight: bold; color: #f39c12;">-$<?php echo number_format($stripe_fees, 2); ?></p>
            </div>
            <div class="stat-card" style="border-top-color: #3498db; background-color: #ebf5fb;">
                <h3 style="margin: 0 0 0.5rem 0; color: #3498db;">Net Revenue</h3>
                <p style="margin: 0; font-size: 2rem; font-weight: bold; color: #2980b9;">$<?php echo number_format($net_revenue, 2); ?></p>
            </div>
        </div>
    </div>

</body>
</html>