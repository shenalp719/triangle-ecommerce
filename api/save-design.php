<?php
/**
 * Save Design API
 * Triangle Printing Solutions
 */
header('Content-Type: application/json');
session_start();
require_once '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['name']) || !isset($data['canvas_json'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$name = sanitize($data['name']);
$product_id = intval($data['product_id'] ?? 1);
$canvas_json = sanitize($data['canvas_json']);
$resolution_dpi = intval($data['resolution_dpi'] ?? 300);
$width = intval($data['width'] ?? 600);
$height = intval($data['height'] ?? 600);

// Handle preview image if provided
$preview_image = null;
if (isset($data['preview_image']) && strpos($data['preview_image'], 'data:image') === 0) {
    // Decode base64 image
    $imageData = $data['preview_image'];
    $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $preview_image = base64_decode($imageData);
}

// Insert into database
$sql = "INSERT INTO designs (user_id, product_id, name, canvas_json, preview_image, width, height, resolution_dpi) 
        VALUES ($user_id, $product_id, '$name', '$canvas_json', ?, $width, $height, $resolution_dpi)";

// Use prepared statement for BLOB
$stmt = $conn->prepare($sql);
$null = NULL;
$stmt->bind_param('sb', $null, $preview_image);

if ($preview_image) {
    $stmt->bind_param('b', $preview_image);
}

if ($stmt->execute()) {
    $design_id = $conn->insert_id;
    echo json_encode([
        'success' => true,
        'message' => 'Design saved successfully',
        'design_id' => $design_id
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error saving design: ' . $conn->error
    ]);
}
?>
