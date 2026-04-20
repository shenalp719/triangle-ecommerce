<?php
session_start();
require_once 'db.php';

// If already logged in, send them to the cart
if (isset($_SESSION['user_id'])) {
    header("Location: cart.php");
    exit();
}

$page_title = 'Login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, first_name, last_name, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify the hashed password
            if (password_verify($password, $user['password'])) {
                // Set Session Variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect to cart or dashboard
                header("Location: cart.php");
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<section class="container" style="padding-top: 4rem; padding-bottom: 4rem; max-width: 400px; margin: 0 auto;">
    <div style="background: var(--white); padding: 2rem; border-radius: 0.75rem; border: 1px solid var(--border-color);">
        <h2 style="margin-bottom: 1.5rem; text-align: center;">Welcome Back</h2>
        
        <?php if ($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Email Address</label>
                <input type="email" name="email" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="width: 100%; padding: 1rem; border-radius: 0.5rem; font-weight: bold; cursor: pointer;">Login</button>
            
            <p style="text-align: center; margin-top: 1rem;">
                Don't have an account? <a href="register.php" style="color: var(--primary-red);">Register here</a>
            </p>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>