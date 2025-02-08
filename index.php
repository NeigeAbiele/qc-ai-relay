<?php
// index.php

header('Content-Type: application/json');

// Define the shared secret that both the LSL script and PHP script know
$SHARED_SECRET = 'gFs^ZHf%sE@%sz&9@mbc@&HLk&mHupeRL+ymGQrfRhHu$C4Fhd$r*AKXeu2cTj6q';

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed. Only GET requests are accepted.']);
    exit;
}

// Retrieve and sanitize parameters from GET
$messageParam = filter_input(INPUT_GET, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
$userParam    = filter_input(INPUT_GET, 'user', FILTER_SANITIZE_SPECIAL_CHARS);
$secretParam  = filter_input(INPUT_GET, 'secret', FILTER_SANITIZE_SPECIAL_CHARS);

// Check if the shared secret matches
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

// (Optional) You can log or process $userParam as needed.

// MindStudio API endpoint and access token
$mindstudio_url = 'https://api.mindstudio.ai/developer/v2/workers/run';
$access_token   = 'sk3TecsoVGRyIi6mgyau2yCuAUoCAUecYuOUY6IyUKuiiKeMwkS2sK8qGwSY8u6ge2aEkC8c0suIMOIc8KwmCw6s';

// Build the payload using the sanitized message (optionally incorporate the user's name)
$payload = [
    'workerId'  => '1166c068-68f8-4481-acb7-2fb3c82e21c6',
    'variables' => [
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
