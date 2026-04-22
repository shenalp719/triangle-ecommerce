<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Security: Make sure they are actually staff or admin
if ($_SESSION['admin_role'] !== 'staff' && $_SESSION['admin_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once '../db.php';

// Get actionable metrics for the print team
$pending_count = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")->fetch_assoc()['count'];
$printing_count = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'printing'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Triangle Printing</title>
    <style>
        :root { --primary: #E31E24; --dark: #333; --light: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light); display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--dark); color: white; padding: 2rem 0; display: flex; flex-direction: column; flex-shrink: 0;}
        .sidebar h2 { text-align: center; color: var(--primary); margin-bottom: 2rem; font-size: 1.5rem; }
        .sidebar a { color: white; text-decoration: none; padding: 1rem 2rem; display: block; border-left: 4px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); border-left-color: var(--primary); }
        .content { flex: 1; padding: 2rem; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; background: white; padding: 1.5rem 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid var(--primary); }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 1rem; }
        .stat-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .btn-primary { background: var(--dark); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-weight: bold; display: inline-block; transition: 0.2s; }
        .btn-primary:hover { background: var(--primary); }
        ul.task-list { line-height: 2; font-size: 1.1rem; color: #555; }
        ul.task-list li::marker { color: var(--primary); font-size: 1.2em; }
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
            <div>
                <h1 style="margin: 0; font-size: 1.8rem;">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>! 👋</h1>
                <p style="margin: 0.5rem 0 0 0; color: #666;">Here is your production overview for today.</p>
            </div>
            <span style="background: #e1e1e1; color: var(--dark); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: bold;">
                Production Team
            </span>
        </div>

        <div class="stat-grid">
            <div class="stat-card" style="border-top: 4px solid #f39c12;">
                <div class="stat-icon" style="background: #fdf2e9; color: #f39c12;">⏳</div>
                <div>
                    <h3 style="margin: 0; color: #666;">New Orders</h3>
                    <p style="margin: 0; font-size: 1.5rem; font-weight: bold;"><?php echo $pending_count; ?> Pending</p>
                </div>
            </div>
            <div class="stat-card" style="border-top: 4px solid #3498db;">
                <div class="stat-icon" style="background: #ebf5fb; color: #3498db;">🖨️</div>
                <div>
                    <h3 style="margin: 0; color: #666;">In Production</h3>
                    <p style="margin: 0; font-size: 1.5rem; font-weight: bold;"><?php echo $printing_count; ?> Printing</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-top: 0; color: var(--dark);">📋 Your Daily Workflow</h2>
            <ul class="task-list">
                <li>Check the <strong>Manage Orders</strong> tab for newly placed customer designs.</li>
                <li>Download the <strong>JSON Production Assets</strong> to load into the printing software.</li>
                <li>Update order statuses to <em>"Printing"</em> once the physical production begins.</li>
                <li>Update order statuses to <em>"Ready for Pickup"</em> when the item is finished to notify the customer.</li>
            </ul>
            <br>
            <a href="orders.php" class="btn-primary">Go to Orders Queue &rarr;</a>
        </div>
    </div>

</body>
</html>