<?php
// Save as api-test.php and run with: php api-test.php
// Tests the TikTok API functionality

// Set your API details
$apiHost = 'tiktok-scraper7.p.rapidapi.com';
$apiKey = '185e5b8f0fmsh6ea4a6b6c76678dp1bc1c9jsn7a40d65fa697';
$userId = '107955'; // TikTok official account

echo "=== TIKTOK API TEST SCRIPT ===\n\n";

// Test 1: User Info by user_id
echo "Test 1: Fetching user info for user_id: {$userId}\n";
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://{$apiHost}/user/info?user_id={$userId}",
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

$response = curl_exec($curl);
$err = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Status Code: {$httpCode}\n";
if ($err) {
    echo "Error: {$err}\n";
} else {
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Response Code: " . ($data['code'] ?? 'N/A') . "\n";
        echo "User Found: " . (isset($data['data']['user']) ? 'Yes' : 'No') . "\n";
        if (isset($data['data']['user'])) {
            echo "Username: " . ($data['data']['user']['unique_id'] ?? 'N/A') . "\n";
            echo "Nickname: " . ($data['data']['user']['nickname'] ?? 'N/A') . "\n";
        }
    } else {
        echo "Invalid JSON response\n";
    }
}

echo "\n";

// Test 2: User Info by unique_id (username)
$username = 'tiktok';
echo "Test 2: Fetching user info for unique_id: {$username}\n";
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://{$apiHost}/user/info?unique_id={$username}",
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

$response = curl_exec($curl);
$err = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Status Code: {$httpCode}\n";
if ($err) {
    echo "Error: {$err}\n";
} else {
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Response Code: " . ($data['code'] ?? 'N/A') . "\n";
        echo "User Found: " . (isset($data['data']['user']) ? 'Yes' : 'No') . "\n";
        if (isset($data['data']['user'])) {
            echo "Username: " . ($data['data']['user']['unique_id'] ?? 'N/A') . "\n";
            echo "Nickname: " . ($data['data']['user']['nickname'] ?? 'N/A') . "\n";
            echo "User ID: " . ($data['data']['user']['id'] ?? 'N/A') . "\n";
        }
    } else {
        echo "Invalid JSON response\n";
    }
}

echo "\n";

// Test 3: User Posts
echo "Test 3: Fetching user posts for user_id: {$userId}\n";
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://{$apiHost}/user/posts?user_id={$userId}&count=5&cursor=0",
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

$response = curl_exec($curl);
$err = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Status Code: {$httpCode}\n";
if ($err) {
    echo "Error: {$err}\n";
} else {
    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Response Code: " . ($data['code'] ?? 'N/A') . "\n";
        echo "Videos Found: " . (isset($data['data']['videos']) ? count($data['data']['videos']) : '0') . "\n";
        if (isset($data['data']['videos']) && !empty($data['data']['videos'])) {
            $video = $data['data']['videos'][0];
            echo "Sample Video ID: " . ($video['video_id'] ?? 'N/A') . "\n";
            echo "Sample Video Title: " . ($video['title'] ?? 'N/A') . "\n";
            echo "Has More: " . ($data['data']['hasMore'] ? 'Yes' : 'No') . "\n";
            echo "Next Cursor: " . ($data['data']['cursor'] ?? 'N/A') . "\n";
        }
    } else {
        echo "Invalid JSON response\n";
    }
}

echo "\nTests completed.\n";