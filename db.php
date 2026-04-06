<?php
/**
 * Database Configuration & Connection
 * Triangle Printing Solutions - Web-to-Print E-commerce
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'triangle_ecommerce');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    // Redirect to setup if DB doesn't exist
    header('Location: /triangle-ecommerce/setup.php');
    exit();
}

// Set charset
$conn->set_charset("utf8mb4");

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to safely escape strings
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// Helper function to run queries
function executeQuery($sql) {
    global $conn;
    $result = $conn->query($sql);
    if (!$result) {
        error_log("Database error: " . $conn->error);
        return false;
    }
    return $result;
}

?>
