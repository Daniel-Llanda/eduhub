@vite(['resources/css/app.css', 'resources/js/app.js'])


<h1 class="text-2xl">hello {{Auth::user()->name}}</h1>
<div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
    <div class="relative overflow-x-auto">
        <table id="item-table" class=" table sm:text-xs lg:text-sm table-auto w-full bg-green-600">
            <thead class="text-xs ">
                <tr>
                    <th scope="col" class="px-4 py-2">Name</th>
                    <th scope="col" class="px-4 py-2">Email</th>
                    <th scope="col" class="px-4 py-2">Post</th> 
                    <th scope="col" class="px-4 py-2">Action</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td scope="row" class="px-6 py-4 font-medium text-black white space-nowrap">
                            {{$post->user->name}}
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium text-black white space-nowrap">
                            {{$post->user->email}}
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium text-black white space-nowrap">
                            {{$post->permission_post}}
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium space-nowrap">
                            <a href="{{route('teacher.approved_post',$post->id)}}" class="bg-green-500 px-2 py-1">APPROVED</a>
                            <a href="{{route('teacher.denied_post',$post->id)}}" class="bg-red-500 px-2 py-1">DENEID</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
    <div class="relative overflow-x-auto">
        <table id="item-table" class=" table sm:text-xs lg:text-sm table-auto w-full bg-green-600">
            <thead class="text-xs ">
                <tr>
                    <th scope="col" class="px-4 py-2">Name</th>
                    <th scope="col" class="px-4 py-2">Email</th>
                    <th scope="col" class="px-4 py-2">Status</th>
                    
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td scope="row" class="px-6 py-4 font-medium text-black white space-nowrap">
                            {{$user->name}}
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium text-black white space-nowrap">
                            {{$user->email}}
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium space-nowrap">
                         {{$user->id}}
                            
                          
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<form method="POST" action="{{ route('teacher.logout') }}">
    @csrf

    <x-dropdown-link
        :href="route('teacher.logout')"
        onclick="event.preventDefault(); this.closest('form').submit();"
    >
        {{ __('Log Out') }}
    </x-dropdown-link>
</form>