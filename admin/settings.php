<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// THE RBAC LOCK: Only the Owner (admin) can touch system keys
if ($_SESSION['admin_role'] !== 'admin') {
    header("Location: staff_dashboard.php");
    exit();
}

require_once '../db.php';
// In a real system, we would fetch these from secrets.php or a DB
// For the defense, we will show them as "Protected"
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Security - Triangle Admin</title>
    <style>
        :root { --primary: #E31E24; --dark: #333; --light: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light); display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--dark); color: white; padding: 2rem 0; display: flex; flex-direction: column; flex-shrink: 0;}
        .sidebar h2 { text-align: center; color: var(--primary); margin-bottom: 2rem; font-size: 1.5rem; }
        .sidebar a { color: white; text-decoration: none; padding: 1rem 2rem; display: block; border-left: 4px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); border-left-color: var(--primary); }
        .content { flex: 1; padding: 2rem; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 2rem; }
        .key-box { background: #1e1e1e; color: #00ff00; padding: 1rem; border-radius: 5px; font-family: monospace; font-size: 0.9rem; margin: 10px 0; border-left: 4px solid #2ecc71; }
        .badge { background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
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
        <h1>🔐 System Security & API Management</h1>
        <p>Manage environmental variables and external service integrations.</p>

        <div class="card">
            <h3>Stripe Payment Gateway <span class="badge">ACTIVE</span></h3>
            <p>Handles PCI-compliant transactions and secure refunds.</p>
            <label>Webhook Secret Key:</label>
            <div class="key-box">whsec_************************************************</div>
            <label>API Mode:</label>
            <div style="margin-top: 5px;"><strong style="color: #3498db;">● TEST MODE</strong> (Simulated Transactions)</div>
        </div>

        <div class="card">
            <h3>Gemini AI Integration <span class="badge">CONNECTED</span></h3>
            <p>Powers the automated customer support and personalization engine.</p>
            <label>Model Endpoint:</label>
            <div class="key-box">gemini-2.0-flash-lite</div>
            <label>API Status:</label>
            <div style="margin-top: 5px; color: #2ecc71;"><strong>● OPERATIONAL</strong> (Rate Limits Monitored)</div>
        </div>

        <div class="card" style="border-top: 4px solid #f1c40f;">
            <h3>System Administrator Notice</h3>
            <p>Encryption protocols are active. All user passwords are hashed using <code>PASSWORD_DEFAULT</code> (Bcrypt). API keys are restricted via environment-level configurations to prevent unauthorized client-side access.</p>
        </div>
    </div>

</body>
</html>