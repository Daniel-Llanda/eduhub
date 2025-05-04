<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Management</title>

    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    <link rel="icon" type="image/png" href="{{ asset('east_logo.png') }}">
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
        * {
            scrollbar-width: none;
        }
        *::-webkit-scrollbar {
            display: none;
        }
    </style>

    <script>
        function toggleMenu() {
            let menu = document.getElementById("mobile-menu");
            menu.classList.toggle("hidden");

            if (!menu.classList.contains("hidden")) {
                menu.classList.add("flex");
            } else {
                menu.classList.remove("flex");
            }
        }
    </script>
</head>
<body class="bg-white text-gray-900">
    <nav class="fixed top-0 left-0 w-full bg-white shadow-md py-4 px-6 flex justify-center z-50">
        <div class="flex items-center justify-between w-4/5">
            <div class="text-xl font-bold text-green-600 flex gap-1 items-center">
                <img src="{{asset('east_logo.png')}}" alt="Logo" class="w-10 h-10">
                <span class="text-base">EPCST EduHub</span>
            </div>
            <div class="flex justify-center items-center gap-3">
                <div class="hidden md:flex space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 hover:text-green-600 underline">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-1 text-green-600 border border-green-600 rounded-md hover:bg-green-500 hover:text-white">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700">Register</a>
                        @endif
                    @endauth
                </div>
                <button class="md:hidden text-green-600" onclick="toggleMenu()">☰</button>
            </div>
        </div>

        <div id="mobile-menu" class="hidden absolute top-14 left-0 w-full bg-white shadow-md p-4 flex-col space-y-2">
            @auth
                <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 hover:text-green-600 underline">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="text-center px-4 py-2 text-green-600 border border-green-600 rounded-md hover:bg-green-500 hover:text-white">Log in</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">Register</a>
                @endif
            @endauth
        </div>
    </nav>

    <div class="mt-16"></div>

    <section class="py-16 text-center px-4">
        <h2 class="text-2xl font-bold text-center mb-6 text-green-600">Works</h2>
        <div class="max-w-4xl mx-auto grid grid-cols-1 gap-6">
            
            @if ($posts->isNotEmpty())
                <div class="grid sm:grid-cols-3 grid-cols-1 gap-2">  
                    @foreach ($posts as $post)
                        @php
                            $tagsArray = explode(',', $post->tags);
                            $tagClasses = implode(' ', array_map(fn($tag) => strtolower(trim($tag)), $tagsArray));
                        @endphp
                    
                        <div class="post-item {{ $tagClasses }}">
                            <div class="bg-gray-100 shadow-md p-4 rounded-lg dark:bg-dark-eval-1 flex flex-col gap-2">
                                <div class="flex justify-between items-center ">
                                    <p>{{ $post->user->name }}</p>
                                    <p>{{ number_format($post->avg_rating, 1) }}/10</p>
                                </div>
                                <div class="p-4 rounded-lg text-center bg-gray-200"> 
                                    <a href="{{ route('login') }}">
                                        <img src="{{ asset('uploads/' . $post->thumbnail) }}" alt="Latest Upload" 
                                        class="w-28 h-28 object-cover mx-auto rounded-lg  {{ $post->pricing == 'exclusive' ? 'blur-md' : '' }}">
                                    </a>
                                    
                                </div>
                                <p class="font-bold text-center">{{ $post->title }}</p>
                                <div class="flex justify-between items-center">
                                    <p class="rounded-md px-2 text-sm text-right uppercase {{ $post->pricing == 'exclusive' ? 'text-blue-700 bg-blue-200' : 'text-green-700 bg-green-200' }}">{{ $post->pricing }}</p>
                                    <div class="flex items-center flex-row gap-1">
                                        
                                        @php
                                            $isFavorite = in_array($post->id, json_decode(auth()->user()->favorites ?? '[]', true));
                                        @endphp
        
        
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                        </svg>
                                        {{ $post->total_engagement }}
                                    </div>
                                </div>
                                <p class="text-sm">Tags:</p>
                                <div class="flex flex-wrap gap-2 mt-1">
                                    @foreach ($tagsArray as $tag)
                                        <span class="bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded-lg">
                                            {{ trim($tag) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="col-span-3 text-gray-500">No post uploaded yet.</p>
            @endif

        </div>
    </section>

    <section class="py-12 text-center px-4">
        <div class="top-users">
            <h2 class="text-2xl font-bold text-center mb-6 text-green-600">Top Creator</h2>
            <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
                @if ($topUsers->isEmpty())
                    <p class="col-span-3 text-gray-500">No creators found.</p>
                @else
                    @foreach ($topUsers as $user)
                        <div class="bg-gray-100 shadow-md p-6 rounded-md border-t-4 border-green-600">
                            <h2 class="text-xl font-semibold">{{ $user->name }} - {{ $user->posts_count }} posts</h2>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <section class="py-12 max-w-4xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-center mb-6 text-green-600">Best Rated</h2>
        <div class="overflow-x-auto">
            @if ($topRatedPosts->isEmpty())
                <p class="text-center text-gray-500">No rated posts available.</p>
            @else
                <table class="w-full bg-white shadow-md rounded-md">
                    <thead class="bg-green-600 text-white">
                        <tr>
                            <th class="py-3 px-6">Rank</th>
                            <th class="py-3 px-6 text-left">Creator</th>
                            <th class="py-3 px-6 text-left">Post Title</th>
                            <th class="py-3 px-6 text-center">Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topRatedPosts as $index => $post)
                            <tr class="border-b border-gray-200">
                                <td class="py-3 px-6 text-center">{{ $index + 1 }}</td>
                                <td class="py-3 px-6">{{ $post->creator_name }}</td>
                                <td class="py-3 px-6">{{ $post->title }}</td>
                                <td class="py-3 px-6 text-center">{{ number_format($post->avg_rating, 1) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>

    <footer class="bg-white text-gray-500 text-center py-4">
        &copy; 2025 File Management. All rights reserved.
    </footer>
</body>
</html>
