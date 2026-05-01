<?php
// Lightweight System Monitor
session_start();

// Security: Make sure they are logged in and are an ADMIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Helper function to format bytes into readable sizes
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Disk Space Calculations
$disk_total = @disk_total_space("/");
$disk_free = @disk_free_space("/");
$disk_used = $disk_total - $disk_free;
$disk_percent = $disk_total > 0 ? round(($disk_used / $disk_total) * 100, 2) : 0;

// Memory Usage
$memory_used = memory_get_usage(true);
$memory_peak = memory_get_peak_usage(true);

// CPU Load 
$load = function_exists('sys_getloadavg') ? sys_getloadavg() : ['N/A', 'N/A', 'N/A'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Monitor - Triangle Printing</title>
    <style>
        :root { --primary: #E31E24; --dark: #333; --light: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light); display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--dark); color: white; padding: 2rem 0; display: flex; flex-direction: column; flex-shrink: 0;}
        .sidebar h2 { text-align: center; color: var(--primary); margin-bottom: 2rem; font-size: 1.5rem; }
        .sidebar a { color: white; text-decoration: none; padding: 1rem 2rem; display: block; border-left: 4px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); border-left-color: var(--primary); }
        .content { flex: 1; padding: 2rem; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; background: white; padding: 1.5rem 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid var(--primary); }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .stat-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 1rem; }
        .progress-bar-bg { background: #e0e0e0; border-radius: 4px; height: 10px; width: 100%; margin-top: 10px; overflow: hidden; }
        .progress-bar-fill { background: var(--primary); height: 100%; border-radius: 4px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Triangle Admin</h2>
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard & Analytics</a>
        <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">Manage Orders</a>
        <a href="products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">Products (CRUD)</a>
        <a href="customers.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>">User Management</a>
        <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">System Security</a>
        <hr style="border-color: #555; margin: 10px 20px;">
        <a href="system_monitor.php" class="active">🖥️ System Monitor</a>
        <a href="database_backup.php">🗄️ Database Backup</a>
        <a href="logout.php" style="margin-top: auto; background-color: #c82333;">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <div>
                <h1 style="margin: 0; font-size: 1.8rem;">🖥️ Server Health Monitor</h1>
                <p style="margin: 0.5rem 0 0 0; color: #666;">Real-time performance metrics for the Triangle printing server.</p>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card" style="border-top: 4px solid #8e44ad;">
                <div class="stat-icon" style="background: #f4ecf7; color: #8e44ad;">⚙️</div>
                <h3 style="margin: 0 0 10px 0; color: var(--dark);">System Info</h3>
                <p style="margin: 5px 0; color: #555;"><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                <p style="margin: 5px 0; color: #555;"><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
            </div>

            <div class="stat-card" style="border-top: 4px solid #f39c12;">
                <div class="stat-icon" style="background: #fdf2e9; color: #f39c12;">💾</div>
                <h3 style="margin: 0 0 10px 0; color: var(--dark);">Storage Space</h3>
                <p style="margin: 5px 0; color: #555;"><strong>Total:</strong> <?php echo formatBytes($disk_total); ?></p>
                <p style="margin: 5px 0; color: #555;"><strong>Free:</strong> <?php echo formatBytes($disk_free); ?></p>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: <?php echo $disk_percent; ?>%; background: <?php echo $disk_percent > 80 ? '#E31E24' : '#27ae60'; ?>;"></div>
                </div>
                <p style="margin: 5px 0; font-size: 0.85rem; color: #777; text-align: right;"><?php echo $disk_percent; ?>% Used</p>
            </div>

            <div class="stat-card" style="border-top: 4px solid #3498db;">
                <div class="stat-icon" style="background: #ebf5fb; color: #3498db;">🧠</div>
                <h3 style="margin: 0 0 10px 0; color: var(--dark);">RAM & CPU Load</h3>
                <p style="margin: 5px 0; color: #555;"><strong>Current RAM Usage:</strong> <?php echo formatBytes($memory_used); ?></p>
                <p style="margin: 5px 0; color: #555;"><strong>Peak RAM Usage:</strong> <?php echo formatBytes($memory_peak); ?></p>
                <p style="margin: 5px 0; color: #555;"><strong>CPU Load:</strong> <?php echo is_array($load) ? implode(', ', $load) : $load; ?></p>
            </div>
        </div>
    </div>
</body>
</html>