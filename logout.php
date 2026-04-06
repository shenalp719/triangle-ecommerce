<?php
/**
 * Logout Page
 */
session_start();

// Destroy session
session_destroy();

// Redirect to home
header('Location: /triangle-ecommerce/');
exit();
?>
