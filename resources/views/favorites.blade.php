<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{ __('Favorites') }}
            </h2>
            
        </div>
    </x-slot>
    @if ($posts->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mt-2">  
            @foreach ($posts as $post)
                @php
                    $tagsArray = explode(',', $post->tags);
                    $tagClasses = implode(' ', array_map(fn($tag) => strtolower(trim($tag)), $tagsArray));
                @endphp
            
                <div class="post-item {{ $tagClasses }} all">
                    <div class="p-4 rounded-lg bg-white dark:bg-dark-eval-1 flex flex-col gap-2">
                        <div class="flex justify-between items-center ">
                            <p>{{ $post->user->name }}</p>
                            <p>{{ number_format($post->avg_rating, 1) }}/10</p>
                        </div>
                        {{-- <p class="text-gray-200 text-md text-right uppercase mb-2">{{ $post->pricing }}</p> --}}
                        <div class="p-4 rounded-lg text-center bg-gray-200 dark:bg-dark-eval-0"> 
                            <a href="{{ route('post_info', $post->id) }}">
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

                                <button class="favorite-btn" data-id="{{ $post->id }}">
                                    @if($isFavorite)
                                        {{-- Filled Icon --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5 text-yellow-500">
                                            <path fill-rule="evenodd" d="M6.32 2.577a49.255 49.255 0 0 1 11.36 0c1.497.174 2.57 1.46 2.57 2.93V21a.75.75 0 0 1-1.085.67L12 18.089l-7.165 3.583A.75.75 0 0 1 3.75 21V5.507c0-1.47 1.073-2.756 2.57-2.93Z" clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                                        </svg>
                                    @endif
                                </button>


                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                </svg>
                                {{ $post->comments_count }}
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
        <p>No favorite post yet.</p>
    @endif
</x-app-layout>