<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anonymous TikTok Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #000;
            color: #fff;
        }
        .tiktok-logo {
            font-size: 2.5rem;
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
    </style>
</head>
<body>
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        <h1 class="tiktok-logo mb-12">TikViewer</h1>
        <p class="text-gray-400 mb-8 text-center max-w-lg">Watch TikTok videos anonymously. Enter a username or paste a TikTok video URL.</p>
        
        <form action="/search" method="GET" class="w-full max-w-lg">
            <div class="search-container flex items-center p-2 w-full mb-8">
                <input 
                    type="text" 
                    name="query" 
                    placeholder="Search username or paste video URL" 
                    class="search-input w-full px-4 py-3 text-white"
                    required
                >
                <button type="submit" class="search-btn px-6 py-3 ml-2 flex items-center">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </form>
        
        @if(session('error'))
            <div class="mt-4 text-red-500">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="mt-8 text-center text-gray-400 text-sm">
            <p>Watch TikTok videos without logging in or being tracked.</p>
            <p class="mt-4">Demo Note: Currently only the TikTok official account (username: tiktok) is fully supported for demonstration purposes.</p>
        </div>
    </div>
</body>
</html>