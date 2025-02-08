<?php
// index.php

header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed. Only GET requests are accepted.']);
    exit;
}

// Validate and sanitize the inputs.
// Using filter_input to retrieve and sanitize the 'message' and 'user' parameters.
// FILTER_SANITIZE_SPECIAL_CHARS will escape HTML entities to prevent XSS attacks.
$messageParam = filter_input(INPUT_GET, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
$userParam    = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_SPECIAL_CHARS);

// Provide default values if parameters are missing or empty.
if (!$messageParam || trim($messageParam) === '') {
    $messageParam = 'Summarize what happened to CX Synthe';
}
if (!$userParam || trim($userParam) === '') {
    $userParam = 'Unknown';
}

// Optionally, you can log or further process $userParam if needed.

// MindStudio API endpoint and access token
$mindstudio_url = 'https://api.mindstudio.ai/developer/v2/workers/run';
$access_token   = 'sk3TecsoVGRyIi6mgyau2yCuAUoCAUecYuOUY6IyUKuiiKeMwkS2sK8qGwSY8u6ge2aEkC8c0suIMOIc8KwmCw6s';

// Build the payload using the sanitized message
$payload = [
    'workerId'  => '1166c068-68f8-4481-acb7-2fb3c82e21c6',
    'variables' => [
        // Optionally prepend or incorporate the user's name into the message
        'message' => $messageParam
    ],
    'workflow'  => 'Main.flow'
];

// Initialize cURL to send the API request
$ch = curl_init($mindstudio_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $access_token,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// Execute the request
$response = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Process and return a minimal JSON response
if ($status == 200) {
    $json   = json_decode($response, true);
    $result = isset($json['result']) ? $json['result'] : 'No result provided.';
    echo json_encode(['result' => $result]);
} else {
    echo json_encode(['error' => 'API call failed with status ' . $status]);
}
?>
