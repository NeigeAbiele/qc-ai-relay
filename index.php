<?php
// index.php

header('Content-Type: application/json');

// MindStudio API endpoint and token
$mindstudio_url = 'https://api.mindstudio.ai/developer/v2/workers/run';
$access_token = 'sk3TecsoVGRyIi6mgyau2yCuAUoCAUecYuOUY6IyUKuiiKeMwkS2sK8qGwSY8u6ge2aEkC8c0suIMOIc8KwmCw6s';

// Prepare the payload
$payload = [
    'workerId'  => '1166c068-68f8-4481-acb7-2fb3c82e21c6',
    'variables' => [
        'message' => 'Summerize what happened to CX Synthe'
    ],
    'workflow'  => 'Main.flow'
];

// Initialize cURL
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
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Process the response
if ($status == 200) {
    $json = json_decode($response, true);
    // Extract just the "result" field
    $result = isset($json['result']) ? $json['result'] : 'No result provided.';
    echo json_encode(['result' => $result]);
} else {
    echo json_encode(['error' => 'API call failed with status ' . $status]);
}
?>
