<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{ __('Dashboard') }}
            </h2>
            @php 
                $allTags = collect($posts)->pluck('tags')->filter()->toArray();
            
                $uniqueTags = collect($allTags)
                    ->flatMap(fn($tags) => explode(',', $tags))
                    ->map(fn($tag) => strtolower(trim($tag)))
                    ->unique()
                    ->values()
                    ->all();
            @endphp
            
            <div class="flex justify-end">
                <select id="tagFilter" class="px-3 py-2 rounded w-48 border-none focus:outline-none focus:ring-2 focus:ring-green-700 dark:bg-dark-eval-1">
                    <option value="all" class="">Show All</option>
                    @foreach ($uniqueTags as $tag)
                        <option value="{{ $tag }}" class="">{{ strtoupper($tag) }}</option>
                    @endforeach
                </select>
            </div>
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
                    <a href="{{ route('post_info', $post->id) }}">
                        <div class="{{ $post->pricing == 'premium' ? 'bg-yellow-500' : 'bg-blue-500' }} p-4 rounded-lg">
                            <p class="text-gray-200 text-md text-right uppercase mb-2">{{ $post->pricing }}</p>
                    
                            <div class="{{ $post->pricing == 'premium' ? 'bg-yellow-600' : 'bg-blue-600' }} p-4 rounded-lg text-center"> 
                                <img src="{{ asset('uploads/' . $post->thumbnail) }}" alt="Latest Upload" 
                                    class="w-28 h-28 object-cover mx-auto rounded-lg mt-2 {{ $post->pricing == 'premium' ? 'blur-md' : '' }}">
                            </div>
                            <p class="text-white font-bold text-center mt-2">{{ $post->title }}</p>
                            
                            <p class="text-gray-200 text-sm">Tags:</p>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach ($tagsArray as $tag)
                                    <span class="bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded-lg">
                                        {{ trim($tag) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <p>No files uploaded yet.</p>
    @endif
    

    <script>
        $("#tagFilter").on("change", function() {
            let filterClass = $(this).val(); // Get selected value

            if (filterClass === "all") {
                $(".post-item").removeClass("hidden"); // Show all posts
            } else {
                $(".post-item").addClass("hidden"); // Hide all posts
                $("." + filterClass).removeClass("hidden"); // Show selected tag
            }
        });

    </script>
    
</x-app-layout>
