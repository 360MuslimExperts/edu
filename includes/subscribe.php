<?php
require_once __DIR__ . '/config.php';
// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
header('Content-Type: application/json');

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed.']);
    exit;
}

// Sanitize and validate email
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// Brevo API configuration
$apiKey = defined('BREVO_API_KEY') ? BREVO_API_KEY : '';
$listId = defined('BREVO_LIST_ID') ? BREVO_LIST_ID : 0;

if (empty($apiKey) || (strpos($apiKey, 'xsmtpsib-') !== 0 && strpos($apiKey, 'xkeysib-') !== 0)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'API key is missing or invalid.']);
    exit;
}

// Prepare API request
$apiUrl = 'https://api.brevo.com/v3/contacts';
$data = [
    'email' => $email,
    'listIds' => [$listId],
    'updateEnabled' => true
];

$headers = [
    "accept: application/json",
    "api-key: $apiKey",
    "content-type: application/json"
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

header('Content-Type: application/json');
if ($curlError) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Curl error: ' . $curlError]);
    exit;
}
if ($httpCode === 201 || $httpCode === 204) {
    // Set cookie from PHP as fallback (for non-JS users)
    setcookie('newsletter_subscribed', '1', time() + 365*24*60*60, '/');
    echo json_encode(['success' => true, 'message' => 'Thank you for subscribing!']);
} else {
    $errorMsg = 'Subscription failed. Please try again later.';
    $isDuplicate = false;
    if ($response) {
        $respArr = json_decode($response, true);
        if (!empty($respArr['message'])) {
            $errorMsg = $respArr['message'];
            // Brevo duplicate detection
            if (
                (isset($respArr['code']) && $respArr['code'] === 'duplicate_parameter') ||
                stripos($respArr['message'], 'already exist') !== false
            ) {
                $errorMsg = 'You are already subscribed to our newsletter.';
                $isDuplicate = true;
            }
        } elseif (!empty($respArr['code'])) {
            $errorMsg .= ' (Error code: ' . $respArr['code'] . ')';
        }
    }
    if ($isDuplicate) {
        setcookie('newsletter_subscribed', '1', time() + 365*24*60*60, '/');
    }
    http_response_code($httpCode);
    echo json_encode([
        'success' => false,
        'message' => $errorMsg,
        'debug' => $response,
        'headers' => $headers
    ]);
}

/*
Manual test with curl (run in terminal, replace <EMAIL> and <APIKEY>):

curl -X POST "https://api.brevo.com/v3/contacts" \
  -H "accept: application/json" \
  -H "api-key: xsmtpsib-3491cbc6c0d5ed46234c72811f689b1bc4d1f314e84e889f7b6cfc31f3c87b5e-QqrBazNhCH4x1R7X" \
  -H "content-type: application/json" \
  -d '{"email":"<EMAIL>","listIds":[9],"updateEnabled":true}'
*/
