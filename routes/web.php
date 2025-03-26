<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Models\Admin;
use App\Models\Teacher;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [UserController::class, 'landingPage']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('dashboard');
    Route::post('/upload-post', [UserController::class, 'upload_post'])->name('upload_post');
    Route::get('/post-info/{id}', [UserController::class, 'post_info'])->name('post_info');
    Route::get('/uploaded-post-info/{id}', [UserController::class, 'uploaded_post_info'])->name('uploaded_post_info');

    Route::post('/update-price', [UserController::class, 'price_update'])->name('price_update');
    Route::post('/change-thumbnail', [UserController::class, 'thumbnail_update'])->name('thumbnail_update');
    Route::post('/rename', [UserController::class, 'rename_update'])->name('rename_update');
    Route::get('/delete-post/{id}', [UserController::class, 'delete_post'])->name('delete_post');
    Route::get('/delete-file/{id}', [UserController::class, 'delete_files'])->name('delete_file');

    

    Route::post('/add-files', [UserController::class, 'add_files'])->name('add_files');


    Route::post('/comment/{user_id}/{post_id}', [UserController::class, 'add_comment'])->name('add_comment');


    

    Route::get('/my-uploads', [UserController::class, 'myUploads'])->name('my_uploads');
    Route::get('/account', [UserController::class, 'account'])->name('account');
    Route::get('/view-profile/{id}', [UserController::class, 'view_profile'])->name('view_profile');


    Route::get('/chat-room', [UserController::class, 'chatRoom'])->name('chat_room');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// useless routes
// Just to demo sidebar dropdown links active states.
Route::get('/buttons/text', function () {
    return view('buttons-showcase.text');
})->middleware(['auth'])->name('buttons.text');

Route::get('/buttons/icon', function () {
    return view('buttons-showcase.icon');
})->middleware(['auth'])->name('buttons.icon');

Route::get('/buttons/text-icon', function () {
    return view('buttons-showcase.text-icon');
})->middleware(['auth'])->name('buttons.text-icon');

require __DIR__ . '/auth.php';

// Route::get('/admin/dashboard', function () {
//     return view('admin.dashboard');
// })->middleware(['auth:admin', 'verified'])->name('admin.dashboard');




Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/status/{id}', [AdminController::class, 'status_update'])->name('admin.status_update');
    Route::post('/admin/assign-teacher', [AdminController::class, 'assign_teacher'])->name('admin.assign_teacher');

    // Route::get('/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    // Route::patch('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    // Route::delete('/profile', [AdminProfileController::class, 'destroy'])->name('admin.profile.destroy');
});

require __DIR__ . '/adminauth.php';


Route::middleware('auth:teacher')->group(function () {
    Route::get('/teacher/dashboard', [TeacherController::class, 'index'])->name('teacher.dashboard');
    Route::get('/teacher/approved-post/{id}', [TeacherController::class, 'approved_post'])->name('teacher.approved_post');
    Route::get('/teacher/denied-post/{id}', [TeacherController::class, 'denied_post'])->name('teacher.denied_post');
    
    
    
    // Route::get('/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    // Route::patch('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    // Route::delete('/profile', [AdminProfileController::class, 'destroy'])->name('admin.profile.destroy');
});

require __DIR__ . '/teacherauth.php';
