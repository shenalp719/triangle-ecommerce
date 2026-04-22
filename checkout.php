<?php
// checkout.php
session_start();

require_once 'vendor/autoload.php';
require_once 'secrets.php';

// 1. STRICT CHECK: Are they actually logged in?
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'You are not logged in. Please log in before checking out.']);
    exit();
}

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$input = file_get_contents('php://input');
$body = json_decode($input);

// 2. STRICT CHECK: Did the cart actually arrive from JS?
if (isset($body->items) && isset($body->total)) {
    // Lock it in the vault!
    $_SESSION['pending_order'] = [
        'total' => $body->total,
        'items' => $body->items
    ];
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Cart data is missing.']);
    exit();
}

$amountInCents = round($body->total * 100);
$itemCount = $body->itemCount;

// 3. THE MAGIC FIX: Dynamically grab the exact domain you are using right now
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$YOUR_DOMAIN = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/triangle-ecommerce";

try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $amountInCents,
                'product_data' => [
                    'name' => 'Triangle Printing Solutions - Custom Order',
                    'description' => $itemCount . ' custom item(s) including tax and shipping.',
                ],
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $YOUR_DOMAIN . '/cart.php',
    ]);

    echo json_encode(['url' => $checkout_session->url]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>