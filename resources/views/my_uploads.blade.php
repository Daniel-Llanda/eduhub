<x-app-layout>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 1500 
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "{{ session('error') }}",
                showConfirmButton: false,
                timer: 1500 
            });
        </script>
    @endif
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{ __('My Uploads') }}
            </h2>
            
        </div>
    </x-slot>
    

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex justify-between">
            @if (Auth::user()->status == 0)
                <span class="bg-gray-400 px-3 py-2 rounded text-white cursor-not-allowed">Locked</span>
            @else
                <button data-modal-target="upload-modal" data-modal-toggle="upload-modal" class="bg-green-500 px-3 py-2 rounded text-white cursor-pointer">Upload</button>
                <select id="postFilter" class="px-3 py-2 rounded w-32 border-none focus:outline-none focus:ring-2 focus:ring-green-700 dark:bg-dark-eval-1">
                    <option value="all">Show All</option>
                    <option value="approved">Approved</option>
                    <option value="denied">Denied</option>
                    <option value="pending">Pending</option>
                </select>
            @endif
          
    
        </div>
     
    </div>

    @if ($userFiles->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 py-3">
            @foreach ($userFiles as $file)
                <div class="post-item {{ $file->pricing == 'premium' ? 'bg-yellow-500' : 'bg-blue-500' }} {{$file->permission_post}} all p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-gray-200 text-md uppercase">{{ $file->pricing }}</p>

                        <button id="dropdownMenuIconButton{{ $file->id }}" data-dropdown-toggle="dropdownDots{{ $file->id }}" 
                            class="inline-flex items-center p-2 text-sm font-medium text-center text-white 
                                {{ $file->pricing == 'premium' ? 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-400' : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-400' }}  
                                rounded-lg focus:ring-4 focus:outline-none">
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15">
                                <path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                            </svg>
                        </button>
                        

                        <div id="dropdownDots{{ $file->id }}" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700 dark:divide-gray-600">
                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownMenuIconButton{{ $file->id }}">
                                <li>
                                    <button value="{{$file->id}}" data-pricing="{{$file->pricing}}" data-modal-target="update-price-modal" data-modal-toggle="update-price-modal" class="update_price_btn block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white w-full text-left">Update Price</button>
                                </li>
                                <li>
                                    <button value="{{$file->id}}" data-modal-target="change-thumbnail-modal" data-modal-toggle="change-thumbnail-modal" class="thumbnail_btn block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white w-full text-left">Change Thumbnail</button>
                                </li>
                                <li>
                                    <button value="{{$file->id}}" data-title="{{$file->title}}" data-modal-target="rename-modal" data-modal-toggle="rename-modal" class="rename_btn block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white w-full text-left">Rename</button>
                                </li>
                            </ul>
                            <div class="py-2">
                                <a href="{{ route('delete_post', $file->id) }}" 
                                    class="delete_btn block px-4 py-2 text-sm text-red-500 hover:bg-gray-100 dark:hover:bg-gray-600" 
                                    data-url="{{ route('delete_post', $file->id) }}">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('uploaded_post_info', $file->id) }}">
                        <div class="{{ $file->pricing == 'premium' ? 'bg-yellow-600' : 'bg-blue-600' }} p-4 rounded-lg text-center"> 
                            <img src="{{ asset('uploads/' . $file->thumbnail) }}" alt="Latest Upload" 
                                class="w-28 h-28 object-cover mx-auto rounded-lg mt-2 {{ $file->pricing == 'premium' ? 'blur-md' : '' }} hover:blur-none transition-300ms">
                        </div>
                    </a>
                    <p class="text-white font-bold text-center mt-2">{{ $file->title }}</p>
                </div>
            @endforeach

        </div>
    @else
        <p>No files uploaded yet.</p>
    @endif

    <div id="upload-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Upload Post
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="upload-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="p-4 md:p-5 space-y-4">
                    <form action="{{ route('upload_post') }}" method="POST" enctype="multipart/form-data" class="max-w-sm mx-auto">
                        @csrf
                        <div class="mb-2">
                            <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required />
                        </div>
                        <div class="mb-2">
                            <label for="thumbnail" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Thumbnail<span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="thumbnail" id="thumbnail" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required />
                        </div>
                        
                        <div class="mb-2">
                            <label for="upload_files" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Files<span class="text-red-500">*</span>
                            </label>

                            <input type="file" name="upload_files[]" id="upload_files" multiple
                                class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light"
                                required />
                        </div>
                        <input type="text" hidden name="tags" id="file_extensions" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required />

                        <div class="mb-2">
                            <label for="pricing" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tiered Pricing <span class="text-red-500">*</span></label>
                            <select name="pricing" id="pricing" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required>
                                <option disabled selected>Pricing</option>
                                <option value="free">Free</option>
                                <option value="premium">Premium</option>
                            </select>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Post
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
    <div id="update-price-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Update Price
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="update-price-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="p-4 md:p-5 space-y-4">
                    <form action="{{route("price_update")}}" method="POST" enctype="multipart/form-data" class="max-w-sm mx-auto">
                        @csrf
                        <div class="mb-2">
                            <label for="update_pricing" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tiered Pricing <span class="text-red-500">*</span></label>
                            <select name="update_pricing" id="update_pricing" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required>
                                <option disabled selected>Pricing</option>
                                <option value="free">Free</option>
                                <option value="premium">Premium</option>
                            </select>
                            <input type="number" class="price_id" name="post_id" hidden>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Update
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
    <div id="change-thumbnail-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Change Thumbnail
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="change-thumbnail-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="p-4 md:p-5 space-y-4">
                    <form action="{{route("thumbnail_update")}}" method="POST" enctype="multipart/form-data" class="max-w-sm mx-auto">
                        @csrf
                        <div class="mb-2">
                            <label for="update_thumbnail" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Thumbnail<span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="update_thumbnail" id="update_thumbnail" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required />
                            <input type="number" class="thumbnail_id" name="post_id" hidden>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Update
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
    <div id="rename-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Rename
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="rename-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="p-4 md:p-5 space-y-4">
                    <form action="{{route("rename_update")}}" method="POST" enctype="multipart/form-data" class="max-w-sm mx-auto">
                        @csrf
                        <div class="mb-2">
                            <label for="update_title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="update_title" id="update_title" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required />
                            <input type="number" class="rename_id" name="post_id" hidden>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Update
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            $("#upload_files").on("change", function () {
                let files = this.files;
                let extensions = new Set();

                Array.from(files).forEach(file => {
                    let ext = file.name.split('.').pop().toLowerCase();
                    extensions.add(ext);
                });

                $("#file_extensions").val(Array.from(extensions).join(","));
            });
            $(document).on('click', '.update_price_btn', function() { 
                let price = $(this).data('pricing');
                let price_id = $(this).val();
                if (price) {
                    $('#update_pricing').val(price);
                    $('.price_id').val(price_id);
                    
                }
            });
            $(document).on('click', '.rename_btn', function() { 
                let title = $(this).data('title');
                let rename_id = $(this).val();
                if (title) {
                    $('#update_title').val(title);
                    $('.rename_id').val(rename_id);
                    
                }
            });
            $(document).on('click', '.thumbnail_btn', function() { 
                let thumbnail_id = $(this).val();
                if (thumbnail_id) {
                    $('.thumbnail_id').val(thumbnail_id);
                    
                }
            });
            $('.delete_btn').click(function(e) {
                e.preventDefault(); // Prevent default action
                
                let deleteUrl = $(this).data('url'); // Get delete URL from data attribute

                Swal.fire({
                    title: "Are you sure?",
                    text: "This action cannot be undone!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteUrl; // Redirect to delete route
                    }
                });
            });
            $("#postFilter").on("change", function() {
                let filterClass = $(this).val(); // Get selected value

                if (filterClass === "all") {
                    $(".post-item").removeClass("hidden"); // Show all posts
                } else {
                    $(".post-item").addClass("hidden"); // Hide all posts
                    $("." + filterClass).removeClass("hidden"); // Show selected tag
                }
            });
        });

    </script>
</x-app-layout>