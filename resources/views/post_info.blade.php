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
    
    @php
        $userStatus = Auth::user()->status;
        $userId = Auth::id();
        $postOwnerId = $post_info->user_id;

        $pendingPermission = \App\Models\Permission::where('user_id', $userId)
            ->where('post_id', $post_info->id)
            ->where('receiver_id', $postOwnerId)
            ->where('permission_status', 0)
            ->first();

        $approvedPermission = \App\Models\Permission::where('user_id', $userId)
            ->where('post_id', $post_info->id)
            ->where('receiver_id', $postOwnerId)
            ->where('permission_status', 1)
            ->first();

        $deniedPermission = \App\Models\Permission::where('user_id', $userId)
            ->where('post_id', $post_info->id)
            ->where('receiver_id', $postOwnerId)
            ->where('permission_status', 2)
            ->first();

        $hasCommented = \App\Models\Comment::where('user_id', $userId)
            ->where('post_id', $post_info->id)
            ->exists();
        $canInteract = $userStatus == 1 && $approvedPermission;
    @endphp



    <div class="py-4 px-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 pb-3">
            @foreach ($file_info as $file)
                <div class="bg-gray-200 dark:bg-dark-eval-0 p-4 rounded-lg shadow hover:shadow-lg transition">
                    <p class="text-md text-right uppercase mb-2 {{ $file->pricing == 'exclusive' ? 'text-green-500' : 'text-blue-500' }}">
                        {{ $file->post->pricing }}
                    </p>
            
                    <div class="p-4 rounded-lg text-center">
                        @php $fileUrl = asset('uploads/' . $file->file_name); @endphp
            
                        <p class="text-sm truncate mb-2">{{ preg_replace('/^[^_]+_/', '', $file->file_name) }}</p>
            
                        @if ($file->post->pricing == 'free')
                            <a href="{{ route('file_download', ['fileId' => $file->id]) }}"
                                class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">
                                Download
                            </a>
                        @elseif ($canInteract)
                            <a href="{{ route('file_download', ['fileId' => $file->id]) }}"
                                class="bg-orange-500 text-white px-3 py-1 rounded hover:bg-orange-600 transition">
                                Download
                            </a>
                        @elseif ($userStatus == 1)
                            @if ($pendingPermission)
                                <span class="bg-yellow-500 text-white px-3 py-1 rounded">Pending</span>
                            @endif
                        @else
                            <span class="bg-red-500 text-white px-3 py-1 rounded">Not Verified</span>
                        @endif
                    </div>
                </div>
            @endforeach
        
        </div>
        <p class="text-md {{ $file->pricing == 'exclusive' ? 'text-green-500' : 'text-blue-500' }}">
            {{ $post_info->description }}
        </p>

        @php $fileCount = $post_info->files()->count(); @endphp

        <div class="flex justify-between">
            <div class="flex flex-col gap-1">
                <a href="{{ route('view_profile', $postOwnerId) }}"
                class="text-lg font-bold hover:underline hover:text-blue-500">
                    {{ $post_info->user->name }}
                </a>
                <p class="text-xs">{{ $post_info->created_at->format('M d, Y - h:i:s') }}</p>
            </div>
            <div class="flex flex-col gap-1">
                <p class="text-sm dark:bg-dark-eval-1">Total Files: {{ $fileCount }}</p>
                @if ($canInteract)
                    <a href="{{ route('download_all_files', $post_info->id) }}"
                    class="{{ $post_info->pricing == 'exclusive' ? 'bg-orange-500' : 'bg-green-500' }} text-white px-2 py-1 rounded">
                        Download
                    </a>
                @elseif ($file->post->pricing == 'free')
                    <a href="{{ route('download_all_files', $post_info->id) }}"
                    class="{{ $post_info->pricing == 'exclusive' ? 'bg-orange-500' : 'bg-green-500' }} text-white px-2 py-1 rounded">
                        Download
                    </a>
                @elseif ($userStatus == 1)
                    @if ($pendingPermission)
                        <span class="text-white px-2 py-1 rounded bg-yellow-500">Pending</span>
                    @endif
                @else
                    <span class="text-white px-2 py-1 rounded bg-red-500">Not Verified</span>
                @endif
            </div>
        </div>

        @if ($userId != $postOwnerId)
            @if ($userStatus == 1)
                @if ($pendingPermission)
                    <span class="text-yellow-600 bg-yellow-100 py-2 rounded-md flex gap-1 justify-center items-center mt-2 w-full">
                        Pending
                    </span>
                @elseif ($approvedPermission && !$hasCommented)
                    <div class="text-green-600 bg-green-200 py-2 px-3 rounded-md flex gap-1 justify-between items-center mt-2 w-full">
                        <p>Your request is approved</p> 
                        <button data-modal-target="comment-modal" data-modal-toggle="comment-modal" class="underline">Rate</button>
                    </div>
                @elseif ($post_info->pricing == 'free')
                    {{-- No need to ask permission for free posts --}}
                @elseif (!$approvedPermission || $deniedPermission)
                    <a href="{{ route('ask_permission', [$userId, $post_info->id, $postOwnerId]) }}"
                    class="bg-blue-500 py-2 rounded-md flex gap-1 justify-center items-center text-white mt-2">
                        Ask Permission
                    </a>
                @endif
            @else
                <div class="text-white text-center py-2 rounded bg-red-500 w-full mt-2">Not Verified</div>
            @endif
        @endif
    </div>

    {{-- Comment Section --}}
    <div class="py-4 px-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 my-3">
        <h2 class="text-xl mb-2 font-semibold leading-tight">Ratings</h2>
        @foreach ($comments as $comment)
            <div class="space-y-6">
                <div class="border border-gray-300 dark:border-gray-900 p-4 rounded-lg mb-2">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold">{{ $comment->user->name }} - {{ $comment->rating }}/10</p>
                            <p class="mt-1">{{ $comment->comment }}</p>
                        </div>
                        <h6 class="text-xs">{{ $comment->created_at->format('M d Y') }}</h6>
                    </div>
        
                    @foreach ($comment->replies as $reply_comment)
                        <div class="mt-4 ml-6 space-y-3">
                            <div class="bg-white dark:bg-dark-eval-0 border dark:border-gray-900 border-gray-300 p-3 rounded-lg">
                                <div class="flex justify-between">
                                    <p class="text-sm font-semibold">{{ $reply_comment->user->name }}</p>
                                    <p class="text-xs">{{ $reply_comment->created_at->format('M d Y') }}</p>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ $reply_comment->reply }}</p>
                            </div>
                        </div>
                    @endforeach
        
                    @if (Auth::id() == $post_info->user_id)
                        <div class="mt-4 ml-4">
                            <form action="{{ route('reply_comment', [$comment->id, Auth::id(), $post_info->id]) }}" method="POST">
                                @csrf
                                <textarea name="reply" class="w-full p-2 bg-white dark:bg-dark-eval-1 border dark:border-gray-900 border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-400" placeholder="Write a reply..."></textarea>
                                <button class="mt-1 px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">Reply</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    
        
    </div>

    @if (!$hasCommented)
        <div id="comment-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-xl max-h-full">
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Rating
                        </h3>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="comment-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    <div class="p-4 md:p-5 space-y-4">
                        <form id="commentForm" action="{{ route('add_comment', [$userId, $post_info->id]) }}" method="post">
                            @csrf
                            <div class="grid grid-cols-1 py-2 gap-2">
                                <textarea id="comment" name="comment" rows="2"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    placeholder="Add a comment...."></textarea>
                        
                                <div class="flex items-center space-x-2 w-full">
                                    <input type="range" id="rating" name="rating" min="1" max="10" value="5"
                                        class="w-full h-1 bg-gray-300 rounded-lg appearance-none cursor-pointer dark:bg-gray-600">
                                    <span id="ratingValue" class="text-sm font-medium text-gray-900 dark:text-white">5</span>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button class="bg-green-500 p-2 rounded-lg text-white" type="submit">Comment</button>    
                                </div>
                            </div>
                        </form>
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                        <script>
                            $(document).ready(function() {
                            
                                // List of bad words
                                const badWords = [
                                    "fuck", "f*ck", "f u c k", "fuk", "fukc", "fcuk", "fucc", "fucck", "phuck", "fck",
                                    "motherfucker", "mother f*cker", "mf", "mothafucka",
                                    "shit", "sh*t", "sh1t", "shet", "shiet", "shiit",
                                    "asshole", "a**hole", "ass hole", "ashole",
                                    "bitch", "b*tch", "biatch", "beetch", "beech", "b1tch",
                                    "bastard", "b@stard", "b4stard",
                                    "damn", "d4mn", "d@mn", "damm",
                                    "crap", "cr@p",
                                    "jerk",
                                    "dick", "d*ck", "d1ck", "d!ck", "dik", "dyk",
                                    "cunt", "c*nt", "kunt", "k*nt",
                                    "pussy", "p*ssy", "pusy", "pussee",
                                    "whore", "wh*re", "h0e", "hoe",
                                    "slut", "sl*t",
                                    "retard", "r3tard",
                                    "fag", "f4g", "faggot", "f@ggot", "f4ggot",
                                    "idiot", "dumbass", "dumb ass", "stupid", "moron",
                                    "shithead", "shit bag", "shitbag",
                                    "fucktard",
                                    "nigger", "n1gger", "n1gr", "nigga", "niga", "negro",
                                    "putang ina", "put@ng ina", "put4ng ina", "p u t a", "puta", "p*ta", "put4",
                                    "putanginamo", "put@nginamo", "pukinangina", "puking ina", "pukingina", "pukeng ina", "pukengina", "puking inang ina",
                                    "tang ina", "tanginamo", "tanginang buhay", "t@ng ina", "t@ngina", "tangina", "tngina",
                                    "pota", "potang ina",
                                    "gago", "gagu", "g@go", "gag0",
                                    "ulol", "ul0l", "ul*l",
                                    "tarantado", "tarantad0", "tarant@do", "t@rantado",
                                    "bobo", "b0b0", "b0bo",
                                    "tanga", "t@nga", "t@ng4", "tang4",
                                    "leche", "l3che", "litshe", "litson",
                                    "bwisit", "bwishet", "b*wisit",
                                    "lintik", "l1nt1k",
                                    "hudas",
                                    "punyeta", "p*nyeta", "puny3ta", "punyetang ina",
                                    "hindot", "h1ndot",
                                    "hayop ka", "hayup ka",
                                    "walang kwenta", "walang silbi",
                                    "anak ng puta", "anak ng p*ta", "anak ng"
                                ];
                            
                                // When the form is submitted
                                $('#commentForm').on('submit', function(e) {
                                    const comment = $('#comment').val().toLowerCase().trim(); // Get comment value
                            
                                    let containsBadWord = false;
                            
                                    $.each(badWords, function(index, word) {
                                        if (comment.includes(word)) {
                                            containsBadWord = true;
                                            return false; // Break out of the loop
                                        }
                                    });
                            
                                    if (containsBadWord) {
                                        e.preventDefault(); // Stop form from submitting
                                        alert('Your comment contains inappropriate words. Please fix it.');
                                    }
                                });
                            
                                // Update rating value when slider changes
                                $('#rating').on('input', function() {
                                    $('#ratingValue').text($(this).val());
                                });
                            
                            });
                        </script>
                        
                    </div>
                </div>
            </div>
        </div>
    @endif
    
</x-app-layout>
