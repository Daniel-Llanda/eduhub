{{-- 
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <h2 class="text-xl font-semibold leading-tight">
                {{ __('Admin') }}
            </h2>
        </div>
    </x-slot> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="relative overflow-x-auto">
            <table id="item-table" class=" table sm:text-xs lg:text-sm table-auto w-full bg-green-600">
                <thead class="text-xs ">
                    <tr>
                        <th scope="col" class="px-4 py-2">Name</th>
                        <th scope="col" class="px-4 py-2">Email</th>
                        <th scope="col" class="px-4 py-2">Status</th>
                        <th scope="col" class="px-4 py-2">Assigned Teacher</th>
                        
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
                                {{-- @if ($user->status == "not_verified")
                                    <a href="{{route('admin.status_update', $user->id)}}" class="bg-red-500 p-1">Not Verified</a>
                                @else
                                    <a href="#" class="bg-green-500">Verified</a>
                                @endif --}}
                                <a href="{{route('admin.status_update', $user->id)}}" class="text-white bg-{{$user->status ? 'green' : 'red'}}-500 hover:bg-{{$user->status ? 'green' : 'red'}}-400 focus:ring-4 focus:outline-none focus:ring-{{$user->status ? 'green' : 'red'}}-300 rounded-lg lg:p-3 p-2 text-center text-sm">{{$user->status ? 'Verified' : 'Not Verified'}}</a>
                                <form action="{{ route('admin.assign_teacher') }}" method="POST">
                                    @csrf
                                    {{-- <select name="teacher_id" required>
                                        <option selected disabled>Teachers</option>
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
                                    </select> --}}
                                    <select class="js-example-basic-multiple" name="teacher_id[]" multiple="multiple">
                                        @foreach ($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @endforeach
                                        
                                    </select>
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <button class="bg-red-500" type="submit">Assign</button>
                                </form>
                                
                            </td>
                            <td scope="row" class="px-6 py-4 font-medium text-black white space-nowrap">
                                @php
                                    $teacherName = \App\Models\Teacher::where('id', $user->teacher)->value('name');
                                @endphp
                                
                                {{ $teacherName ?? 'No teacher found' }}
                            
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2();
        });
    </script>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf

        <x-dropdown-link
            :href="route('admin.logout')"
            onclick="event.preventDefault(); this.closest('form').submit();"
        >
            {{ __('Log Out') }}
        </x-dropdown-link>
    </form>

