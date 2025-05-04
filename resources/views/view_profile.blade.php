<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <a href="{{route("dashboard")}}">Dashboard</a> - Profile
            </h2>
            
        </div>
    </x-slot>
    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{$user_profile->name}}
            </h2>
            <h2 class="text-lg font-normal leading-tight">
                {{$user_profile->email}}
            </h2>
        </div>
        @if ($post_info->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 py-3">
                @foreach ($post_info as $file)
                    <div class="bg-gray-200 dark:bg-dark-eval-0 p-4 rounded-lgp-4 rounded-lg">
                        <p class="rounded-md px-2 mb-2 text-sm text-right uppercase {{ $file->pricing == 'exclusive' ? 'text-blue-500 ' : 'text-green-500 ' }}">{{ $file->pricing }}</p>
                        <div class="bg-white dark:bg-dark-eval-1 p-4 rounded-lg text-center"> 
                            <img src="{{ asset('uploads/' . $file->thumbnail) }}" alt="Latest Upload" 
                                class="w-28 h-28 object-cover mx-auto rounded-lg mt-2  {{ $file->pricing == 'exclusive' ? 'blur-md' : '' }}" 
                                >
                        </div>
                        {{-- @php
                            $fileCount = count(json_decode($file->file_name));
                        @endphp --}}
                        <p class="font-bold text-center mt-2">{{ $file->title }}</p>
                    
                    </div>
                @endforeach
            </div>
        @else
            <p>No files uploaded yet.</p>
        @endif
    
    </div>
</x-app-layout>