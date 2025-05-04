<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherProfileController;
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
    Route::post('/favorite/{id}', [UserController::class, 'toggle_favorite'])->name('favorite_toggle');


    Route::post('/upload-post', [UserController::class, 'upload_post'])->name('upload_post');
    Route::get('/post-info/{id}', [UserController::class, 'post_info'])->name('post_info');
    Route::get('/download-all-files/{post}', [UserController::class, 'download_all'])->name('download_all_files');
    Route::get('/download-file/{fileId}', [UserController::class, 'download_file'])->name('file_download');


    Route::get('/uploaded-post-info/{id}', [UserController::class, 'uploaded_post_info'])->name('uploaded_post_info');
    
    Route::post('/upload-note-post', [UserController::class, 'updated_note_post'])->name('updated_note_post');


    Route::post('/update-price', [UserController::class, 'price_update'])->name('price_update');
    Route::post('/change-thumbnail', [UserController::class, 'thumbnail_update'])->name('thumbnail_update');
    Route::post('/rename', [UserController::class, 'rename_update'])->name('rename_update');
    Route::post('/description', [UserController::class, 'description_update'])->name('description_update');
    Route::get('/delete-post/{id}', [UserController::class, 'delete_post'])->name('delete_post');
    Route::get('/delete-file/{id}', [UserController::class, 'delete_files'])->name('delete_file');

    Route::post('/add-files', [UserController::class, 'add_files'])->name('add_files');

    Route::post('/comment/{user_id}/{post_id}', [UserController::class, 'add_comment'])->name('add_comment');
    Route::get('/ask-permission/{user_id}/{post_id}/{receiver_id}', [UserController::class, 'ask_permission'])->name('ask_permission');
    Route::post('/comment/{comment_id}/{user_id}/{post_id}', [UserController::class, 'reply_comment'])->name('reply_comment');

    Route::get('/my-uploads', [UserController::class, 'myUploads'])->name('my_uploads');
    Route::get('/account', [UserController::class, 'account'])->name('account');
    Route::get('/account-file/{post_id}', [UserController::class, 'account_file'])->name('account_file');
    
    Route::get('/view-profile/{id}', [UserController::class, 'view_profile'])->name('view_profile');


    Route::get('/favorites', [UserController::class, 'favorites'])->name('favorites');
    Route::get('/notification', [UserController::class, 'notification'])->name('notification');
    Route::get('/permission/approve/{id}', [UserController::class, 'approve'])->name('permission_approve');
    Route::get('/permission/deny/{id}', [UserController::class, 'deny'])->name('permission_deny');

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

    Route::get('/admin/teacher', [AdminController::class, 'teacher'])->name('admin.teacher');

    Route::post('/admin/add-teacher', [AdminController::class, 'add_teacher'])->name('admin.teacher.register');

    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/file-size', [AdminController::class, 'file_size'])->name('admin.file_size');
    Route::post('/admin/file-resolution', [AdminController::class, 'file_resolution'])->name('admin.file_resolution');

    Route::get('/admin-profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/admin-profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    // Route::delete('/profile', [AdminProfileController::class, 'destroy'])->name('admin.profile.destroy');
});

require __DIR__ . '/adminauth.php';


Route::middleware('auth:teacher')->group(function () {
    Route::get('/teacher/dashboard', [TeacherController::class, 'index'])->name('teacher.dashboard');
    Route::get('/teacher/approved-post/{userId}/{teacherId}/{postId}', [TeacherController::class, 'approved_post'])->name('teacher.approved_post');

    Route::post('/teacher/denied-post', [TeacherController::class, 'denied_post'])->name('teacher.denied_post');

    Route::get('/teacher/student', [TeacherController::class, 'student'])->name('teacher.student');
    Route::get('/teacher/student-post', [TeacherController::class, 'student_post'])->name('teacher.student_post');
    Route::get('/student-post-info/{id}', [TeacherController::class, 'student_post_info'])->name('teacher.student_post_info');
    Route::post('/teacher_favorite/{id}', [TeacherController::class, 'toggle_favorite'])->name('favorite_toggle');
    Route::get('/teacher-post-favorites', [TeacherController::class, 'post_favorites'])->name('teacher.post_favorites');

    Route::get('/teacher/upload_post', [TeacherController::class, 'upload_post'])->name('teacher.upload_post');

    Route::post('/teacher/upload-post', [TeacherController::class, 'teacher_upload_post'])->name('teacher.teacher_upload_post');
    Route::post('/teacher/thumbnail-update', [TeacherController::class, 'thumbnail_update'])->name('teacher.thumbnail_update');
    Route::post('/teacher/title-update', [TeacherController::class, 'title_update'])->name('teacher.title_update');
    Route::post('/teacher/description-update', [TeacherController::class, 'description_update'])->name('teacher.description_update');
    
    Route::get('/teacher-profile', [TeacherProfileController::class, 'edit'])->name('teacher.profile.edit');
    Route::patch('/teacher-profile', [TeacherProfileController::class, 'update'])->name('teacher.profile.update'); 
    // Route::delete('/profile', [AdminProfileController::class, 'destroy'])->name('admin.profile.destroy');
});

require __DIR__ . '/teacherauth.php';
