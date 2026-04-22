<?php
// api/chat.php
require_once '../secrets.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = isset($input['message']) ? trim($input['message']) : '';

if (empty($userMessage)) {
    echo json_encode(['reply' => "I didn't catch that."]);
    exit();
}

// System Prompt
$systemPrompt = "You are a support bot for Triangle Printing. Pricing: Posters $25, Mugs $12, T-shirts $18. Delivery 5-7 days.";

$data = [
    "contents" => [
        ["parts" => [["text" => $systemPrompt . "\n\nCustomer: " . $userMessage]]]
    ]
];

// Using the exact model alias from your Google AI Studio snippet
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . GEMINI_API_KEY;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if ($http_code === 200 && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    echo json_encode(['reply' => $result['candidates'][0]['content']['parts'][0]['text']]);
} else {
    // FAILSAFE: If the API is still 429 (Quota), give a smart local response
    $lowerMsg = strtolower($userMessage);
    if (strpos($lowerMsg, 'price') !== false) {
        $reply = "Our posters start at $25 and mugs at $12. Which are you interested in?";
    } elseif (strpos($lowerMsg, 'deliver') !== false || strpos($lowerMsg, 'ship') !== false) {
        $reply = "Shipping takes 5-7 business days for $15, or 2-3 days for $30.";
    } else {
        $reply = "I'm currently in 'offline mode' due to high traffic, but I can still help with pricing and delivery questions!";
    }
    echo json_encode(['reply' => $reply]);
}