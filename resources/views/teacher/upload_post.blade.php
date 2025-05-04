@extends('teacher.layout.app')
@section('main_teacher')


<div class="container-fluid px-4">
    <h1 class="mt-4">My Uploads</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Post </li>
    </ol> 
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPost">
                    <i class="bi bi-upload me-1"></i>Upload Post
                </button>
            </div>
        </div>
    </div>
    @if ($userFiles->isNotEmpty())
        <div class="row py-3">
            @foreach ($userFiles as $file)
                <div class="col-12 col-md-6 col-xl-3 mb-4">
                    <div class="card shadow-sm border {{ $file->permission_post }}">
                        <div class="d-flex justify-content-end">
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton{{ $file->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                    Edit
                                </button>
    
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $file->id }}">
                                    <li>
                                        <button value="{{$file->id}}" type="button" class="dropdown-item change_thumbnail_btn" data-bs-toggle="modal" data-bs-target="#thumbnailModal">
                                            Change Thumbnail
                                        </button>
                                    </li>
                                    <li>
                                        <button value="{{$file->id}}" data-title="{{ $file->title}}" type="button" class="dropdown-item rename_btn" data-bs-toggle="modal" data-bs-target="#renameModal">
                                            Rename
                                        </button>
                                    </li>
                                    <li>
                                        <button value="{{$file->id}}" data-title="{{ $file->description}}" type="button" class="dropdown-item description_btn" data-bs-toggle="modal" data-bs-target="#descriptionModal">
                                            Description
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a href="{{ route('delete_post', $file->id) }}" class="dropdown-item text-danger delete_btn" data-url="{{ route('delete_post', $file->id) }}">
                                            Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="text-center bg-light rounded p-2">
                                <img src="{{ asset('storage/' . $file->thumbnail) }}" class="img-fluid rounded" style="max-width: 100px; height: 100px; object-fit: cover;" alt="Thumbnail">
                            </div>

                            <h6 class="mt-2 text-center fw-bold">{{ $file->title }}</h6>
                            <p class="small mt-1">{{ $file->description }}</p>
                            <p class="small mt-1">
                                @php
                                    $file_names = json_decode($file->file_name);
                                @endphp
                            
                                @foreach($file_names as $file)
                                    @php
                                        $file_name_with_path = basename($file); 
                                        
                                        $file_name = preg_replace('/^\d+_/', '', $file_name_with_path);
                                    @endphp
                                    <a href="{{ asset('storage/uploads/' . $file_name_with_path) }}" target="_blank">{{ $file_name }}</a><br>
                                @endforeach
                            </p>
                            
                            
                            
                            
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p>No files uploaded yet.</p>
    @endif

    
</div>
<div class="modal fade" id="uploadPost" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="uploadPostLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="uploadPostLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <div class="modal-body">
            <form id="form_upload" action="{{ route('teacher.teacher_upload_post') }}" method="POST" enctype="multipart/form-data" class="container-sm">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">
                        Title <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="title" id="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">
                        Description <span class="text-danger">*</span>
                    </label>
                    <textarea rows="2" name="description" id="description" class="form-control" required placeholder="Add a description...."></textarea>
                </div>
                <div class="mb-3">
                    <label for="thumbnail" class="form-label">
                        Thumbnail <span class="text-danger">*</span>
                    </label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="upload_files" class="form-label">
                        Files <span class="text-danger">*</span>
                    </label>
                    <input type="file" name="upload_files[]" id="upload_files" multiple class="form-control" required>
                </div>
                <input type="text" hidden name="tags" id="file_extensions" required />
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        Post
                    </button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="thumbnailModal" tabindex="-1" aria-labelledby="thumbnailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="thumbnailModalLabel">Change Thumbnail</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('teacher.thumbnail_update') }}" method="POST" enctype="multipart/form-data" class="mx-auto" style="max-width: 24rem;">
                    @csrf
                    <div class="mb-3">
                        <label for="update_thumbnail" class="form-label">
                            Thumbnail<span class="text-danger">*</span>
                        </label>
                        <input type="file" name="update_thumbnail" id="update_thumbnail" accept="image/*" class="form-control" required>
                        <input type="text" hidden id="thumbnail_post_id" name="post_id">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            Update
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="renameModal" tabindex="-1" aria-labelledby="renameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="renameModalLabel">Rename</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{ route('teacher.title_update') }}" method="POST" enctype="multipart/form-data" class="mx-auto" style="max-width: 24rem;">
                @csrf
                <div class="mb-3">
                    <label for="update_title" class="form-label">
                        Title<span class="text-danger">*</span>
                    </label>
                    <input type="text" name="update_title" id="update_title" class="form-control" required>
                    <input type="text" hidden id="title_post_id" name="post_id">
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
</div>
<div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="descriptionModalLabel">Description</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{ route('teacher.description_update') }}" method="POST" enctype="multipart/form-data" class="mx-auto" style="max-width: 24rem;">
                @csrf
                <div class="mb-3">
                    <label for="description_update" class="form-label">
                        Description<span class="text-danger">*</span>
                    </label>
                    <textarea name="description_update" id="description_update" rows="2" class="form-control" placeholder="Add a description...." required></textarea>
                
                    <input type="text" hidden id="description_post_id" name="post_id">
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
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
        $(".change_thumbnail_btn").on("click", function () {
            let post_id = $(this).val();
            $('#thumbnail_post_id').val(post_id)
        });
        $(".rename_btn").on("click", function () {
            let post_id = $(this).val();
            let title = $(this).data('title');

            $('#title_post_id').val(post_id);
            $('#update_title').val(title);
            
        });

    });

</script>


@endsection