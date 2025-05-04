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
            <input type="text" id="file_size" value="{{ $file_info->file_size}}" hidden>
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
                <div class="post-item overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 {{$file->permission_post}} all p-4 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-md uppercase px-2 py-1 rounded-md {{ $file->pricing == 'exclusive' ? 'text-blue-700 bg-blue-200' : 'text-green-700 bg-green-200' }}">{{ $file->pricing }}</p>

                        <button id="dropdownMenuIconButton{{ $file->id }}" data-dropdown-toggle="dropdownDots{{ $file->id }}" 
                            class="inline-flex items-center p-2 text-sm font-medium text-center
                                bg-white dark:bg-dark-eval-1
                                rounded-lg focus:ring-4 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
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
                                <li>
                                    <button value="{{$file->id}}" data-description="{{$file->description}}" data-modal-target="description-modal" data-modal-toggle="description-modal" class="description_btn block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white w-full text-left">Description</button>
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
                        <div class="bg-gray-200 dark:bg-dark-eval-0 p-4 rounded-lg text-center"> 
                            <img src="{{ asset('uploads/' . $file->thumbnail) }}" alt="Latest Upload" 
                                class="w-28 h-28 object-cover mx-auto rounded-lg mt-2 {{ $file->pricing == 'exclusive' ? 'blur-md' : '' }} hover:blur-none transition-300ms">
                        </div>
                    </a>
                    <p class="font-bold text-center mt-2">{{ $file->title }}</p>
                    <p class="text-sm mt-2">{{ $file->description }}</p>
                    @if ( $file->permission_post == "denied" )
                        <div class="flex gap-1 mt-2">
                            <button value="{{$file->id}}" 
                                data-modal-target="note-modal"
                                data-modal-toggle="note-modal" 
                                data-permissions='@json($file->permissionData)'
                                class="note_btn bg-red-200 text-red-800 rounded py-1 w-full">
                                Note
                            </button>
                            <button value="{{$file->id}}" 
                                data-modal-target="note-update-modal"
                                data-modal-toggle="note-update-modal" 
                                class="note_update_btn bg-blue-200 text-blue-800 rounded py-1 w-full">
                                Update
                            </button>

                        </div>
                    @elseif( $file->permission_post == "approved" )
                        <p class="bg-green-200 text-green-800 rounded py-1 w-full mt-2 text-center">Approved</p>
                    @else
                        <p class="bg-yellow-200 text-yellow-800 rounded py-1 w-full mt-2 text-center">Pending</p>
                    @endif

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
                    <form id="form_upload" action="{{ route('upload_post') }}" method="POST" enctype="multipart/form-data" class="max-w-sm mx-auto">
                        @csrf
                    
                        {{-- Title --}}
                        <div class="mb-2">
                            <label for="title" class="block mb-2 text-sm font-medium {{ $errors->has('title') ? 'text-red-700 dark:text-red-500' : 'text-gray-900 dark:text-white' }}">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title"
                                    class="shadow-xs bg-gray-50 border {{ $errors->has('title') ? 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' : 'border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:text-white dark:border-gray-600' }} text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    value="{{ old('title') }}" required>
                            @error('title')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    <span class="font-medium">Oops!</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="mb-2">
                            <label for="description" class="block mb-2 text-sm font-medium {{ $errors->has('description') ? 'text-red-700 dark:text-red-500' : 'text-gray-900 dark:text-white' }}">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea rows="2" name="description" id="description"
                                    class="shadow-xs bg-gray-50 border {{ $errors->has('description') ? 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' : 'border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:text-white dark:border-gray-600' }} text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    value="{{ old('description') }}" required placeholder="Add a comment...."></textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    <span class="font-medium">Oops!</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    
                        {{-- Thumbnail --}}
                        <div class="mb-2">
                            <label for="thumbnail" class="block mb-2 text-sm font-medium {{ $errors->has('thumbnail') ? 'text-red-700 dark:text-red-500' : 'text-gray-900 dark:text-white' }}">
                                Thumbnail <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                                    class="shadow-xs bg-gray-50 border {{ $errors->has('thumbnail') ? 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' : 'border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:text-white dark:border-gray-600' }} text-sm rounded-lg block w-full dark:bg-gray-700 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    required>
                            @error('thumbnail')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    <span class="font-medium">Oops!</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    
                        {{-- Upload Files --}}
                        <div class="mb-2">
                            <label for="upload_files" class="block mb-2 text-sm font-medium {{ $errors->has('upload_files') ? 'text-red-700 dark:text-red-500' : 'text-gray-900 dark:text-white' }}">
                                Files <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="upload_files[]" id="upload_files" multiple
                                    class="shadow-xs bg-gray-50 border {{ $errors->has('upload_files') ? 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' : 'border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:text-white dark:border-gray-600' }} text-sm rounded-lg block w-full dark:bg-gray-700 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    required>
                            @error('upload_files')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    <span class="font-medium">Oops!</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    
                        {{-- Hidden Tags --}}
                        <input type="text" hidden name="tags" id="file_extensions"
                                class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required />
                    
                        {{-- Pricing --}}
                        <div class="mb-2">
                            <label for="pricing" class="block mb-2 text-sm font-medium {{ $errors->has('pricing') ? 'text-red-700 dark:text-red-500' : 'text-gray-900 dark:text-white' }}">
                                Tiered Pricing <span class="text-red-500">*</span>
                            </label>
                            <select name="pricing" id="pricing"
                                    class="shadow-xs bg-gray-50 border {{ $errors->has('pricing') ? 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' : 'border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:text-white dark:border-gray-600' }} text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    required>
                                <option disabled selected>Pricing</option>
                                <option value="free" {{ old('pricing') == 'free' ? 'selected' : '' }}>Free</option>
                                <option value="exclusive" {{ old('pricing') == 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                            </select>
                            @error('pricing')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    <span class="font-medium">Oops!</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="flex items-center">
                            <input id="updated_add_watermark" name="updated_add_watermark" type="checkbox" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="updated_add_watermark" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Add Watermark</label>
                        </div>
                        
                        <!-- Hidden span -->
                        <span id="updated_watermark-message" class="text-sm text-red-500 mt-2 hidden">The watermark will not be editable if permanently added.</span>
                        
                    
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
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
                                <option value="exclusive">Exclusive</option>
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
                            <input type="file" name="update_thumbnail" accept="image/*" id="update_thumbnail" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required />
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
    <div id="description-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Rename
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="description-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="p-4 md:p-5 space-y-4">
                    <form action="{{route("description_update")}}" method="POST" enctype="multipart/form-data" class="max-w-sm mx-auto">
                        @csrf
                        <div class="mb-2">
                            <label for="update_description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description <span class="text-red-500">*</span></label>
                            
                            <input type="text" name="update_description" id="update_description" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required />
                            <input type="number" class="description_id" name="post_id" hidden>
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
    <div id="note-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Note
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="note-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="p-4 md:p-5 space-y-4">
                    <div id="permissionNoteText">

                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <div id="note-update-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Update Note
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="note-update-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="p-4 md:p-5 space-y-4">
                    <form id="updated_form_upload" action="{{route('updated_note_post')}}" method="POST" enctype="multipart/form-data" class="max-w-sm mx-auto">
                        @csrf
                    
                        {{-- Updated Title --}}
                        <div class="mb-2">
                            <label for="updated_title" class="block mb-2 text-sm font-medium {{ $errors->has('updated_title') ? 'text-red-700 dark:text-red-500' : 'text-gray-900 dark:text-white' }}">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="updated_title" id="updated_title"
                                    class="shadow-xs bg-gray-50 border {{ $errors->has('updated_title') ? 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' : 'border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:text-white dark:border-gray-600' }} text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    value="{{ old('updated_title') }}" required>
                            @error('updated_title')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    <span class="font-medium">Oops!</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    
                        {{-- Updated Description --}}
                        <div class="mb-2">
                            <label for="updated_description" class="block mb-2 text-sm font-medium {{ $errors->has('updated_description') ? 'text-red-700 dark:text-red-500' : 'text-gray-900 dark:text-white' }}">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea rows="2" name="updated_description" id="updated_description"
                                        class="shadow-xs bg-gray-50 border {{ $errors->has('updated_description') ? 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' : 'border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:text-white dark:border-gray-600' }} text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        required placeholder="Add a comment....">{{ old('updated_description') }}</textarea>
                            @error('updated_description')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    <span class="font-medium">Oops!</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    
                        {{-- Updated Thumbnail --}}
                        <div class="mb-2">
                            <label for="updated_thumbnail" class="block mb-2 text-sm font-medium {{ $errors->has('updated_thumbnail') ? 'text-red-700 dark:text-red-500' : 'text-gray-900 dark:text-white' }}">
                                Thumbnail <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="updated_thumbnail" id="updated_thumbnail" accept="image/*"
                                    class="shadow-xs bg-gray-50 border {{ $errors->has('updated_thumbnail') ? 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' : 'border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:text-white dark:border-gray-600' }} text-sm rounded-lg block w-full dark:bg-gray-700 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    required>
                            @error('updated_thumbnail')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    <span class="font-medium">Oops!</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    
                        {{-- Updated Upload Files --}}
                        <div class="mb-2">
                            <label for="updated_upload_files" class="block mb-2 text-sm font-medium {{ $errors->has('updated_upload_files') ? 'text-red-700 dark:text-red-500' : 'text-gray-900 dark:text-white' }}">
                                Files <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="updated_upload_files[]" id="updated_upload_files" multiple
                                    class="shadow-xs bg-gray-50 border {{ $errors->has('updated_upload_files') ? 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' : 'border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:text-white dark:border-gray-600' }} text-sm rounded-lg block w-full dark:bg-gray-700 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    required>
                            @error('updated_upload_files')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    <span class="font-medium">Oops!</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    
                        {{-- Hidden Updated Tags --}}
                        <input type="text" name="updated_tags" id="updated_file_extensions"
                                class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required />
                    
                        {{-- Updated Pricing --}}
                        <div class="mb-2">
                            <label for="updated_pricing" class="block mb-2 text-sm font-medium {{ $errors->has('updated_pricing') ? 'text-red-700 dark:text-red-500' : 'text-gray-900 dark:text-white' }}">
                                Tiered Pricing <span class="text-red-500">*</span>
                            </label>
                            <select name="updated_pricing" id="updated_pricing"
                                    class="shadow-xs bg-gray-50 border {{ $errors->has('updated_pricing') ? 'border-red-500 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 dark:text-red-500 dark:placeholder-red-500 dark:border-red-500' : 'border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500 dark:text-white dark:border-gray-600' }} text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    required>
                                <option disabled selected>Pricing</option>
                                <option value="free" {{ old('updated_pricing') == 'free' ? 'selected' : '' }}>Free</option>
                                <option value="exclusive" {{ old('updated_pricing') == 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                            </select>
                            @error('updated_pricing')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">
                                    <span class="font-medium">Oops!</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    
                        {{-- Watermark --}}
                        <div class="flex items-center">
                            <input id="add_watermark" name="add_watermark" type="checkbox" value="1"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="add_watermark" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Add Watermark</label>
                        </div>
                    
                        <span id="watermark-message" class="text-sm text-red-500 mt-2 hidden">The watermark will not be editable if permanently added.</span>
                        <input type="text" name="updated_post_id" id="updated_post_id" hidden>
                        <div class="flex justify-end">
                            <button type="submit"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Updated
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
            $("#updated_upload_files").on("change", function () {
                let files = this.files;
                let extensions = new Set();

                Array.from(files).forEach(file => {
                    let ext = file.name.split('.').pop().toLowerCase();
                    extensions.add(ext);
                });

                $("#updated_file_extensions").val(Array.from(extensions).join(","));
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
            $(document).on('click', '.description_btn', function() { 
                let description = $(this).data('description');
                let description_id = $(this).val();
                if (description) {
                    $('#update_description').val(description);
                    $('.description_id').val(description_id);
                    
                }
            });
            $(document).on('click', '.thumbnail_btn', function() { 
                let thumbnail_id = $(this).val();
                if (thumbnail_id) {
                    $('.thumbnail_id').val(thumbnail_id);
                    
                }
            });
            $('.delete_btn').click(function(e) {
                e.preventDefault(); 
                
                let deleteUrl = $(this).data('url');

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
                        window.location.href = deleteUrl; 
                    }
                });
            });
            $("#postFilter").on("change", function() {
                let filterClass = $(this).val(); 

                if (filterClass === "all") {
                    $(".post-item").removeClass("hidden"); 
                } else {
                    $(".post-item").addClass("hidden");
                    $("." + filterClass).removeClass("hidden"); 
                }
            });
            $('#add_watermark').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#watermark-message').removeClass('hidden');
                } else {
                    $('#watermark-message').addClass('hidden');
                }
            });
            $('#updated_add_watermark').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#updated_watermark-message').removeClass('hidden');
                } else {
                    $('#updated_watermark-message').addClass('hidden');
                }
            });
            
            $('.note_btn').on('click', function () {
                let permissions = $(this).data('permissions');
                let $noteContainer = $('#permissionNoteText');
                console.log(permissions);
 
                $noteContainer.empty();

                    if (permissions.length > 0) {
                    $.each(permissions, function(index, permission) {
    
                        let note = permission.permission_note ? permission.permission_note : 'No note from the teacher.';

                        let permissionText = `
                            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-lg shadow mb-4">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    <span class="text-gray-500">Teacher Name:</span> ${permission.teacher_name}
                                </p>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    <span class="text-gray-500">Note:</span> ${note}
                                </p>
                            </div>
                        `;

                        $noteContainer.append(permissionText);
                    });
                } else {
                    $noteContainer.text('No permissions available.');
                }
            });
            $('.note_update_btn').on('click', function () {
                let post_id = $(this).val();
                $('#updated_post_id').val(post_id)
            });
            $('#form_upload').on('submit', function (e) {
                let valid = true;
                let errors = [];

                $('.validation-error').remove();
                $('input, select').removeClass('border-red-500');

                const title = $('#title').val().trim();
                if (title === '') {
                    valid = false;
                    showError('#title', 'Title is required');
                }

                const description = $('#description').val().trim();
                if (description === '') {
                    valid = false;
                    showError('#description', 'Title is required');
                }


                const thumbnail = $('#thumbnail')[0].files[0];
                if (!thumbnail) {
                    valid = false;
                    showError('#thumbnail', 'Thumbnail is required');
                } else {
                    const allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedImageTypes.includes(thumbnail.type)) {
                        valid = false;
                        showError('#thumbnail', 'Thumbnail must be an image (JPG, PNG, GIF, WebP)');
                    }
                }


                const files = $('#upload_files')[0].files;
                const file_size = $('#file_size').val();
                
                if (files.length === 0) {
                    valid = false;
                    showError('#upload_files', 'At least one file is required');
                } else {
                    for (let i = 0; i < files.length; i++) {
                        if (files[i].size > file_size * 1024 * 1024) {
                            valid = false;
                            showError('#upload_files', `File "${files[i].name}" exceeds "${file_size}"MB`);
                            break;
                        }
                    }
                }


                const pricing = $('#pricing').val();
                if (!pricing || pricing === 'Pricing') {
                    valid = false;
                    showError('#pricing', 'Please select a pricing tier');
                }

                if (!valid) {
                    e.preventDefault();
                }

                function showError(selector, message) {
                    $(selector).addClass('border-red-500');
                    $(selector).after(`<p class="mt-1 text-sm text-red-600 validation-error">${message}</p>`);
                }
            });
            $('#updated_form_upload').on('submit', function (e) {
                let valid = true;

                $('.validation-error').remove();
                $('input, select, textarea').removeClass('border-red-500');

                const title = $('#updated_title').val().trim();
                if (title === '') {
                    valid = false;
                    showError('#updated_title', 'Title is required');
                }

                const description = $('#updated_description').val().trim();
                if (description === '') {
                    valid = false;
                    showError('#updated_description', 'Description is required');
                }

                const thumbnail = $('#updated_thumbnail')[0].files[0];
                if (!thumbnail) {
                    valid = false;
                    showError('#updated_thumbnail', 'Thumbnail is required');
                } else {
                    const allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedImageTypes.includes(thumbnail.type)) {
                        valid = false;
                        showError('#updated_thumbnail', 'Thumbnail must be an image (JPG, PNG, GIF, WebP)');
                    }
                }

                const files = $('#updated_upload_files')[0].files;
                const file_size = $('#file_size').val();
                if (files.length === 0) {
                    valid = false;
                    showError('#updated_upload_files', 'At least one file is required');
                } else {
                    for (let i = 0; i < files.length; i++) {
                        if (files[i].size > file_size * 1024 * 1024) {
                            valid = false;
                            showError('#updated_upload_files', `File "${files[i].name}" exceeds "${file_size}"MB`);
                            break;
                        }
                    }
                }

                const pricing = $('#updated_pricing').val();
                if (!pricing || pricing === 'Pricing') {
                    valid = false;
                    showError('#updated_pricing', 'Please select a pricing tier');
                }

                if (!valid) {
                    e.preventDefault();
                }

                function showError(selector, message) {
                    $(selector).addClass('border-red-500');
                    $(selector).after(`<p class="mt-1 text-sm text-red-600 validation-error">${message}</p>`);
                }
            });

        });

    </script>
</x-app-layout>