<?php
session_start();
// Security Check 1: Must be logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
// Security Check 2: MUST be the 'admin' (Shop Owner). Kick out staff!
if ($_SESSION['admin_role'] !== 'admin') {
    die("<h2 style='color:red; text-align:center; margin-top:50px; font-family: sans-serif;'>Access Denied. Only the Shop Owner can view this page.</h2><a href='index.php' style='display:block; text-align:center; font-family: sans-serif;'>Go Back</a>");
}

require_once '../db.php';

$message = '';

// Handle Password Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $new_password = $_POST['new_password'];
    $admin_id = $_SESSION['admin_id'];
    
    if(strlen($new_password) < 6) {
        $message = "<div style='color: #721c24; background: #f8d7da; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'>Password must be at least 6 characters.</div>";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed, $admin_id);
        if($stmt->execute()) {
            $message = "<div style='color: #155724; background: #d4edda; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'>Password successfully updated!</div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Triangle Admin</title>
    <style>
        :root { --primary: #E31E24; --dark: #333; --light: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light); display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--dark); color: white; padding: 2rem 0; display: flex; flex-direction: column; flex-shrink: 0;}
        .sidebar h2 { text-align: center; color: var(--primary); margin-bottom: 2rem; font-size: 1.5rem; }
        .sidebar a { color: white; text-decoration: none; padding: 1rem 2rem; display: block; border-left: 4px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); border-left-color: var(--primary); }
        .content { flex: 1; padding: 2rem; overflow-y: auto; }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); max-width: 600px;}
        input { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 1rem; box-sizing: border-box; }
        button { background: var(--dark); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #555; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Triangle Admin</h2>
        <a href="index.php">Dashboard</a>
        <a href="orders.php">Manage Orders</a>
        <a href="customers.php">Customers</a>
        <a href="settings.php" class="active">Settings (Owner)</a>
        <a href="logout.php" style="margin-top: auto; background-color: #c82333;">Logout</a>
    </div>

    <div class="content">
        <h1 style="margin-top: 0;">Owner Settings</h1>
        <p>Manage administrative settings and security.</p>
        
        <div class="card">
            <h3 style="margin-top: 0;">Update Admin Password</h3>
            <?php echo $message; ?>
            <form method="POST">
                <label style="display:block; margin-bottom: 0.5rem; font-weight: bold;">New Password</label>
                <input type="password" name="new_password" required placeholder="Enter new password...">
                <button type="submit" name="update_password">Save New Password</button>
            </form>
        </div>
    </div>
</body>
</html>