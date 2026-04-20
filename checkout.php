<?php
// checkout.php
require_once 'vendor/autoload.php'; // Loads the Stripe library
require_once 'secrets.php'; // Loads your hidden API key

// Set your Stripe Secret Key using the hidden variable
\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

// Get the JSON data sent from the JavaScript frontend
$input = file_get_contents('php://input');
$body = json_decode($input);

// Stripe requires amounts in EXACT CENTS (e.g., $15.50 = 1550 cents)
// We use round() to prevent weird decimal errors from tax calculations
$amountInCents = round($body->total * 100);
$itemCount = $body->itemCount;

$YOUR_DOMAIN = 'http://localhost/triangle-ecommerce'; 

try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'unit_amount' => $amountInCents,
                'product_data' => [
                    'name' => 'Triangle Printing Solutions - Custom Order',
                    'description' => $itemCount . ' custom item(s) including applicable tax and shipping.',
                ],
            ],
            // We group the whole cart into 1 quantity for the Stripe payment page
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/success.php',
        'cancel_url' => $YOUR_DOMAIN . '/cart.php', // Send them back to the cart if they cancel
    ]);

    // Send the Stripe URL back to the frontend
    echo json_encode(['url' => $checkout_session->url]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>