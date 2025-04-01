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
    
        // Get users where the teacher's ID exists in the JSON column
        $users = User::whereJsonContains('teacher', strval($teacher->id))->get();
    
        // Get pending posts that belong to the teacher's users
        $posts = Post::where('permission_post', 'pending')
                    ->whereIn('user_id', $users->pluck('id')) 
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
    public function student(){
        $teacher = Auth::user();
    
        // Get users where the teacher's ID exists in the JSON column
        $users = User::whereJsonContains('teacher', strval($teacher->id))->get();
    
        // Get pending posts that belong to the teacher's users
        $posts = Post::where('permission_post', 'pending')
                    ->whereIn('user_id', $users->pluck('id')) 
                    ->get();
    
        return view('teacher.student', compact('users', 'posts'));
    }

    
}
