<?php
// Standalone Database Backup Script
session_start();

// Security: Make sure they are logged in and are an ADMIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || $_SESSION['admin_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$backup_dir = __DIR__ . '/';
$message = '';
$message_type = ''; // 'success' or 'error'

// ==========================================
// ACTION 1: DELETE A BACKUP
// ==========================================
if (isset($_POST['delete_backup']) && !empty($_POST['filename'])) {
    // basename() prevents directory traversal attacks (e.g., trying to delete ../index.php)
    $file_to_delete = basename($_POST['filename']); 
    $file_path = $backup_dir . $file_to_delete;
    
    // Double check it's actually a .sql file and exists
    if (strpos($file_to_delete, '.sql') !== false && file_exists($file_path)) {
        if (unlink($file_path)) {
            $message = "✅ Backup '$file_to_delete' was successfully deleted.";
            $message_type = "success";
        } else {
            $message = "❌ Failed to delete backup.";
            $message_type = "error";
        }
    }
}

// ==========================================
// ACTION 2: CREATE A NEW BACKUP
// ==========================================
if (isset($_POST['create_backup'])) {
    $host = 'localhost';
    $username = 'root'; 
    $password = '';     
    $database = 'triangle_printing'; 

    $backup_name = 'backup_triangle_' . date("Y-m-d-H-i-s") . '.sql';
    $backup_path = $backup_dir . $backup_name;

    // Define the exact path to mysqldump for WAMP (Update version if needed!)
    $mysqldump_path = 'C:\wamp64\bin\mysql\mysql9.1.0\bin\mysqldump.exe'; 

    // Command to execute
    $command = "\"$mysqldump_path\" --opt -h $host -u $username " . ($password ? "-p$password " : "") . "$database > \"$backup_path\"";
    
    system($command, $output);

    if (file_exists($backup_path) && filesize($backup_path) > 0) {
        $message = "✅ Backup successfully created!";
        $message_type = "success";
    } else {
        $message = "❌ Backup failed. Check your WAMP mysqldump path.";
        $message_type = "error";
    }
}

// ==========================================
// GET LIST OF EXISTING BACKUPS
// ==========================================
$existing_backups = glob($backup_dir . "*.sql");

// Sort the files so the newest backups are at the top of the list
if ($existing_backups) {
    usort($existing_backups, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Backup - Triangle Printing</title>
    <style>
        :root { --primary: #E31E24; --dark: #333; --light: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light); display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--dark); color: white; padding: 2rem 0; display: flex; flex-direction: column; flex-shrink: 0;}
        .sidebar h2 { text-align: center; color: var(--primary); margin-bottom: 2rem; font-size: 1.5rem; }
        .sidebar a { color: white; text-decoration: none; padding: 1rem 2rem; display: block; border-left: 4px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); border-left-color: var(--primary); }
        .content { flex: 1; padding: 2rem; overflow-y: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; background: white; padding: 1.5rem 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 5px solid var(--primary); }
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 20px;}
        
        .btn { padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-weight: bold; display: inline-block; transition: 0.2s; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #c0392b; }
        .btn-danger { background: #e74c3c; color: white; padding: 0.4rem 0.8rem; font-size: 0.9rem;}
        .btn-danger:hover { background: #c0392b; }
        
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; border-left: 4px solid; }
        .alert-success { background: #e8f8f5; border-color: #27ae60; color: #27ae60; }
        .alert-error { background: #fdedec; border-color: #e74c3c; color: #e74c3c; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; color: var(--dark); }
        tr:hover { background-color: #f1f1f1; }
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
        <a href="system_monitor.php">🖥️ System Monitor</a>
        <a href="database_backup.php" class="active">🗄️ Database Backup</a>
        <a href="logout.php" style="margin-top: auto; background-color: #c82333;">Logout</a>
    </div>

    <div class="content">
        <div class="header">
            <div>
                <h1 style="margin: 0; font-size: 1.8rem;">🗄️ Database Management</h1>
                <p style="margin: 0.5rem 0 0 0; color: #666;">Create and manage system backups.</p>
            </div>
        </div>

        <?php if ($message != ''): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <strong><?php echo $message; ?></strong>
            </div>
        <?php endif; ?>

        <!-- CREATE BACKUP SECTION -->
        <div class="card">
            <h2 style="margin-top: 0; color: var(--dark);">Create New Backup</h2>
            <p style="color: #555;">Click the button below to generate a new `.sql` dump of the entire <strong>triangle_printing</strong> database. This will save all current orders, products, and user accounts.</p>
            <form method="POST" action="">
                <button type="submit" name="create_backup" class="btn btn-primary">➕ Generate New Backup</button>
            </form>
        </div>

        <!-- MANAGE EXISTING BACKUPS SECTION -->
        <div class="card">
            <h2 style="margin-top: 0; color: var(--dark);">Existing Backups</h2>
            
            <?php if (empty($existing_backups)): ?>
                <p style="color: #777; font-style: italic;">No backups found on the server.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Date Created</th>
                            <th>File Size</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($existing_backups as $file): ?>
                            <tr>
                                <td><code style="background: #eee; padding: 2px 6px; border-radius: 4px;"><?php echo basename($file); ?></code></td>
                                <td><?php echo date("F d, Y - h:i A", filemtime($file)); ?></td>
                                <td><?php echo round(filesize($file) / 1024, 2); ?> KB</td>
                                <td>
                                    <!-- Delete Button Form -->
                                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to permanently delete this backup?');" style="margin:0;">
                                        <input type="hidden" name="filename" value="<?php echo basename($file); ?>">
                                        <button type="submit" name="delete_backup" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>