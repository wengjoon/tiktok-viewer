<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch TikTok Video | Anonymous TikTok Viewer</title>
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
        .video-container {
            max-width: 500px;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
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
        
        <!-- Video Player -->
        <div class="flex-grow p-6">
            <div class="video-container bg-gray-900">
                <!-- Embedding the TikTok video player -->
                <div class="aspect-w-9 aspect-h-16 relative">
                    <iframe 
                        src="https://www.tiktok.com/embed/v2/{{ $videoId }}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                        class="absolute inset-0 w-full h-full"
                    ></iframe>
                </div>
            </div>
            
            <div class="mt-6 max-w-lg mx-auto">
                <div class="flex items-center justify-between">
                    <a href="/" class="text-white hover:text-gray-200">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Home
                    </a>
                    
                    <div class="flex space-x-4">
                        <button class="text-white hover:text-gray-200">
                            <i class="fas fa-share mr-1"></i> Share
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>