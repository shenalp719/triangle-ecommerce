<?php
session_start();
require_once 'db.php';

// PHPMailer Headers - MUST be at the very top
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$page_title = 'Payment Successful';
$order_saved = false;
$display_order_number = '';
$db_error = '';

if (isset($_SESSION['pending_order']) && isset($_SESSION['user_id'])) {
    $order = $_SESSION['pending_order'];
    $user_id = $_SESSION['user_id'];
    $total = $order['total'];
    
    // 1. Fetch user details for the email
    $user_stmt = $conn->prepare("SELECT email, first_name FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_data = $user_stmt->get_result()->fetch_assoc();
    $customer_email = $user_data['email'] ?? 'shenux2004@gmail.com';
    $customer_name = $user_data['first_name'] ?? 'Valued Customer';
    $user_stmt->close();

    $order_number = 'ORD-' . strtoupper(substr(uniqid(), -6));
    $display_order_number = $order_number;

    // Grab the Stripe ID
    $stripe_id = isset($_GET['session_id']) ? $_GET['session_id'] : (isset($_GET['payment_intent']) ? $_GET['payment_intent'] : 'txn_simulated_' . uniqid());

    // 2. Insert Order into Database
    $stmt = $conn->prepare("INSERT INTO orders (user_id, order_number, total_amount, status, stripe_payment_id) VALUES (?, ?, ?, 'pending', ?)");
    
    if (!$stmt) {
        $db_error = "Orders Prepare Error: " . $conn->error;
    } else {
        $stmt->bind_param("isds", $user_id, $order_number, $total, $stripe_id);
        
        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;
            $stmt->close();

            // 3. Save Order Items - FIXED: Added product_name and image to the INSERT query
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, quantity, unit_price, print_file, product_name, image) VALUES (?, ?, ?, ?, ?, ?)");
            $items_successful = true;

            foreach ($order['items'] as $item) {
                // Determine if item is an object or array and grab values safely
                $qty = is_object($item) ? $item->quantity : $item['quantity'];
                $price = is_object($item) ? $item->price : $item['price'];
                $custom_data = is_string($item) ? $item : json_encode($item); 
                
                // FIXED: Extract the name and image safely!
                $item_name = is_object($item) ? ($item->name ?? 'Custom Product') : ($item['name'] ?? 'Custom Product');
                $item_image = is_object($item) ? ($item->image ?? '') : ($item['image'] ?? '');

                // bind_param signature updated: "iids" -> "iidsss"
                $item_stmt->bind_param("iidsss", $order_id, $qty, $price, $custom_data, $item_name, $item_image);
                if (!$item_stmt->execute()) { $items_successful = false; }
            }
            $item_stmt->close();
            
            if ($items_successful) {
                $order_saved = true;
                unset($_SESSION['pending_order']);

                // 4. TRIGGER PHPMAILER (The Receipt)
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'shenux2004@gmail.com';
                    $mail->Password   = 'jooo cxnm ehez mggn';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('triangleprinting@gmail.com', 'Triangle Printing Solutions');
                    $mail->addAddress($customer_email, $customer_name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Order Confirmation - ' . $order_number;
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; max-width: 600px; border: 1px solid #eee; padding: 20px;'>
                            <h2 style='color: #E31E24;'>Thank You for Your Order!</h2>
                            <p>Hi $customer_name,</p>
                            <p>We've received your payment and our team is ready to start printing.</p>
                            <hr>
                            <p><strong>Order Number:</strong> $order_number</p>
                            <p><strong>Total Paid:</strong> $$total</p>
                            <p><strong>Status:</strong> Pending (Processing)</p>
                            <hr>
                            <p>You can view your order status anytime in your user dashboard.</p>
                            <p>Regards,<br><strong>Triangle Printing Team</strong></p>
                        </div>";

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Mail Error: " . $mail->ErrorInfo);
                }
            }
        } else {
            $db_error = "Orders Execute Error: " . $stmt->error;
        }
    }
} else {
    $db_error = "Session Error: Could not find user_id or pending_order in memory.";
}

include 'includes/header.php';
?>

<section style="padding: 5rem 2rem; text-align: center; min-height: 60vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">
    <div style="background-color: #d4edda; color: #155724; padding: 2.5rem; border-radius: 1rem; max-width: 550px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h1 style="font-size: 3.5rem; margin-bottom: 1rem;">✅</h1>
        <h2 style="margin-bottom: 0.5rem;">Payment Successful!</h2>
        
        <?php if ($order_saved): ?>
            <p style="font-weight: bold; font-size: 1.2rem; margin-bottom: 1rem;">Order #: <?php echo $display_order_number; ?></p>
            <p style="margin-bottom: 1.5rem;">A confirmation email has been sent to <strong><?php echo htmlspecialchars($customer_email); ?></strong>.</p>
        <?php else: ?>
            <div style="color: #721c24; background-color: #f8d7da; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; border: 1px solid #f5c6cb;">
                <p style="font-weight: bold; margin-bottom: 0.5rem;">Database Save Failed!</p>
                <p style="font-family: monospace; font-size: 0.9rem; word-break: break-all;">
                    <?php echo $db_error ? $db_error : "Unknown error occurred."; ?>
                </p>
            </div>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-primary" style="padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: bold; margin-right: 0.5rem;">View Order History</a>
        <a href="index.php" class="btn btn-secondary" style="padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: bold;">Return to Home</a>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof app !== 'undefined' && typeof app.clearCart === 'function') {
            app.clearCart();
        }
    });
</script>

<?php include 'includes/footer.php'; ?>