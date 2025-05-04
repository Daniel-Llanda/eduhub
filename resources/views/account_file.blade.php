<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                <a href="{{route("account")}}">Account</a> - {{$post_info->title}}
            </h2>
            
        </div>
    </x-slot>

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
              
         
              
                <p class="text-xs">{{ $post_info->created_at->format('M d, Y - h:i:s') }}</p>
            </div>
            <div class="flex flex-col gap-1">
                <p class="text-sm dark:bg-dark-eval-1">Total Files: {{ $fileCount }}</p>
               
            </div>
        </div>

      
    </div>
    

</x-app-layout>
