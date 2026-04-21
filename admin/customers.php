<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
require_once '../db.php';

// Fetch all regular customers from the database
$customers = $conn->query("SELECT id, first_name, last_name, email, created_at FROM users WHERE role = 'customer' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers - Triangle Admin</title>
    <style>
        :root { --primary: #E31E24; --dark: #333; --light: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light); display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--dark); color: white; padding: 2rem 0; display: flex; flex-direction: column; flex-shrink: 0;}
        .sidebar h2 { text-align: center; color: var(--primary); margin-bottom: 2rem; font-size: 1.5rem; }
        .sidebar a { color: white; text-decoration: none; padding: 1rem 2rem; display: block; border-left: 4px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); border-left-color: var(--primary); }
        .content { flex: 1; padding: 2rem; overflow-y: auto; }
        .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f9f9f9; color: #555; }
        tr:hover { background-color: #f1f4f6; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Triangle Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="orders.php">Manage Orders</a>
        <a href="customers.php" class="active">Customers</a>
        <?php if($_SESSION['admin_role'] === 'admin'): ?>
            <a href="settings.php">Settings (Owner)</a>
        <?php endif; ?>
        <a href="logout.php" style="margin-top: auto; background-color: #c82333;">Logout</a>
    </div>

    <div class="content">
        <h1 style="margin-top: 0;">Customer Management</h1>
        <p>View all registered customers in your store.</p>
        
        <div class="card">
            <?php if($customers->num_rows === 0): ?>
                <p>No customers registered yet.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered Date</th>
                    </tr>
                    <?php while($customer = $customers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>