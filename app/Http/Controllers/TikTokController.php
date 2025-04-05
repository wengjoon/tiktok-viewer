<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokController extends Controller
{
    protected $apiHost = 'tiktok-scraper7.p.rapidapi.com';
    protected $apiKey = '185e5b8f0fmsh6ea4a6b6c76678dp1bc1c9jsn7a40d65fa697';

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query)) {
            return redirect('/')->with('error', 'Please enter a username or TikTok URL');
        }
        
        // Check if query is a URL
        if (filter_var($query, FILTER_VALIDATE_URL)) {
            // Extract video ID from URL
            preg_match('/video\/(\d+)/', $query, $matches);
            if (isset($matches[1])) {
                $videoId = $matches[1];
                return redirect('/video/' . $videoId);
            }
            return redirect()->back()->with('error', 'Invalid TikTok URL');
        } 
        // Else it's a username, redirect to username page
        else {
            return redirect('/username/' . $query);
        }
    }

    public function getUserProfile($username)
{
    Log::info('Fetching user profile for username: ' . $username);
    
    try {
        // For certain usernames, we know the user_id (for demo purposes)
        $knownUserIds = [
            'tiktok' => '107955'
        ];
        
        $params = [];
        if (isset($knownUserIds[strtolower($username)])) {
            $params['user_id'] = $knownUserIds[strtolower($username)];
            Log::info('Using known user_id: ' . $params['user_id']);
        } else {
            $params['unique_id'] = $username;
            Log::info('Using unique_id parameter: ' . $params['unique_id']);
        }
        
        $response = Http::withHeaders([
            'x-rapidapi-host' => $this->apiHost,
            'x-rapidapi-key' => $this->apiKey,
        ])->get('https://' . $this->apiHost . '/user/info', $params);
        
        Log::info('API Response Status: ' . $response->status());
        
        if ($response->successful()) {
            $userData = $response->json();
            
            Log::info('API Response Structure: ' . json_encode(array_keys($userData)));
            
            if ($userData['code'] === 0 && isset($userData['data'])) {
                // Normalize the user data structure
                $user = [];
                
                // Check different possible locations for user data
                if (isset($userData['data']['user'])) {
                    $user = $userData['data']['user'];
                } else if (isset($userData['data'])) {
                    $user = $userData['data'];
                }
                
                // Get stats from different possible locations
                $stats = [];
                if (isset($userData['data']['stats'])) {
                    $stats = $userData['data']['stats'];
                } else if (isset($user['stats'])) {
                    $stats = $user['stats'];
                }
                
                // Map the stats to the expected fields
                if (!empty($stats)) {
                    $user['followingCount'] = $stats['followingCount'] ?? $stats['following_count'] ?? 0;
                    $user['followerCount'] = $stats['followerCount'] ?? $stats['follower_count'] ?? 0;
                    $user['heartCount'] = $stats['heartCount'] ?? $stats['heart_count'] ?? $stats['digg_count'] ?? 0;
                }
                
                // If we don't have a user_id yet, get it from the response
                $userId = $user['id'] ?? null;
                if (!$userId && isset($params['user_id'])) {
                    $userId = $params['user_id'];
                }
                
                if (!$userId) {
                    Log::error('Failed to get user_id from response');
                    return redirect('/')->with('error', 'Unable to find user ID. Please try again with a different username.');
                }
                
                Log::info('Successfully retrieved user data for user_id: ' . $userId);
                
                return view('profile', [
                    'user' => $user,
                    'userId' => $userId,
                    'activeTab' => 'posts',
                    'username' => $username
                ]);
            } else {
                // For demo purposes, use tiktok's official account
                if ($username !== 'tiktok') {
                    Log::warning('User not found, redirecting to tiktok official account as fallback');
                    return redirect('/username/tiktok')->with('warning', 'User not found. Showing TikTok official account instead.');
                } else {
                    Log::error('API returned error or unexpected format: ' . json_encode($userData));
                    return redirect('/')->with('error', 'Unable to fetch TikTok user. API might be down.');
                }
            }
        } else {
            Log::error('API request failed: ' . $response->status() . ' - ' . $response->body());
            return redirect('/')->with('error', 'Failed to fetch user data. API error: ' . $response->status());
        }
    } catch (\Exception $e) {
        Log::error('Exception in getUserProfile: ' . $e->getMessage());
        return redirect('/')->with('error', 'An error occurred: ' . $e->getMessage());
    }
}

    public function getVideo($videoId)
    {
        try {
            $response = Http::withHeaders([
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key' => $this->apiKey,
            ])->get('https://' . $this->apiHost . '/video/info', [
                'video_id' => $videoId,
            ]);
            
            if ($response->successful()) {
                $videoData = $response->json();
                
                if ($videoData['code'] === 0 && isset($videoData['data']) && isset($videoData['data']['video'])) {
                    $video = $videoData['data']['video'];
                    $author = $videoData['data']['author'] ?? [];
                    
                    return view('video', [
                        'video' => $video,
                        'author' => $author,
                        'videoId' => $videoId
                    ]);
                } else {
                    return redirect('/')->with('error', 'Video not found');
                }
            } else {
                return redirect('/')->with('error', 'Failed to fetch video data');
            }
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function getUserPosts(Request $request, $userId)
    {
        $cursor = $request->input('cursor', 0);
        $count = $request->input('count', 10);
        
        Log::info("Fetching user posts for userId: {$userId}, cursor: {$cursor}, count: {$count}");
        
        try {
            $response = Http::withHeaders([
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key' => $this->apiKey,
            ])->get('https://' . $this->apiHost . '/user/posts', [
                'user_id' => $userId,
                'count' => $count,
                'cursor' => $cursor,
            ]);

            Log::info('Posts API Response Status: ' . $response->status());

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('Posts API Response Structure: ' . json_encode(array_keys($responseData)));
                
                if ($responseData['code'] === 0 && isset($responseData['data']) && isset($responseData['data']['videos'])) {
                    return response()->json([
                        'videos' => $responseData['data']['videos'],
                        'cursor' => $responseData['data']['cursor'] ?? $cursor + $count,
                        'hasMore' => $responseData['data']['hasMore'] ?? false
                    ]);
                } else {
                    Log::warning('No videos found in the response: ' . json_encode($responseData));
                    // Return empty data
                    return response()->json([
                        'videos' => [],
                        'cursor' => $cursor,
                        'hasMore' => false
                    ]);
                }
            } else {
                Log::error('User posts API request failed: ' . $response->status() . ' - ' . $response->body());
                return response()->json([
                    'videos' => [],
                    'cursor' => $cursor,
                    'hasMore' => false,
                    'error' => 'API request failed: ' . $response->status()
                ], 200); // Still return 200 to handle error in frontend
            }
        } catch (\Exception $e) {
            Log::error('Exception in getUserPosts: ' . $e->getMessage());
            return response()->json([
                'videos' => [],
                'cursor' => $cursor,
                'hasMore' => false,
                'error' => $e->getMessage()
            ], 200); // Still return 200 to handle error in frontend
        }
    }
    
    public function getPopularPosts(Request $request, $userId)
    {
        // Since there's no specific popular posts endpoint,
        // we'll use the user posts endpoint for now
        return $this->getUserPosts($request, $userId);
    }
}