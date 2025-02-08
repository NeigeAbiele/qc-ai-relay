<?php
// index.php

// Autoload dependencies installed via Composer, including phpdotenv.
require __DIR__ . '/vendor/autoload.php';

// Load environment variables from .env file.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Retrieve the shared secret from the environment.
$SHARED_SECRET = getenv('SHARED_SECRET');

// Set the response content type.
header('Content-Type: application/json');

// Ensure that only GET requests are accepted.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed. Only GET requests are accepted.']);
    exit;
}

// Validate and sanitize input parameters using filter_input.
$messageParam = filter_input(INPUT_GET, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
$userParam    = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_SPECIAL_CHARS);
$secretParam  = filter_input(INPUT_GET, 'secret', FILTER_SANITIZE_SPECIAL_CHARS);

// Verify that the secret parameter matches the shared secret.
if (!$secretParam || $secretParam !== $SHARED_SECRET) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized: invalid secret.']);
    exit;
}

// Provide default values if parameters are missing or empty.
if (!$messageParam || trim($messageParam) === '') {
    $messageParam = 'Summarize what happened to CX Synthe';
}
if (!$userParam || trim($userParam) === '') {
    $userParam = 'Unknown';
}

// Optionally, you can log $userParam here if needed.

// Define the MindStudio API endpoint and your access token.
$mindstudio_url = 'https://api.mindstudio.ai/developer/v2/workers/run';
$access_token   = 'sk3TecsoVGRyIi6mgyau2yCuAUoCAUecYuOUY6IyUKuiiKeMwkS2sK8qGwSY8u6ge2aEkC8c0suIMOIc8KwmCw6s';

// Build the payload for the API call.
$payload = [
    'workerId'  => '1166c068-68f8-4481-acb7-2fb3c82e21c6',
    'variables' => [
        // You can optionally include the user's name in the message if desired.
        'message' => $messageParam
    ],
    'workflow'  => 'Main.flow'
];

// Initialize a cURL session to send the API request.
$ch = curl_init($mindstudio_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// Execute the cURL request and capture the response.
$response = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Process the API response and return a minimal JSON response.
if ($status == 200) {
    $json   = json_decode($response, true);
    $result = isset($json['result']) ? $json['result'] : 'No result provided.';
    echo json_encode(['result' => $result]);
} else {
    echo json_encode(['error' => 'API call failed with status ' . $status]);
}
?>
