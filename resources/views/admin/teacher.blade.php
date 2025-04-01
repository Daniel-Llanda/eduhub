
@extends('admin.layout.app')
@section('main_admin')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
<div class="container-fluid px-4">
    <h1 class="mt-4">Teacher</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Teacher</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                Teachers
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTeacherModal">
                    Add Teacher
                </button>
            </div>
        <!-- Modal -->
        <div class="modal fade" id="addTeacherModal" tabindex="-1" aria-labelledby="addTeacherModalLabel" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTeacherModalLabel">Add Teacher</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.teacher.register') }}">
                            @csrf
                        
                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-12">
                                    <label for="name" class="form-label">{{ __('Name') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="{{ __('Name') }}">
                                    </div>
                                </div>
                        
                                <!-- Email Address -->
                                <div class="col-12">
                                    <label for="email" class="form-label">{{ __('Email') }}</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required placeholder="{{ __('Email') }}">
                                    </div>
                                </div>
                        
                                <!-- Password -->
                                <div class="col-12">
                                    <label for="password" class="form-label">{{ __('Password') }}</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" required autocomplete="new-password" placeholder="{{ __('Password') }}">
                                    </div>
                                </div>
                        
                                <!-- Confirm Password -->
                                <div class="col-12">
                                    <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required placeholder="{{ __('Confirm Password') }}">
                                    </div>
                                </div>
                        
                                <!-- Submit Button -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-person-plus"></i> {{ __('Register') }}
                                    </button>
                                </div>
                        
                            </div>
                        </form>
                        
                    </div>
                  
                </div>
            </div>
        </div>
        </div>
        <div class="card-body">
            <table id="myTable"  class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach ($teachers as $teacher)
                        <tr>
                            <td>
                                {{$teacher->name}}
                            </td>
                            <td>
                                {{$teacher->email}}
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
    });
</script>

@endsection