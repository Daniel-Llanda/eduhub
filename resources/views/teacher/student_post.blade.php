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
    <h1 class="mt-4">Student Post</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Student</li>
    </ol>
    @if ($posts->isNotEmpty())
        <div class="row mt-4">
            @foreach ($posts as $post)
                @php
                    $tagsArray = explode(',', $post->tags);
                    $tagClasses = implode(' ', array_map(fn($tag) => strtolower(trim($tag)), $tagsArray));
                    $isFavorite = in_array($post->id, json_decode(auth()->user()->favorites ?? '[]', true));
                @endphp

                <div class="col-12 col-md-6 col-xl-3 mb-4 {{ $tagClasses }} all">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">{{ $post->user->name }}</small>
                                <small class="text-muted">{{ number_format($post->avg_rating, 1) }}/10</small>
                            </div>

                            <div class="text-center mb-3">
                                <a href="{{ route('teacher.student_post_info', $post->id) }}">
                                    <img src="{{ asset('uploads/' . $post->thumbnail) }}" alt="Latest Upload"
                                        class="img-fluid rounded {{ $post->pricing == 'exclusive' ? 'blur' : '' }}"
                                        style="max-width: 100px; height: 100px; object-fit: cover;">
                                </a>
                            </div>

                            <h6 class="card-title text-center">{{ $post->title }}</h6>

                            <div class="d-flex justify-content-between align-items-center my-2">
                                <span class="badge {{ $post->pricing == 'exclusive' ? 'bg-primary' : 'bg-success' }} text-uppercase">
                                    {{ $post->pricing }}
                                </span>
                                <div class="d-flex align-items-center gap-2">
                                    <button class="btn btn-sm p-0 favorite-btn" data-id="{{ $post->id }}" style="border: none; background: none;">
                                        @if($isFavorite)
                                            <!-- Filled favorite -->
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="text-warning" width="20" height="20" viewBox="0 0 24 24">
                                                <path d="M6.32 2.577a49.255 49.255 0 0 1 11.36 0c1.497.174 2.57 1.46 2.57 2.93V21a.75.75 0 0 1-1.085.67L12 18.089l-7.165 3.583A.75.75 0 0 1 3.75 21V5.507c0-1.47 1.073-2.756 2.57-2.93Z"/>
                                            </svg>
                                        @else
                                            <!-- Outline favorite -->
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" width="20" height="20" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z"/>
                                            </svg>
                                        @endif
                                    </button>
                                    <i class="bi bi-chat-dots"></i>
                                    <small>{{ $post->total_engagement }}</small>
                                </div>
                            </div>

                            <div class="mt-2">
                                <small class="text-muted">Tags:</small>
                                <div class="d-flex flex-wrap gap-1 mt-1">
                                    @foreach ($tagsArray as $tag)
                                        <span class="badge bg-success text-light py-1">{{ trim($tag) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>
    @else
        <p class="text-center mt-4">No files uploaded yet.</p>
    @endif

    
</div>
<script>
    $('.favorite-btn').on('click', function () {
        var postId = $(this).data('id');
        var btn = $(this);

        $.ajax({
            url: '/teacher_favorite/' + postId,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                // Optionally reload the button to update icon
                location.reload(); // or dynamically swap SVG
            }
        });
    });
</script>



@endsection