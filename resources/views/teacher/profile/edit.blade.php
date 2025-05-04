
@extends('teacher.layout.app')
@section('main_teacher')
<div class="container-fluid px-4">
    <h1 class="mt-4">Profile</h1>
   
    <div class="d-flex flex-column gap-4">
        <div class="p-4 p-sm-5 bg-white shadow rounded">
            <div class="w-100" style="max-width: 600px;">
                @include('teacher.profile.partials.update-profile-information-form')
            </div>
        </div>
    
        <div class="p-4 p-sm-5 bg-white shadow rounded">
            <div class="w-100" style="max-width: 600px;">
                @include('teacher.profile.partials.update-password-form')
            </div>
        </div>
    </div>
    
</div>

@endsection
