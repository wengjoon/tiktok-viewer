<?php
// Save this file as posts-api-test.php in the root of your Laravel project
// Run it with: php posts-api-test.php

// Test script for the TikTok posts API
$apiHost = 'tiktok-api23.p.rapidapi.com';
$apiKey = '185e5b8f0fmsh6ea4a6b6c76678dp1bc1c9jsn7a40d65fa697';
$secUid = 'MS4wLjABAAAAqB08cUbXaDWqbD6MCga2RbGTuhfO2EsHayBYx08NDrN7IE3jQuRDNNN6YwyfH6_6'; // Taylor Swift

echo "Testing TikTok Posts API for secUid: $secUid\n\n";

// Initialize cURL session
$curl = curl_init();

// Set cURL options
curl_setopt_array($curl, [
    CURLOPT_URL => "https://{$apiHost}/api/user/posts?secUid={$secUid}&count=10&cursor=0",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "x-rapidapi-host: {$apiHost}",
        "x-rapidapi-key: {$apiKey}"
    ],
]);

// Execute the request
$response = curl_exec($curl);
$err = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

// Close cURL session
curl_close($curl);

echo "HTTP Status Code: $httpCode\n\n";

// Check for errors
if ($err) {
    echo "cURL Error: " . $err . "\n";
    exit(1);
}

// Decode JSON response
$data = json_decode($response, true);

// Check if data is valid JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "Error decoding JSON response: " . json_last_error_msg() . "\n";
    echo "Raw response:\n$response\n";
    exit(1);
}

// Print API response structure
echo "API Response Structure:\n";
print_r(array_keys($data));
echo "\n";

// Check if the response has videos
if (isset($data['itemList']) && is_array($data['itemList'])) {
    echo "Videos found in 'itemList'. Count: " . count($data['itemList']) . "\n";
    if (!empty($data['itemList'])) {
        echo "First video keys: " . implode(", ", array_keys($data['itemList'][0])) . "\n";
    }
} else if (isset($data['items']) && is_array($data['items'])) {
    echo "Videos found in 'items'. Count: " . count($data['items']) . "\n";
    if (!empty($data['items'])) {
        echo "First video keys: " . implode(", ", array_keys($data['items'][0])) . "\n";
    }
} else if (isset($data['data']['items']) && is_array($data['data']['items'])) {
    echo "Videos found in 'data.items'. Count: " . count($data['data']['items']) . "\n";
    if (!empty($data['data']['items'])) {
        echo "First video keys: " . implode(", ", array_keys($data['data']['items'][0])) . "\n";
    }
} else {
    echo "No videos found in expected locations. Response structure:\n";
    print_r($data);
}

echo "\nTest completed.\n";