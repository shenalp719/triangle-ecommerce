<?php
/**
 * Create Order API
 * Triangle Printing Solutions
 */
header('Content-Type: application/json');
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['items']) || count($data['items']) === 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No items in order']);
    exit();
}

$subtotal = floatval($data['subtotal'] ?? 0);
$shipping = floatval($data['shipping'] ?? 15);
$tax = floatval($data['tax'] ?? $subtotal * 0.08);
$total = $subtotal + $shipping + $tax;

$order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

$sql = "INSERT INTO orders (user_id, order_number, status, total_amount) 
        VALUES ($user_id, '$order_number', 'pending', $total)";

if ($conn->query($sql)) {
    $order_id = $conn->insert_id;
    
    // Add order items
    foreach ($data['items'] as $item) {
        $product_id = intval($item['productId'] ?? 1);
        $quantity = intval($item['quantity'] ?? 1);
        $unit_price = floatval($item['price'] ?? 0);
        
        // FIXED: Grab the name and image, making sure they are safe for the database
        $product_name = $conn->real_escape_string($item['name'] ?? 'Custom Product');
        $image_url = $conn->real_escape_string($item['image'] ?? '');
        
        // FIXED: Insert the name and image into the database too!
        $itemSQL = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, product_name, image) 
                   VALUES ($order_id, $product_id, $quantity, $unit_price, '$product_name', '$image_url')";
        
        $conn->query($itemSQL);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Order created successfully',
        'orderId' => $order_id,
        'orderNumber' => $order_number
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error creating order: ' . $conn->error
    ]);
}
?>