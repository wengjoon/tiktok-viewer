<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user['nickname'] ?? $user['uniqueId'] ?? $username }} | Anonymous TikTok Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #000;
            color: #fff;
        }
        .tiktok-logo {
            font-size: 1.8rem;
            background: linear-gradient(90deg, #25F4EE, #FE2C55);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: bold;
        }
        .search-container {
            background-color: #1F1F1F;
            border-radius: 92px;
        }
        .search-input {
            background-color: transparent;
            outline: none;
        }
        .search-btn {
            background: linear-gradient(90deg, #25F4EE, #FE2C55);
            color: white;
            border-radius: 92px;
        }
        .tab-active {
            border-bottom: 2px solid #FE2C55;
            color: #FE2C55;
        }
        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .video-card {
            background-color: #1F1F1F;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .video-card:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .video-thumbnail {
            position: relative;
            overflow: hidden;
        }

        .video-thumbnail img {
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .video-card:hover .video-thumbnail img {
            transform: scale(1.05);
        }

        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 255, 255, 0.2);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0.8;
            transition: all 0.2s ease;
        }

        .play-button i {
            color: white;
            font-size: 24px;
        }

        .video-card:hover .play-button {
            background-color: rgba(255, 255, 255, 0.4);
            opacity: 1;
            transform: translate(-50%, -50%) scale(1.1);
        }

        .video-thumbnail::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(0deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 40%);
            pointer-events: none;
        }

        /* Line clamp for descriptions */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .loading-spinner {
            border-top-color: #FE2C55;
            border-left-color: #FE2C55;
        }

        /* Video Popup Styles */
        .video-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            z-index: 50;
            display: none;
            justify-content: center;
            align-items: center;
            overflow-y: auto;
        }
        
        .video-popup-container {
            position: relative;
            width: 100%;
            max-width: 420px;
            height: calc(100vh - 80px);
            background-color: #000;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .video-player-container {
            position: relative;
            width: 100%;
            flex-grow: 1;
            background-color: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .video-player {
            width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .video-loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 5;
        }
        
        .video-loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #FE2C55;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .video-info {
            padding: 15px;
            background-color: #121212;
            border-top: 1px solid #2a2a2a;
        }
        
        .close-popup {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: rgba(0, 0, 0, 0.6);
            color: white;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 60;
        }
                
        /* Video controls */
        .video-controls {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            display: flex;
            align-items: center;
            z-index: 6;
        }
        
        .play-pause-btn {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            margin-right: 10px;
            cursor: pointer;
        }
        
        .video-progress {
            flex-grow: 1;
            height: 4px;
            background-color: rgba(255, 255, 255, 0.3);
            cursor: pointer;
            position: relative;
        }
        
        .video-progress-filled {
            height: 100%;
            background-color: #FE2C55;
            width: 0%;
        }
        
        .volume-control {
            margin-left: 10px;
            display: flex;
            align-items: center;
        }
        
        .volume-btn {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        
        .volume-slider {
            width: 60px;
            height: 4px;
            background-color: rgba(255, 255, 255, 0.3);
            margin-left: 5px;
            cursor: pointer;
            position: relative;
        }
        
        .volume-filled {
            height: 100%;
            background-color: white;
            width: 100%;
        }
        
        .fullscreen-btn {
            background: none;
            border: none;
            color: white;
            font-size: 16px;
            margin-left: 10px;
            cursor: pointer;
        }
        
        .error-message {
            color: #FE2C55;
            text-align: center;
            padding: 20px;
        }

        /* Image Carousel Styles */
        .image-container {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #000;
        }

        .image-carousel {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .carousel-item {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .carousel-item.active {
            opacity: 1;
            z-index: 1;
        }

        .carousel-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .carousel-nav:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .carousel-nav.prev {
            left: 10px;
        }

        .carousel-nav.next {
            right: 10px;
        }

        .carousel-indicators {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            gap: 8px;
            z-index: 10;
        }

        .indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .indicator.active, .indicator:hover {
            background-color: white;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="py-4 px-6 border-b border-gray-800 flex items-center justify-between">
            <a href="/" class="tiktok-logo">TikViewer</a>
            
            <form action="/search" method="GET" class="w-full max-w-xl mx-4">
                <div class="search-container flex items-center p-1 w-full">
                    <input 
                        type="text" 
                        name="query" 
                        placeholder="Search username or paste video URL" 
                        class="search-input w-full px-4 py-2 text-white text-sm"
                        required
                    >
                    <button type="submit" class="search-btn px-4 py-2 ml-1 text-sm">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <div>
                <!-- Optional: Add theme toggle or other controls here -->
            </div>
        </header>
        
        @if(session('warning'))
            <div class="bg-yellow-800 border border-yellow-700 text-yellow-200 px-4 py-3 my-4 mx-6 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('warning') }}</span>
            </div>
        @endif
        
        <!-- Profile Info -->
        <div class="px-6 py-8 border-b border-gray-800">
            <div class="flex items-center">
                <img 
                    src="{{ $user['avatar'] ?? 'https://via.placeholder.com/150' }}" 
                    alt="{{ $user['nickname'] ?? $user['uniqueId'] ?? $username }}" 
                    class="w-24 h-24 rounded-full border-2 border-gray-700"
                >
                <div class="ml-6">
                    <h1 class="text-2xl font-bold mb-1">{{ $user['nickname'] ?? $user['uniqueId'] ?? $username }}</h1>
                    <p class="text-gray-400 mb-3">{{ '@' . ($user['uniqueId'] ?? $username) }}</p>
                    <div class="flex space-x-6 text-sm">
                        <div>
                            <span class="font-bold">{{ number_format($user['followingCount'] ?? 0) }}</span>
                            <span class="text-gray-400 ml-1">Following</span>
                        </div>
                        <div>
                            <span class="font-bold">{{ number_format($user['followerCount'] ?? 0) }}</span>
                            <span class="text-gray-400 ml-1">Followers</span>
                        </div>
                        <div>
                            <span class="font-bold">{{ number_format($user['heartCount'] ?? 0) }}</span>
                            <span class="text-gray-400 ml-1">Likes</span>
                        </div>
                    </div>
                </div>
            </div>
            @if(isset($user['signature']) && $user['signature'])
                <p class="mt-4 text-gray-300">{{ $user['signature'] }}</p>
            @endif
        </div>
        
        <!-- Tabs -->
        <div class="border-b border-gray-800">
            <div class="flex">
                <button 
                    id="postsTab" 
                    class="py-4 px-6 text-center font-medium text-sm {{ $activeTab == 'posts' ? 'tab-active' : 'text-gray-400' }}"
                    onclick="switchTab('posts')"
                >
                    User Posts
                </button>
                <button 
                    id="popularTab" 
                    class="py-4 px-6 text-center font-medium text-sm {{ $activeTab == 'popular' ? 'tab-active' : 'text-gray-400' }}"
                    onclick="switchTab('popular')"
                >
                    Popular Posts
                </button>
            </div>
        </div>
        
        <!-- Videos Grid -->
        <div class="flex-grow p-6">
            <div id="videosContainer" class="video-grid">
                <!-- Videos will be loaded here -->
            </div>
            
            <div id="loadingIndicator" class="flex justify-center my-8 hidden">
                <div class="loading-spinner w-8 h-8 border-4 border-gray-700 border-solid rounded-full animate-spin"></div>
            </div>
            
            <div id="loadMoreContainer" class="flex justify-center my-8 hidden">
                <button 
                    id="loadMoreBtn" 
                    class="bg-gray-800 hover:bg-gray-700 text-white py-2 px-6 rounded-full"
                >
                    Load More
                </button>
            </div>
        </div>
    </div>

    <!-- Video Popup Modal -->
    <div id="videoPopup" class="video-popup-overlay">
        <div class="close-popup" onclick="closeVideoPopup()">
            <i class="fas fa-times"></i>
        </div>
        <div class="video-popup-container">
            <div id="videoPlayerContainer" class="video-player-container">
                <!-- Custom video player will be inserted here -->
                <div id="videoLoading" class="video-loading">
                    <div class="video-loading-spinner"></div>
                </div>
                
                <video id="videoPlayer" class="video-player" controlsList="nodownload" playsinline></video>
                
                <!-- Add image container for photo posts -->
                <div id="imageContainer" class="image-container" style="display: none;"></div>
                
                <div id="videoError" class="error-message hidden">
                    Unable to load video. The API may have restrictions.
                </div>
                
                <div class="video-controls">
                    <button class="play-pause-btn" id="playPauseBtn">
                        <i class="fas fa-play"></i>
                    </button>
                    
                    <div class="video-progress" id="videoProgress">
                        <div class="video-progress-filled" id="videoProgressFilled"></div>
                    </div>
                    
                    <div class="volume-control">
                        <button class="volume-btn" id="volumeBtn">
                            <i class="fas fa-volume-up"></i>
                        </button>
                        <div class="volume-slider" id="volumeSlider">
                            <div class="volume-filled" id="volumeFilled"></div>
                        </div>
                    </div>
                    
                    <button class="fullscreen-btn" id="fullscreenBtn">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
            </div>
            <div id="videoInfo" class="video-info">
                <!-- Video info will be inserted here -->
            </div>
        </div>
    </div>

    <script>
        let currentCursor = 0;
        let isLoading = false;
        let activeTab = '{{ $activeTab }}';
        const userId = '{{ $userId }}';
        let currentVideos = []; // Store all loaded videos
        let videoPlayer = null;
        let currentSlide = 0;
        
        // Load videos when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadVideos();
            
            // Initialize video player
            videoPlayer = document.getElementById('videoPlayer');
            
            // Set up video player event listeners
            setupVideoPlayer();
            
            // Add infinite scroll
            window.addEventListener('scroll', function() {
                if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
                    if (!isLoading) {
                        loadVideos();
                    }
                }
            });
        });
        
        function switchTab(tab) {
            activeTab = tab;
            
            // Update UI
            if (tab === 'posts') {
                document.getElementById('postsTab').classList.add('tab-active');
                document.getElementById('postsTab').classList.remove('text-gray-400');
                document.getElementById('popularTab').classList.remove('tab-active');
                document.getElementById('popularTab').classList.add('text-gray-400');
            } else {
                document.getElementById('popularTab').classList.add('tab-active');
                document.getElementById('popularTab').classList.remove('text-gray-400');
                document.getElementById('postsTab').classList.remove('tab-active');
                document.getElementById('postsTab').classList.add('text-gray-400');
            }
            
            // Reset videos and load new ones
            document.getElementById('videosContainer').innerHTML = '';
            currentVideos = [];
            currentCursor = 0;
            loadVideos();
        }
        
        // Update the processVideo function to correctly extract thumbnail URLs
function processVideo(video) {
    console.log('Processing video:', video);
    
    // Get video ID
    const videoId = video.video_id || video.id || video.aweme_id || '';
    
    // Get video description
    let videoDesc = video.desc || video.title || 'No description';
    
    // Get video cover image - try different possible fields
    // Default to an empty string if none are found
    let coverImage = '';
    
    // For images post
    if (video.images && video.images.length > 0) {
        coverImage = video.images[0];
    } else {
        // For video post - check all possible cover image fields
        if (video.cover) {
            coverImage = video.cover;
        } else if (video.origin_cover) {
            coverImage = video.origin_cover;
        } else if (video.ai_dynamic_cover) {
            coverImage = video.ai_dynamic_cover;
        } else if (video.thumbnail_url) {
            coverImage = video.thumbnail_url;
        } else if (video.thumbnail) {
            coverImage = video.thumbnail;
        } else if (video.covers && video.covers.length > 0) {
            coverImage = video.covers[0];
        } else {
            coverImage = 'https://via.placeholder.com/300x400?text=No+Preview';
        }
    }
    
    // For debugging
    console.log('Extracted thumbnail URL:', coverImage);
    
    // Get video URL - try different possible fields
    const videoUrl = video.play || 
                    video.play_url || 
                    video.download_url || 
                    (video.video && video.video.play_addr && video.video.play_addr.url_list && 
                     video.video.play_addr.url_list.length > 0 ? video.video.play_addr.url_list[0] : '') || 
                    '';
    
    // Get stats - ensure we have defaults in case fields don't exist
    const playCount = parseInt(video.play_count || '0', 10);
    const likeCount = parseInt(video.digg_count || '0', 10);
    const commentCount = parseInt(video.comment_count || '0', 10);
    const shareCount = parseInt(video.share_count || '0', 10);
    
    // Get author info
    const author = video.author || {};
    const authorName = author.nickname || video.author_name || '';
    const authorAvatar = author.avatar || video.author_avatar || '';
    const authorUniqueId = author.unique_id || video.author_unique_id || '';
    
    // Determine if this is an image post or video post
    const isImagePost = video.images && video.images.length > 0;
    
    return {
        id: videoId,
        description: videoDesc,
        coverImage: coverImage,
        videoUrl: videoUrl,
        isImagePost: isImagePost,
        images: video.images || [],
        stats: {
            playCount,
            likeCount,
            commentCount,
            shareCount
        },
        author: {
            name: authorName,
            avatar: authorAvatar,
            uniqueId: authorUniqueId
        },
        createTime: video.create_time || 0
    };
}
        
        function loadVideos() {
            if (isLoading) return;
            
            isLoading = true;
            document.getElementById('loadingIndicator').classList.remove('hidden');
            document.getElementById('loadMoreContainer').classList.add('hidden');
            
            const endpoint = activeTab === 'posts' 
                ? `/api/user/${userId}/videos` 
                : `/api/user/${userId}/popular`;
            
            console.log(`Loading videos from ${endpoint}?cursor=${currentCursor}`);
            
            fetch(`${endpoint}?cursor=${currentCursor}`)
                .then(response => response.json())
                .then(data => {
                    isLoading = false;
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    
                    console.log('API Response:', data);
                    
                    if (data.videos && Array.isArray(data.videos) && data.videos.length > 0) {
                        // Process and render videos
                        renderVideos(data.videos);
                        
                        // Update cursor for next page
                        if (data.hasMore) {
                            currentCursor = data.cursor;
                            document.getElementById('loadMoreContainer').classList.remove('hidden');
                        } else {
                            document.getElementById('loadMoreContainer').classList.add('hidden');
                        }
                    } else {
                        // No videos or end of results
                        document.getElementById('loadMoreContainer').classList.add('hidden');
                        if (currentCursor === 0) {
                            document.getElementById('videosContainer').innerHTML = `
                                <div class="col-span-full text-center py-8">
                                    <p class="text-gray-400">No videos found for this account.</p>
                                </div>
                            `;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading videos:', error);
                    isLoading = false;
                    document.getElementById('loadingIndicator').classList.add('hidden');
                    document.getElementById('loadMoreContainer').classList.add('hidden');
                    
                    if (currentCursor === 0) {
                        document.getElementById('videosContainer').innerHTML = `
                            <div class="col-span-full text-center py-8">
                                <p class="text-gray-400">Failed to load videos. Please try again later.</p>
                            </div>
                        `;
                    }
                });
        }
        
        // Update the renderVideos function to ensure thumbnails display correctly
function renderVideos(videos) {
    const container = document.getElementById('videosContainer');
    
    videos.forEach(video => {
        // Process the video to normalize data structure
        const processedVideo = processVideo(video);
        currentVideos.push(processedVideo);
        
        // Create video card
        const videoCard = document.createElement('div');
        videoCard.className = 'video-card';
        videoCard.onclick = () => openVideoPopup(processedVideo.id);
        
        // Format stats
        const formattedViews = formatNumber(processedVideo.stats.playCount);
        const formattedLikes = formatNumber(processedVideo.stats.likeCount);
        
        // Debug the thumbnail URL
        console.log(`Rendering video ${processedVideo.id} with thumbnail: ${processedVideo.coverImage}`);
        
        // Check if we have images (for photo posts) or just a regular video
        if (video.images && video.images.length > 0) {
            // This is a photo post
            videoCard.innerHTML = `
                <div class="video-thumbnail relative pb-[177%]">
                    <img 
                        src="${video.images[0]}" 
                        alt="${processedVideo.description}" 
                        class="absolute top-0 left-0 w-full h-full object-cover"
                        loading="lazy"
                    >
                    <div class="absolute top-2 right-2 bg-black bg-opacity-50 px-2 py-1 rounded-full">
                        <i class="fas fa-images text-white"></i>
                    </div>
                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 px-2 py-1 rounded text-xs text-white">
                        <i class="fas fa-heart mr-1"></i>${formattedLikes}
                    </div>
                </div>
                <div class="p-3">
                    <p class="text-sm line-clamp-2">${processedVideo.description}</p>
                </div>
            `;
        } else {
            // This is a regular video post
            videoCard.innerHTML = `
                <div class="video-thumbnail relative pb-[177%]">
                    <img 
                        src="${processedVideo.coverImage}" 
                        alt="${processedVideo.description}" 
                        class="absolute top-0 left-0 w-full h-full object-cover"
                        onerror="this.onerror=null; this.src='https://via.placeholder.com/300x400?text=Thumbnail+Error';"
                        loading="lazy"
                    >
                    <div class="play-button">
                        <i class="fas fa-play"></i>
                    </div>
                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 px-2 py-1 rounded text-xs text-white">
                        <i class="fas fa-play mr-1"></i>${formattedViews}
                    </div>
                </div>
                <div class="p-3">
                    <p class="text-sm line-clamp-2">${processedVideo.description}</p>
                </div>
            `;
        }
        
        container.appendChild(videoCard);
    });
}
        
        function formatNumber(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return num.toString();
        }
        
        function openVideoPopup(videoId) {
            // Find the video in our loaded videos
            const video = currentVideos.find(v => v.id === videoId);
            if (!video) return;
            
            // Show popup
            const popup = document.getElementById('videoPopup');
            popup.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            
            // Reset player
            const videoPlayer = document.getElementById('videoPlayer');
            videoPlayer.pause();
            videoPlayer.src = '';
            videoPlayer.style.display = 'none';
            
            // Get container for image slideshow (if needed)
            const imageContainer = document.getElementById('imageContainer');
            if (imageContainer) {
                imageContainer.style.display = 'none';
                imageContainer.innerHTML = '';
            }
            
            // Show loading spinner
            document.getElementById('videoLoading').style.display = 'flex';
            document.getElementById('videoError').classList.add('hidden');
            
            // Update video info
            document.getElementById('videoInfo').innerHTML = `
                <div class="flex items-center mb-3">
                    <img 
                        src="${video.author.avatar || 'https://via.placeholder.com/50'}" 
                        alt="${video.author.name}" 
                        class="w-10 h-10 rounded-full mr-3"
                    >
                    <div>
                        <p class="font-semibold">${video.author.name || 'Unknown'}</p>
                        <p class="text-gray-400 text-sm">@${video.author.uniqueId || 'user'}</p>
                    </div>
                </div>
                <p class="text-sm mb-3">${video.description}</p>
                <div class="flex space-x-4 text-sm text-gray-400">
                    <div><i class="fas fa-play mr-1"></i>${formatNumber(video.stats.playCount)}</div>
                    <div><i class="fas fa-heart mr-1"></i>${formatNumber(video.stats.likeCount)}</div>
                    <div><i class="fas fa-comment mr-1"></i>
                    ${formatNumber(video.stats.commentCount)}</div>
                    <div><i class="fas fa-share mr-1"></i>${formatNumber(video.stats.shareCount)}</div>
                </div>
            `;
            
            // Check if this is an image post or video post
            if (video.isImagePost && video.images && video.images.length > 0) {
                // This is an image post - create image carousel
                if (imageContainer) {
                    videoPlayer.style.display = 'none';
                    imageContainer.style.display = 'block';
                    imageContainer.innerHTML = '';
                    
                    // Create image carousel
                    const carousel = document.createElement('div');
                    carousel.className = 'image-carousel';
                    
                    video.images.forEach((imageUrl, index) => {
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'carousel-item' + (index === 0 ? ' active' : '');
                        
                        const img = document.createElement('img');
                        img.src = imageUrl;
                        img.alt = `Image ${index + 1}`;
                        img.className = 'carousel-image';
                        
                        imgDiv.appendChild(img);
                        carousel.appendChild(imgDiv);
                    });
                    
                    // Add navigation buttons if there are multiple images
                    if (video.images.length > 1) {
                        const prevBtn = document.createElement('button');
                        prevBtn.className = 'carousel-nav prev';
                        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
                        prevBtn.onclick = (e) => {
                            e.stopPropagation();
                            navigateCarousel(-1);
                        };
                        
                        const nextBtn = document.createElement('button');
                        nextBtn.className = 'carousel-nav next';
                        nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
                        nextBtn.onclick = (e) => {
                            e.stopPropagation();
                            navigateCarousel(1);
                        };
                        
                        // Add indicators for multiple images
                        const indicators = document.createElement('div');
                        indicators.className = 'carousel-indicators';
                        
                        for (let i = 0; i < video.images.length; i++) {
                            const dot = document.createElement('span');
                            dot.className = 'indicator' + (i === 0 ? ' active' : '');
                            dot.onclick = (e) => {
                                e.stopPropagation();
                                goToSlide(i);
                            };
                            indicators.appendChild(dot);
                        }
                        
                        imageContainer.appendChild(carousel);
                        imageContainer.appendChild(prevBtn);
                        imageContainer.appendChild(nextBtn);
                        imageContainer.appendChild(indicators);
                    } else {
                        // Just add the carousel for a single image
                        imageContainer.appendChild(carousel);
                    }
                    
                    document.getElementById('videoLoading').style.display = 'none';
                }
            } else {
                // This is a video post - use the video player
                if (imageContainer) {
                    imageContainer.style.display = 'none';
                }
                
                if (video.videoUrl) {
                    videoPlayer.src = video.videoUrl;
                    videoPlayer.load();
                    
                    videoPlayer.oncanplay = function() {
                        document.getElementById('videoLoading').style.display = 'none';
                        videoPlayer.style.display = 'block';
                        videoPlayer.play().catch(e => {
                            console.error('Video play failed:', e);
                            // Many browsers require user interaction for autoplay
                        });
                    };
                    
                    videoPlayer.onerror = function() {
                        document.getElementById('videoLoading').style.display = 'none';
                        document.getElementById('videoError').classList.remove('hidden');
                    };
                } else {
                    document.getElementById('videoLoading').style.display = 'none';
                    document.getElementById('videoError').classList.remove('hidden');
                }
            }
        }
        
        function closeVideoPopup() {
            const popup = document.getElementById('videoPopup');
            popup.style.display = 'none';
            document.body.style.overflow = ''; // Restore scrolling
            
            // Pause video
            const videoPlayer = document.getElementById('videoPlayer');
            videoPlayer.pause();
            videoPlayer.src = '';
            
            // Reset currentSlide for carousel
            currentSlide = 0;
        }
        
        // Carousel navigation functions
        function navigateCarousel(direction) {
            const items = document.querySelectorAll('.carousel-item');
            const indicators = document.querySelectorAll('.indicator');
            
            if (items.length === 0) return;
            
            // Remove active class from current slide
            items[currentSlide].classList.remove('active');
            if (indicators.length > 0) {
                indicators[currentSlide].classList.remove('active');
            }
            
            // Calculate new slide index
            currentSlide = (currentSlide + direction + items.length) % items.length;
            
            // Add active class to new slide
            items[currentSlide].classList.add('active');
            if (indicators.length > 0) {
                indicators[currentSlide].classList.add('active');
            }
        }

        function goToSlide(index) {
            const items = document.querySelectorAll('.carousel-item');
            const indicators = document.querySelectorAll('.indicator');
            
            if (items.length === 0 || index < 0 || index >= items.length) return;
            
            // Remove active class from all slides and indicators
            items.forEach(item => item.classList.remove('active'));
            indicators.forEach(indicator => indicator.classList.remove('active'));
            
            // Add active class to selected slide and indicator
            items[index].classList.add('active');
            indicators[index].classList.add('active');
            
            // Update current slide
            currentSlide = index;
        }
        
        function setupVideoPlayer() {
            const video = document.getElementById('videoPlayer');
            const playPauseBtn = document.getElementById('playPauseBtn');
            const progress = document.getElementById('videoProgress');
            const progressFilled = document.getElementById('videoProgressFilled');
            const volumeBtn = document.getElementById('volumeBtn');
            const volumeSlider = document.getElementById('volumeSlider');
            const volumeFilled = document.getElementById('volumeFilled');
            const fullscreenBtn = document.getElementById('fullscreenBtn');
            
            // Play/Pause
            playPauseBtn.addEventListener('click', togglePlay);
            video.addEventListener('click', togglePlay);
            
            function togglePlay() {
                if (video.paused) {
                    video.play();
                    playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                } else {
                    video.pause();
                    playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                }
            }
            
            // Update progress bar
            video.addEventListener('timeupdate', () => {
                const percent = (video.currentTime / video.duration) * 100;
                progressFilled.style.width = `${percent}%`;
            });
            
            // Click on progress bar
            progress.addEventListener('click', (e) => {
                const progressTime = (e.offsetX / progress.offsetWidth) * video.duration;
                video.currentTime = progressTime;
            });
            
            // Volume
            volumeBtn.addEventListener('click', () => {
                video.muted = !video.muted;
                updateVolumeIcon();
            });
            
            volumeSlider.addEventListener('click', (e) => {
                const volume = e.offsetX / volumeSlider.offsetWidth;
                video.volume = volume;
                volumeFilled.style.width = `${volume * 100}%`;
                updateVolumeIcon();
            });
            
            function updateVolumeIcon() {
                if (video.muted || video.volume === 0) {
                    volumeBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
                    volumeFilled.style.width = '0%';
                } else if (video.volume < 0.5) {
                    volumeBtn.innerHTML = '<i class="fas fa-volume-down"></i>';
                } else {
                    volumeBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
                }
            }
            
            // Fullscreen
            fullscreenBtn.addEventListener('click', () => {
                const videoContainer = document.getElementById('videoPlayerContainer');
                
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else {
                    videoContainer.requestFullscreen().catch(err => {
                        console.error('Fullscreen error:', err);
                    });
                }
            });
            
            // Play/Pause icon update
            video.addEventListener('play', () => {
                playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
            });
            
            video.addEventListener('pause', () => {
                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
            });
            
            // Fullscreen change detection
            document.addEventListener('fullscreenchange', () => {
                if (document.fullscreenElement) {
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
                } else {
                    fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
                }
            });
        }
        
        // Listen for load more button clicks
        document.getElementById('loadMoreBtn').addEventListener('click', loadVideos);
        
        // Close popup when clicking ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeVideoPopup();
            }
        });
    </script>
</body>
</html>