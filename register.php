<?php
/**
 * Register Page - Triangle Printing Solutions
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

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    
    // Validation
    if (!$email || !$password || !$first_name) {
        $error = 'Please fill in all required fields';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match';
    } else {
        // Check if email already exists
        $checkResult = executeQuery("SELECT id FROM users WHERE email = '$email'");
        
        if ($checkResult && $checkResult->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            // Create account
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insertSQL = "INSERT INTO users (email, password, first_name, last_name, role) 
                         VALUES ('$email', '$hashed_password', '$first_name', '$last_name', 'customer')";
            
            if (executeQuery($insertSQL)) {
                $success = 'Account created successfully! Please login.';
                
                // Auto-login
                $userResult = executeQuery("SELECT id, email, first_name, role FROM users WHERE email = '$email'");
                if ($userResult && $userResult->num_rows === 1) {
                    $user = $userResult->fetch_assoc();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['first_name'];
                    $_SESSION['role'] = $user['role'];
                    
                    header('Location: /triangle-ecommerce/dashboard.php');
                    exit();
                }
            } else {
                $error = 'Error creating account. Please try again.';
            }
        }
    }
}

$page_title = 'Register';
include 'includes/header.php';
?>

    <section style="max-width: 600px; margin: 3rem auto; padding: 2rem;">
        <div class="card">
            <div class="card-body">
                <h2 style="text-align: center; margin-bottom: 2rem;">Create Your Account</h2>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required minlength="8">
                        <small style="color: var(--text-light);">Minimum 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Confirm Password *</label>
                        <input type="password" id="password_confirm" name="password_confirm" required minlength="8">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <input type="checkbox" name="terms" required style="margin-top: 0.25rem;">
                            <span style="font-size: 0.9rem;">I agree to the <a href="#" style="color: var(--primary-red);">Terms of Service</a> and <a href="#" style="color: var(--primary-red);">Privacy Policy</a></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="padding: 0.875rem;">
                        Create Account
                    </button>
                </form>

                <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid var(--border-color);">

                <p style="text-align: center; margin-bottom: 0;">
                    Already have an account? <a href="login.php" style="font-weight: 600;">Login here</a>
                </p>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
