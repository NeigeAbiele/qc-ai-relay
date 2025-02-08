<?php
// index.php

header('Content-Type: application/json');

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed. Only GET requests are accepted.']);
    exit;
}

// Retrieve parameters from GET (you may also use $_POST if desired)
$messageParam = isset($_GET['message']) ? $_GET['message'] : 'Summarize what happened to CX Synthe';
$userParam    = isset($_GET['user']) ? $_GET['user'] : 'Unknown';

// (Optional) You can log or process $userParam as needed. For now, we simply use $messageParam.

// MindStudio API endpoint and access token
$mindstudio_url = 'https://api.mindstudio.ai/developer/v2/workers/run';
$access_token   = 'sk3TecsoVGRyIi6mgyau2yCuAUoCAUecYuOUY6IyUKuiiKeMwkS2sK8qGwSY8u6ge2aEkC8c0suIMOIc8KwmCw6s';

// Build the payload using the message from the request
$payload = [
    'workerId'  => '1166c068-68f8-4481-acb7-2fb3c82e21c6',
    'variables' => [
        // Using the message passed from LSL; you might also prepend the user's name if desired:
        // "[$userParam] $messageParam"
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
