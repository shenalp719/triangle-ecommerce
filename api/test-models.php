<?php
// api/test-models.php
require_once '../secrets.php';

// We are calling the base 'models' endpoint with a GET request
$url = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . GEMINI_API_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Bypassing WAMP SSL issues again
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$response = curl_exec($ch);
curl_close($ch);

// Output the raw results from Google
header('Content-Type: application/json');
echo $response;
?>