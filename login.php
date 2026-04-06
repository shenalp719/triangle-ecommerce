<?php
/**
 * Login Page - Triangle Printing Solutions
 */
session_start();
require_once 'db.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: /triangle-ecommerce/dashboard.php');
    exit();
}

$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!$email || !$password) {
        $error = 'Please fill in all fields';
    } else {
        $result = executeQuery("SELECT id, email, password, first_name, role FROM users WHERE email = '$email'");
        
        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'];
                $_SESSION['role'] = $user['role'];
                
                header('Location: /triangle-ecommerce/dashboard.php');
                exit();
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'Email not found';
        }
    }
}

$page_title = 'Login';
include 'includes/header.php';
?>

    <section style="max-width: 500px; margin: 3rem auto; padding: 2rem;">
        <div class="card">
            <div class="card-body">
                <h2 style="text-align: center; margin-bottom: 2rem;">Login to Your Account</h2>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="#forgot" style="font-size: 0.9rem;">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="padding: 0.875rem;">
                        Login
                    </button>
                </form>

                <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid var(--border-color);">

                <p style="text-align: center; margin-bottom: 0;">
                    Don't have an account? <a href="register.php" style="font-weight: 600;">Register here</a>
                </p>
            </div>
        </div>

        <!-- Demo Credentials -->
        <div class="alert alert-info" style="margin-top: 1.5rem;">
            <strong>Demo Account:</strong><br>
            Email: demo@triangleprinting.com<br>
            Password: demo123456
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
