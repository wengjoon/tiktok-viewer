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
            // For simplicity, we'll use a static mapping for some common usernames
            // In a production app, you would implement a proper search functionality
            $userIdMapping = [
                'tiktok' => '107955',
                // Add more mappings if needed
            ];
            
            // If we have a direct mapping, use it
            if (isset($userIdMapping[strtolower($username)])) {
                $userId = $userIdMapping[strtolower($username)];
            } else {
                // For demo purposes, we'll use the TikTok official account
                $userId = '107955';
                // In a real implementation, you would search for the user ID here
            }
            
            $response = Http::withHeaders([
                'x-rapidapi-host' => $this->apiHost,
                'x-rapidapi-key' => $this->apiKey,
            ])->get('https://' . $this->apiHost . '/user/info', [
                'user_id' => $userId,
            ]);
            
            if ($response->successful()) {
                $userData = $response->json();
                
                Log::info('API Response: ' . json_encode(array_keys($userData)));
                
                if (isset($userData['data']) && isset($userData['data']['user'])) {
                    $user = $userData['data']['user'];
                    $stats = $userData['data']['stats'] ?? [];
                    
                    // Merge stats into user for compatibility
                    $user['followingCount'] = $stats['followingCount'] ?? 0;
                    $user['followerCount'] = $stats['followerCount'] ?? 0;
                    $user['heartCount'] = $stats['heartCount'] ?? 0;
                    
                    // In this API, user ID is used instead of secUid
                    return view('profile', [
                        'user' => $user,
                        'userId' => $userId,
                        'activeTab' => 'posts',
                        'username' => $username
                    ]);
                } else {
                    Log::warning('User data not found in API response: ' . json_encode($userData));
                    return redirect('/')->with('error', 'User not found in TikTok');
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
                
                if (isset($videoData['data']) && isset($videoData['data']['video'])) {
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

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('API Response Structure: ' . json_encode(array_keys($responseData)));
                
                if (isset($responseData['data']) && isset($responseData['data']['videos'])) {
                    // Format response to match our frontend expectations
                    return response()->json([
                        'data' => [
                            'cursor' => $responseData['data']['cursor'] ?? 0,
                            'hasMore' => $responseData['data']['hasMore'] ?? false,
                            'items' => $responseData['data']['videos']
                        ]
                    ]);
                } else {
                    Log::warning('No videos found in the response: ' . json_encode(array_keys($responseData)));
                    // Return empty data
                    return response()->json([
                        'data' => [
                            'cursor' => 0,
                            'hasMore' => false,
                            'items' => []
                        ]
                    ]);
                }
            } else {
                Log::error('User posts API request failed: ' . $response->status());
                return response()->json([
                    'error' => 'API request failed',
                    'status' => $response->status()
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception in getUserPosts: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // We don't have a specific popular posts endpoint in the new API,
    // so we'll just return regular posts for now
    public function getPopularPosts(Request $request, $userId)
    {
        return $this->getUserPosts($request, $userId);
    }
}