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
   
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Students
        </div>
    
        <div class="card-body p-3">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-post-tab" data-bs-toggle="pill" data-bs-target="#pills-post" type="button" role="tab" aria-controls="pills-post" aria-selected="true">Post</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-history-tab" data-bs-toggle="pill" data-bs-target="#pills-history" type="button" role="tab" aria-controls="pills-history" aria-selected="false">History</button>
                </li>
            
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-post" role="tabpanel" aria-labelledby="pills-post-tab">
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
                                        @php
                                            $teacher_approval = App\Models\PermissionPost::where('post_id', $post->id)
                                                                ->where('teacher_id', Auth::user()->id)
                                                                ->exists();
                                            $user = App\Models\User::findOrFail($post->user->id);

                                            // Decode the teacher field (assuming it's stored as a JSON array)
                                            $teacherIds = json_decode($user->teacher, true);

                                            // Count how many teacher IDs are there
                                            $teacherCount = is_array($teacherIds) ? count($teacherIds) : 0;
                                            $permission_count = App\Models\PermissionPost::where('post_id', $post->id)->count();
                                        @endphp
                                        @if (!$teacher_approval)
                                            <a href="#" class="approved_btn btn btn-success btn-sm" data-url="{{ route('teacher.approved_post',[$post->user->id, Auth::user()->id ,$post->id]) }}">Approved</a>
                                            <button type="button" class="denied_btn btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deniedModal" data-user_name="{{$post->user->name}}"  data-user_id="{{$post->user->id}}" data-teacher_id="{{ Auth::user()->id}}" data-post_id="{{$post->id}}">
                                                Denied
                                            </button>
                                            {{-- <a href="{{route('teacher.denied_post',$post->id)}}" class="denied_btn btn btn-danger btn-sm" data-url="{{ route('teacher.denied_post', $post->id) }}">Denied</a> --}}
                                            <p>
                                                @if ($permission_count != 0)
                                                    <small>{{ $permission_count}}/{{$teacherCount}} responses submitted.</small>
                                                @endif
                                              
                                            </p>
                                        @else
                                            <p class="text-success">Response submitted. </p>
                                            <p><small>Awaiting others ({{ $permission_count}}/{{$teacherCount}}  approvals).</small></p>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="pills-history" role="tabpanel" aria-labelledby="pills-history-tab">
                    <table id="myTableHistory"  class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Thumbnail</th>
                                <th>Post</th>
                                <th>Permission</th>
                                
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Thumbnail</th>
                                <th>Post</th>
                                <th>Permission</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($posts_history as $post)
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
                                    <td class="uppercase">
                                        {{$post->permission_post}}
                                    </td>
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>
            
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deniedModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Denied Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('teacher.denied_post')}}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <p>Recipient: <span id="recipient_name"></span></p>
                    </div>
                    <div class="mb-3">
                        <label for="permission_note" class="col-form-label">Reason:</label>
                        <textarea class="form-control" id="permission_note" name="permission_note"></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    <input type="text" id="user_id" name="user_id" hidden>
                    <input type="text" id="teacher_id" name="teacher_id" hidden>
                    <input type="text" id="post_id" name="post_id" hidden>

                </form>
            </div>
            
        </div>
    </div>
</div>
<script>
    $(document).ready( function () {
        $('#myTable').DataTable();
        $('#myTableHistory').DataTable();
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
        $('.denied_btn').click(function(e) {
            let user_id = $(this).data('user_id'); 
            let user_name = $(this).data('user_name'); 
            let teacher_id = $(this).data('teacher_id'); 
            let post_id = $(this).data('post_id'); 

            $('#user_id').val(user_id)
            $('#recipient_name').text(user_name)
            $('#teacher_id').val(teacher_id)
            $('#post_id').val(post_id)
            console.log(user_id,teacher_id,post_id);
            
        });
    });
</script>

@endsection