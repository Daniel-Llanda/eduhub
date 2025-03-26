<x-app-layout>
    <style>
        /* For Webkit browsers (Chrome, Safari) */
        input[type="range"]::-webkit-slider-thumb {
            appearance: none;
            background-color: #16a34a; /* Green color */
            border-radius: 50%;
            cursor: pointer;
        }
    
        /* For Firefox */
        input[type="range"]::-moz-range-thumb {
            background-color: #16a34a; /* Green color */
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
    <x-slot name="header">
        
            <h2 class="text-xl font-semibold leading-tight">
                <a href="{{route("dashboard")}}">Dashboard</a> - {{$post_info->title}}
            </h2>
       
    </x-slot>
    
    <div class="py-4 px-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 pb-3">
            @foreach ($file_info as $file)
                <div class="{{ $post_info->pricing == 'premium' ? 'bg-yellow-500' : 'bg-blue-500' }} p-4 rounded-lg cursor-not-allowed">
                    <p class="text-gray-200 text-md text-right uppercase mb-2">{{ $post_info->pricing }}</p>
                    <div class="{{ $post_info->pricing == 'premium' ? 'bg-yellow-600' : 'bg-blue-600' }} p-4 rounded-lg text-center"> 
                        <img src="{{ asset('uploads/' . $file) }}" alt="Latest Upload" 
                        class="w-28 h-28 object-cover mx-auto rounded-lg mt-2 {{ $post_info->pricing == 'premium' ? 'blur-md' : '' }}" 
                        >
                    </div>
                </div>
            @endforeach
        </div>
        @php
          $fileCount = $post_info->files()->count();

        @endphp
        <div class="flex justify-between">
            <div>
                <a href="{{route("view_profile", $post_info->user->id)}}" class="text-lg font-bold hover:underline hover:text-blue-500">{{ $post_info->user->name }}</a>
                <p class="text-xs">{{ $post_info->created_at->format('M d, Y - h:i:s') }}</p>
            </div>
            <div>
                <button class="{{ $post_info->pricing == "premium" ? 'bg-yellow-500' : 'bg-green-500' }} rounded-md w-full">{{  $post_info->pricing == "premium" ? 'AVAIL' : 'FREE' }}</button>
                <p class="text-sm dark:bg-dark-eval-1">Total Files: {{ $fileCount }}</p>
            </div>
        </div>
    </div>
    <div class="py-4 px-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 my-3">
        
        <h2 class="text-xl mb-2 font-semibold leading-tight">
            Comments
        </h2>
        <form action="{{ route('add_comment', [Auth::user()->id, $post_info->id]) }}" method="post">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 py-2 gap-2">
            
                    <textarea name="comment" id="" rows="1" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500" placeholder="Add a comment...."></textarea>

                    <div class="flex items-center space-x-2 w-full">
                        <input type="range" id="rating" name="rating" min="1" max="10" value="5" 
                            class="w-full h-1 bg-gray-300 rounded-lg appearance-none cursor-pointer dark:bg-gray-600">
                        <span id="ratingValue" class="text-sm font-medium text-gray-900 dark:text-white">5</span>
                    </div>
                    <button class="bg-green-500 rounded-lg text-gray-200" type="submit">Comment</button>
            
                <script>
                    document.getElementById('rating').addEventListener('input', function() {
                        document.getElementById('ratingValue').textContent = this.value;
                    });
                </script>
                
                
            </div>
        </form>
        <hr class="h-px bg-gray-200 border-0 dark:bg-gray-700">
        @foreach ($comments as $comment)
            
            <div class="py-2">
                <div class="flex justify-between">
                    <h2 class="text-base font-semibold leading-tight">
                        {{$comment->user->name}}- {{$comment->rating}}/10
                    </h2>
                    <h6 class="text-xs">{{$comment->created_at->format('m D Y')}}</h6>
                </div>
                <h2 class="text-sm font-normal leading-tight">
                    {{$comment->comment}}
                </h2>
            </div>
            <hr class="h-px bg-gray-200 border-0 dark:bg-gray-700">
        @endforeach
    </div>
    
</x-app-layout>
