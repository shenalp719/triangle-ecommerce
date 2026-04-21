<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Handle preview image if provided
$preview_image = null;
if (isset($data['preview_image']) && strpos($data['preview_image'], 'data:image') === 0) {
    // Extract the raw Base64 string
    $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $data['preview_image']);
    $preview_image = base64_decode($imageData);
}

// SECURE PREPARED STATEMENT: All 8 variables use '?' to prevent SQL Injection
$sql = "INSERT INTO designs (user_id, product_id, name, canvas_json, preview_image, width, height, resolution_dpi) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database prepare failed: ' . $conn->error]);
    exit();
}

// i = integer, s = string, b = blob
// We use 's' for the preview_image here because PHP passes binary data perfectly as strings in mysqli
$stmt->bind_param('iissssii', $user_id, $product_id, $name, $canvas_json, $preview_image, $width, $height, $resolution_dpi);

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
        'message' => 'Error saving design: ' . $stmt->error
    ]);
}
$stmt->close();
?>
