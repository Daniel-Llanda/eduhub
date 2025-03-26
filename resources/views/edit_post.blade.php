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
                <a href="{{route("my_uploads")}}">My Uploads</a> - {{$post_info->title}}
            </h2>
            
        </div>
    </x-slot>
    <div class="w-full py-4 px-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 relative">
        <div class="flex justify-between items-center">
            <h1 class="text-lg">Files</h1>
            <button value="{{$post_info->id}}" data-modal-target="add-file-modal" data-modal-toggle="add-file-modal" class="add_files_btn bg-blue-500 px-3 py-2 rounded text-white cursor-pointer flex flex-row gap-1">
                <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7.757v8.486M7.757 12h8.486M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                Add File
            </button>
        </div>
        
        
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 py-3">
            @foreach ($file_info as $file) 
                <div>
                    <div class="p-4 rounded-lg flex justify-center items-center"> 
                        @php
                            $extension = pathinfo($file->file_name, PATHINFO_EXTENSION);
                        @endphp
                        
                        @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            <img src="{{ asset('uploads/' . $file->file_name) }}" class="w-20 h-20">
                        @elseif (in_array(strtolower($extension), ['pdf']))
                            <a href="{{ asset('uploads/' . $file->file_name) }}" target="_blank">
                                <img src="{{ asset('icons/pdf.png') }}" class="w-20 h-20">
                            </a>
                        @elseif (in_array(strtolower($extension), ['doc', 'docx']))
                            <a href="{{ asset('uploads/' . $file->file_name) }}" target="_blank">
                                <img src="{{ asset('icons/doc.png') }}" class="w-20 h-20">
                            </a>
                        @elseif (in_array(strtolower($extension), ['ppt', 'pptx']))
                            <a href="{{ asset('uploads/' . $file->file_name) }}" target="_blank">
                                <img src="{{ asset('icons/ppt.png') }}" class="w-20 h-20">
                            </a>
                        @elseif (in_array(strtolower($extension), ['xlsx']))
                            <a href="{{ asset('uploads/' . $file->file_name) }}" target="_blank">
                                <img src="{{ asset('icons/xls.png') }}" class="w-20 h-20">
                            </a>
                        @else
                            <a href="{{ asset('uploads/' . $file->file_name) }}" target="_blank">
                                <img src="{{ asset('icons/apk.png') }}" class="w-20 h-20">
                            </a>
                        @endif
                    </div>
                    <a href="{{ route('delete_file', $file->id) }}" 
                        class="delete_btn block px-2 py-1 w-full bg-red-600 text-center text-white"
                        data-url="{{ route('delete_file', $file->id) }}">
                        Delete
                    </a>
                </div>
            @endforeach

        </div>

    </div>
    <div id="add-file-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Add Files
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="add-file-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>

                <div class="p-4 md:p-5 space-y-4">
                    <form action="{{route("add_files")}}" method="POST" enctype="multipart/form-data" class="max-w-sm mx-auto">
                        @csrf
                        <div class="mb-2">
                            <label for="upload_files" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Files<span class="text-red-500">*</span>
                            </label>

                            <input type="file" name="upload_files[]" id="upload_files" multiple
                                class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light"
                                required />
                                <input type="text" class="add_file_id" name="post_id" hidden>
                                <input type="text" hidden name="tags" id="file_extensions" class="shadow-xs bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 dark:shadow-xs-light" required />
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Add
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $("#upload_files").on("change", function () {
                let files = this.files;
                let extensions = [];

                for (let i = 0; i < files.length; i++) {
                    let fileName = files[i].name;
                    let ext = fileName.split('.').pop();
                    extensions.push(ext);
                }
                $("#file_extensions").val(extensions.join(","));
            });
            $(document).on('click', '.add_files_btn', function() { 
                let add_file_id = $(this).val();
                if (add_file_id) {
                    $('.add_file_id').val(add_file_id);
                    
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
        });

    </script>

</x-app-layout>
