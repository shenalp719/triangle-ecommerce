<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// THE RBAC LOCK: Only Shop Owners (admins) can manage users
if ($_SESSION['admin_role'] !== 'admin') {
    header("Location: staff_dashboard.php");
    exit();
}

require_once '../db.php';
$message = '';
$edit_user = null;

// 1. Handle Delete User
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    if ($delete_id === $_SESSION['admin_id']) {
        $message = "<div style='background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'><strong>Error:</strong> You cannot delete your own active session account.</div>";
    } else {
        $conn->query("DELETE FROM users WHERE id = $delete_id");
        $message = "<div style='background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'>User account permanently removed.</div>";
    }
}

// 2. Fetch User for Editing
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_user = $conn->query("SELECT * FROM users WHERE id = $edit_id")->fetch_assoc();
}

// 3. Handle Add / Update User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    if ($user_id > 0) {
        // --- UPDATE EXISTING USER ---
        if (!empty($_POST['password'])) {
            // Update everything INCLUDING password
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=?, password=? WHERE id=?");
            $stmt->bind_param("sssssi", $first_name, $last_name, $email, $role, $password, $user_id);
        } else {
            // Update everything EXCEPT password
            $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, role=? WHERE id=?");
            $stmt->bind_param("ssssi", $first_name, $last_name, $email, $role, $user_id);
        }

        if ($stmt->execute()) {
            $message = "<div style='background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'>User account updated successfully!</div>";
            $edit_user = null; // Clear the edit state
        } else {
            $message = "<div style='background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'><strong>Error:</strong> Could not update user. Email might already be in use.</div>";
        }
    } else {
        // --- CREATE NEW USER ---
        if (empty($_POST['password'])) {
            $message = "<div style='background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'><strong>Error:</strong> A password is required for new accounts.</div>";
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check->bind_param("s", $email);
            $check->execute();
            
            if ($check->get_result()->num_rows > 0) {
                $message = "<div style='background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'><strong>Error:</strong> That email address is already registered.</div>";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $first_name, $last_name, $email, $password, $role);
                if ($stmt->execute()) {
                    $message = "<div style='background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;'>New account created successfully!</div>";
                }
            }
        }
    }
}

// --- TABBED VIEW LOGIC ---
$view = isset($_GET['view']) ? $_GET['view'] : 'customers';

if ($view === 'staff') {
    $users = $conn->query("SELECT * FROM users WHERE role IN ('staff', 'admin') ORDER BY id DESC");
} else {
    $users = $conn->query("SELECT * FROM users WHERE role NOT IN ('staff', 'admin') ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Triangle Admin</title>
    <style>
        :root { --primary: #E31E24; --dark: #333; --light: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light); display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background-color: var(--dark); color: white; padding: 2rem 0; display: flex; flex-direction: column; flex-shrink: 0;}
        .sidebar h2 { text-align: center; color: var(--primary); margin-bottom: 2rem; font-size: 1.5rem; }
        .sidebar a { color: white; text-decoration: none; padding: 1rem 2rem; display: block; border-left: 4px solid transparent; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(255,255,255,0.1); border-left-color: var(--primary); }
        .content { flex: 1; padding: 2rem; overflow-y: auto; }
        
        .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 2rem;}
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f9f9f9; color: #555; }
        
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 0.9rem; display: inline-block;}
        .btn-primary { background: var(--dark); color: white; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-cancel { background: #ccc; color: #333; }
        
        input, select { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 1rem; box-sizing: border-box; font-family: inherit;}
        
        /* Tab Styles */
        .tab-btn { padding: 0.5rem 1.5rem; text-decoration: none; border-radius: 4px; border: 2px solid var(--dark); font-weight: bold; transition: 0.2s; }
        .tab-active { background: var(--dark); color: white; }
        .tab-inactive { background: transparent; color: var(--dark); }
        .tab-inactive:hover { background: rgba(0,0,0,0.05); }
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
        <h1 style="margin-top: 0;">User & Staff Management</h1>
        
        <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
            <a href="customers.php?view=customers" class="tab-btn <?php echo $view === 'customers' ? 'tab-active' : 'tab-inactive'; ?>">Registered Customers</a>
            <a href="customers.php?view=staff" class="tab-btn <?php echo $view === 'staff' ? 'tab-active' : 'tab-inactive'; ?>">Internal Staff & Admins</a>
        </div>

        <?php echo $message; ?>

        <div class="card" style="border-top: 4px solid var(--primary);">
            <h3 style="margin-top: 0;"><?php echo $edit_user ? '✏️ Edit Account: ' . htmlspecialchars($edit_user['first_name']) : '➕ Add New Account'; ?></h3>
            <form method="POST" action="customers.php?view=<?php echo $view; ?>">
                <input type="hidden" name="user_id" value="<?php echo $edit_user ? $edit_user['id'] : ''; ?>">
                
                <div style="display: flex; gap: 1rem;">
                    <div style="flex: 1;">
                        <label>First Name</label>
                        <input type="text" name="first_name" required value="<?php echo $edit_user ? htmlspecialchars($edit_user['first_name']) : ''; ?>">
                    </div>
                    <div style="flex: 1;">
                        <label>Last Name</label>
                        <input type="text" name="last_name" required value="<?php echo $edit_user ? htmlspecialchars($edit_user['last_name']) : ''; ?>">
                    </div>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <div style="flex: 1;">
                        <label>Email Address</label>
                        <input type="email" name="email" required value="<?php echo $edit_user ? htmlspecialchars($edit_user['email']) : ''; ?>">
                    </div>
                    <div style="flex: 1;">
                        <label>Password <?php echo $edit_user ? '(Leave blank to keep current)' : ''; ?></label>
                        <input type="password" name="password" <?php echo $edit_user ? '' : 'required'; ?> placeholder="<?php echo $edit_user ? 'Enter new password or leave blank' : 'Will be securely hashed'; ?>">
                    </div>
                    <div style="flex: 1;">
                        <label>Account Role</label>
                        <select name="role" required>
                            <option value="customer" <?php echo ($edit_user && $edit_user['role'] == 'customer') || (!$edit_user && $view === 'customers') ? 'selected' : ''; ?>>Customer</option>
                            <option value="staff" <?php echo ($edit_user && $edit_user['role'] == 'staff') || (!$edit_user && $view === 'staff') ? 'selected' : ''; ?>>Print Staff</option>
                            <option value="admin" <?php echo ($edit_user && $edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Shop Owner (Admin)</option>
                        </select>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="save_user" class="btn btn-primary">
                        <?php echo $edit_user ? 'Save Changes' : 'Create Account'; ?>
                    </button>
                    <?php if($edit_user): ?>
                        <a href="customers.php?view=<?php echo $view; ?>" class="btn btn-cancel">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="card">
            <h3 style="margin-top: 0;"><?php echo $view === 'staff' ? 'Staff Directory' : 'Customer Database'; ?></h3>
            <?php if($users->num_rows === 0): ?>
                <p>No users found in this category.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                    <?php while($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span style="background: #e1e1e1; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; font-weight: bold; text-transform: uppercase;">
                                    <?php echo htmlspecialchars($row['role']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="customers.php?view=<?php echo $view; ?>&edit=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                                
                                <?php if ($row['id'] !== $_SESSION['admin_id']): ?>
                                    <a href="customers.php?view=<?php echo $view; ?>&delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to permanently delete this user?');">Remove</a>
                                <?php else: ?>
                                    <span style="color: #888; font-size: 0.85rem; margin-left: 0.5rem;">(You)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>