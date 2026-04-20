<?php
session_start();
require_once 'db.php'; // Make sure this file sets up your database connection as $conn

$page_title = 'Register';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "An account with this email already exists.";
        } else {
            // Hash the password securely
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'customer'; // Default role

            // Insert new user
            $insert_stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("sssss", $first_name, $last_name, $email, $hashed_password, $role);
            
            if ($insert_stmt->execute()) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<section class="container" style="padding-top: 4rem; padding-bottom: 4rem; max-width: 500px; margin: 0 auto;">
    <div style="background: var(--white); padding: 2rem; border-radius: 0.75rem; border: 1px solid var(--border-color);">
        <h2 style="margin-bottom: 1.5rem; text-align: center;">Create an Account</h2>
        
        <?php if ($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">First Name</label>
                    <input type="text" name="first_name" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Last Name</label>
                    <input type="text" name="last_name" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                </div>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Email Address</label>
                <input type="email" name="email" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Confirm Password</label>
                <input type="password" name="confirm_password" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 0.5rem;">
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="width: 100%; padding: 1rem; border-radius: 0.5rem; font-weight: bold; cursor: pointer;">Register</button>
            
            <p style="text-align: center; margin-top: 1rem;">
                Already have an account? <a href="login.php" style="color: var(--primary-red);">Login here</a>
            </p>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>