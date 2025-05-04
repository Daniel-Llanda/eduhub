
@extends('admin.layout.app')
@section('main_admin')

<div class="container-fluid px-4">
    <h1 class="mt-4">Settings</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Settings</li>
    </ol>
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card text-white p-3">
                <form action="{{route('admin.file_size')}}" method="POST">
                    @csrf
                    <select class="form-select" name="file_size" required>
                        <option disabled selected>File size</option>
                        <option value="10">10mb</option>
                        <option value="20">20mb</option>
                        <option value="30">30mb</option>
                        <option value="40">40mb</option>
                        <option value="50">50mb</option>
                    </select>
                    <div class="d-flex justify-content-between">
                        <p class="text-dark">Current: {{ $file_info->file_size ?? 'Not set' }}mb</p>
                        <button type="submit" class="btn btn-primary btn-sm mt-2 ">Submit</button>
                    </div>
                    
                </form>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card text-white p-3">
                <form action="{{route('admin.file_resolution')}}" method="POST">
                    @csrf
                    <select class="form-select" name="file_resolution" required>
                        <option disabled selected>File resolution</option>
                        <option value="50">50</option>
                        <option value="60">60</option>
                        <option value="70">70</option>
                        <option value="80">80</option>
                        <option value="90">90</option>
                    </select>
                    <div class="d-flex justify-content-between">
                        <p class="text-dark">Current: {{ $file_info->file_resolution ?? 'Not set' }}</p>
                        <button type="submit" class="btn btn-primary btn-sm mt-2 ">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection