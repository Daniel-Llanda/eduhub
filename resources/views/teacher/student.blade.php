{{-- @vite(['resources/css/app.css', 'resources/js/app.js'])


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
</form> --}}

{{-- 
<x-slot name="header">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <h2 class="text-xl font-semibold leading-tight">
            {{ __('Admin') }}
        </h2>
    </div>
</x-slot> --}}
{{-- @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                            @if ($user->status == "not_verified")
                                <a href="{{route('admin.status_update', $user->id)}}" class="bg-red-500 p-1">Not Verified</a>
                            @else
                                <a href="#" class="bg-green-500">Verified</a>
                            @endif
                            <a href="{{route('admin.status_update', $user->id)}}" class="text-white bg-{{$user->status ? 'green' : 'red'}}-500 hover:bg-{{$user->status ? 'green' : 'red'}}-400 focus:ring-4 focus:outline-none focus:ring-{{$user->status ? 'green' : 'red'}}-300 rounded-lg lg:p-3 p-2 text-center text-sm">{{$user->status ? 'Verified' : 'Not Verified'}}</a>
                            <form action="{{ route('admin.assign_teacher') }}" method="POST">
                                @csrf
                                <select name="teacher_id" required>
                                    <option selected disabled>Teachers</option>
                                    @foreach ($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                    @endforeach
                                </select>
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
--}}
@extends('teacher.layout.app')
@section('main_teacher')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>

<style>
    img{
        height: 50px;
    }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4">Student</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Student</li>
    </ol>
    <div class="row">
    </div>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Students
        </div>
        <div class="card-body">
            <table id="myTable"  class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Thumbnail</th>
                        <th>Post</th>
                        <th>Action</th>
                        
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Thumbnail</th>
                        <th>Post</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach ($posts as $post)
                        <tr>
                            <td>
                                {{$post->user->name}}
                            </td>
                            <td>
                                {{$post->user->email}}
                            </td>
                            <td>
                                <img src="{{ asset('uploads/' . $post->thumbnail) }}">  
                            </td>
                            <td>
                                @foreach ($post->files as $file)
                                @php
                                $extension = pathinfo($file->file_name, PATHINFO_EXTENSION);
                            @endphp
                            
                            @if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ asset('uploads/' . $file->file_name) }}">
                            @elseif (in_array(strtolower($extension), ['pdf']))
                                <a href="{{ asset('uploads/' . $file->file_name) }}" target="_blank">
                                    <img src="{{ asset('icons/pdf.png') }}">
                                </a>
                            @elseif (in_array(strtolower($extension), ['doc', 'docx']))
                                <a href="{{ asset('uploads/' . $file->file_name) }}" target="_blank">
                                    <img src="{{ asset('icons/doc.png') }}">
                                </a>
                            @elseif (in_array(strtolower($extension), ['ppt', 'pptx']))
                                <a href="{{ asset('uploads/' . $file->file_name) }}" target="_blank">
                                    <img src="{{ asset('icons/ppt.png') }}">
                                </a>
                            @elseif (in_array(strtolower($extension), ['xlsx']))
                                <a href="{{ asset('uploads/' . $file->file_name) }}" target="_blank">
                                    <img src="{{ asset('icons/xls.png') }}">
                                </a>
                            @else
                                <a href="{{ asset('uploads/' . $file->file_name) }}" target="_blank">
                                    <img src="{{ asset('icons/apk.png') }}">
                                </a>
                            @endif
                                @endforeach
                            </td>
                            <td>
                                <a href="{{route('teacher.approved_post',$post->id)}}" class="approved_btn btn btn-success btn-sm" data-url="{{ route('teacher.approved_post', $post->id) }}">Approved</a>
                                <a href="{{route('teacher.denied_post',$post->id)}}" class="denied_btn btn btn-danger btn-sm" data-url="{{ route('teacher.denied_post', $post->id) }}">Denied</a>
                            </td>
                        </tr>
                    @endforeach
                    
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready( function () {
        $('#myTable').DataTable();
        $('.approved_btn').click(function(e) {
            e.preventDefault();
                
            let approvedUrl = $(this).data('url'); 

            Swal.fire({
                title: "Are you sure?",
                text: "This action cannot be undone!",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, approved it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = approvedUrl; 
                }
            });
        });
    });
</script>

@endsection