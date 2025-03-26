<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function index(){
        $teacher = Auth::user();
    
        // Get users related to the authenticated teacher
        $users = User::where('teacher', $teacher->id)->get();
    
        // Get only pending posts that belong to the teacher's users
        $posts = Post::where('permission_post', 'pending')
                    ->whereIn('user_id', $users->pluck('id')) // Get posts only for these users
                    ->get();
    
        return view('teacher.dashboard', compact('users', 'posts'));
    }
    
    
    public function approved_post($id){
        $post = Post::findOrFail($id);

        $post->permission_post = 'approved';
        $post->save();

        return redirect()->back()->with('success', 'Approved post successfully.');
    }
    public function denied_post($id){
        $post = Post::findOrFail($id);
        $post->permission_post = 'denied';
        $post->save();

        return redirect()->back()->with('success', 'Denied post successfully.');
    }

    
}
