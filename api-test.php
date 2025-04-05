<?php
// Save this file as api-test.php in the root of your Laravel project
// Run it with: php api-test.php

// Simple script to test the TikTok API directly
$apiHost = 'tiktok-api23.p.rapidapi.com';
$apiKey = '185e5b8f0fmsh6ea4a6b6c76678dp1bc1c9jsn7a40d65fa697';
$username = 'taylorswift'; // Replace with a username you want to test

echo "Testing TikTok API for username: $username\n\n";

// Initialize cURL session
$curl = curl_init();

// Set cURL options
curl_setopt_array($curl, [
    CURLOPT_URL => "https://{$apiHost}/api/user/info?uniqueId={$username}",
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

// Check if the response has the expected structure
if (isset($data['data']) && isset($data['data']['user'])) {
    echo "User data found successfully!\n";
    
    $user = $data['data']['user'];
    
    echo "\nBasic User Info:\n";
    echo "- Nickname: " . ($user['nickname'] ?? 'N/A') . "\n";
    echo "- Username: " . ($user['uniqueId'] ?? 'N/A') . "\n";
    echo "- SecUid: " . ($user['secUid'] ?? 'N/A') . "\n";
    echo "- Followers: " . number_format($user['followerCount'] ?? 0) . "\n";
    echo "- Following: " . number_format($user['followingCount'] ?? 0) . "\n";
    echo "- Likes: " . number_format($user['heartCount'] ?? 0) . "\n";
    
    echo "\nThe API is working correctly. If your application is not working, the issue is in your Laravel code.\n";
} else {
    echo "User data not found in the API response.\n";
    echo "API response:\n";
    print_r($data);
    exit(1);
}

echo "\nTest completed.\n";