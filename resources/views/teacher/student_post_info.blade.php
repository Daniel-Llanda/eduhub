
@extends('teacher.layout.app')
@section('main_teacher')


<div class="container-fluid px-4">
    <h1 class="mt-4">Student Post</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"> <a href="{{route('teacher.student_post')}}" class="text-decoration-none">Student</a> - {{$post_info->title}}</li>
    </ol>
    <div class="p-4 bg-light rounded shadow-sm mb-3 overflow-hidden">
        <div class="row g-4 pb-3">
            @foreach ($file_info as $file)
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="bg-white p-3 rounded shadow-sm transition">
                        <p class="text-end text-uppercase mb-2 {{ $file->pricing == 'exclusive' ? 'text-success' : 'text-primary' }}">
                            {{ $file->post->pricing }}
                        </p>
                        <div class="p-3 text-center">
                            @php $fileUrl = asset('uploads/' . $file->file_name); @endphp
                            
                            <p class="text-truncate small mb-2">{{ preg_replace('/^[^_]+_/', '', $file->file_name) }}</p>
                            @if ($post_info->pricing == 'free')
                                <a href="#" class="btn btn-success btn-sm">
                                    Download
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    
        <p class="mb-3 {{ $file->pricing == 'exclusive' ? 'text-success' : 'text-primary' }}">
            {{ $post_info->description }}
        </p>
    
        @php $fileCount = $post_info->files()->count(); @endphp
    
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex flex-column gap-1">
                <p class="h5 fw-bold text-dark">
                    {{ $post_info->user->name }}
                </p>
                <p class="small text-muted">{{ $post_info->created_at->format('M d, Y - h:i:s') }}</p>
            </div>
    
            <div class="d-flex flex-column gap-1 text-end">
                <p class="small mb-1">Total Files: {{ $fileCount }}</p>
                @if ($post_info->pricing == 'free')
                    <a href="#"
                        class="btn btn-sm {{ $post_info->pricing == 'exclusive' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                        Download
                    </a>
                @endif
               
            </div>
        </div>
    </div>
    
    <div class="p-4 bg-white rounded shadow-sm my-3 overflow-hidden">
        <h2 class="h5 mb-3 fw-semibold text-dark">Ratings</h2>
    
        @foreach ($comments as $comment)
            <div class="mb-4">
                <div class="border p-3 rounded mb-2 bg-light">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="fw-semibold mb-1 text-dark">{{ $comment->user->name }} - {{ $comment->rating }}/10</p>
                            <p class="mb-0 text-muted">{{ $comment->comment }}</p>
                        </div>
                        <h6 class="small text-muted">{{ $comment->created_at->format('M d Y') }}</h6>
                    </div>
    
                    @foreach ($comment->replies as $reply_comment)
                        <div class="mt-3 ms-4">
                            <div class="border p-3 rounded bg-white">
                                <div class="d-flex justify-content-between">
                                    <p class="fw-semibold mb-1 small text-dark">{{ $reply_comment->user->name }}</p>
                                    <p class="small text-muted mb-0">{{ $reply_comment->created_at->format('M d Y') }}</p>
                                </div>
                                <p class="small text-secondary mt-1 mb-0">{{ $reply_comment->reply }}</p>
                            </div>
                        </div>
                    @endforeach
    
                </div>
            </div>
        @endforeach
    </div>
    
    
    
    
</div>



@endsection