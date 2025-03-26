<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{ __('Account') }}
            </h2>
            
        </div>
    </x-slot>

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="flex justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{Auth::user()->name}}
            </h2>
            <h2 class="text-lg font-normal leading-tight">
                {{Auth::user()->email}}
            </h2>
        </div>
        @if ($userFiles->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 py-3">
                @foreach ($userFiles as $file)
                    <div class="{{ $file->pricing == 'premium' ? 'bg-yellow-500' : 'bg-blue-500' }} p-4 rounded-lg">
                        <p class="text-gray-200 text-md text-right uppercase mb-2">{{ $file->pricing }}</p>
                        <div class="{{ $file->pricing == 'premium' ? 'bg-yellow-600' : 'bg-blue-600' }} p-4 rounded-lg text-center"> 
                            <img src="{{ asset('uploads/' . $file->thumbnail) }}" alt="Latest Upload" 
                                class="w-28 h-28 object-cover mx-auto rounded-lg mt-2  {{ $file->pricing == 'premium' ? 'blur-md' : '' }}" 
                                >
                        </div>
                        {{-- @php
                            $fileCount = count(json_decode($file->file_name));
                        @endphp --}}
                        <p class="text-white font-bold text-center mt-2">{{ $file->title }}</p>
                    
                    </div>
                @endforeach
            </div>
        @else
            <p>No files uploaded yet.</p>
        @endif
    </div>
</x-app-layout>