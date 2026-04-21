<?php
session_start();
// Point back one folder to find your db.php
require_once '../db.php'; 

$error = '';

// If they are already logged in as staff/admin, bypass this screen
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Look for staff or admin roles
        $stmt = $conn->prepare("SELECT id, first_name, last_name, password, role FROM users WHERE email = ? AND role IN ('staff', 'admin')");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['admin_role'] = $user['role']; 
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = 'Invalid password.';
            }
        } else {
            $error = 'Access Denied. You do not have administrative privileges.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Triangle Printing</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: var(--light-gray); display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: 'Inter', sans-serif; }
        .login-card { background: white; padding: 2.5rem; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 style="text-align: center; color: var(--primary-red); margin-bottom: 2rem;">Admin Portal</h2>
        
        <?php if ($error): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center; font-size: 0.9rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Staff Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
            </div>
            <div style="margin-bottom: 2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem; border-radius: 0.5rem; font-weight: bold; background-color: var(--text-dark); color: white; border: none; cursor: pointer;">
                Secure Login
            </button>
        </form>
        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="../index.php" style="color: var(--text-light); text-decoration: none; font-size: 0.85rem;">&larr; Return to Public Store</a>
        </div>
    </div>
</body>
</html>