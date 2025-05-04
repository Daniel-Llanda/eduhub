<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Setting;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminController extends Controller
{
    public function index(){
        // $users = User::where('status', 'not_verified')->get();
        // return view('admin.dashboard', compact('users'));
        $users = User::all();
        $teachers = Teacher::all();
        $posts = Post::all();
        return view('admin.dashboard', compact('users', "teachers", "posts"));
    }
    public function status_update($id){
        $user = User::find($id);
        if ($user) {
            if ($user->status) {
                $user->status = 0;
            }else{
                $user->status = 1;
            }
            $user->save();
        }
        // dd($user);
        return back();
    }
    public function assign_teacher(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'teacher_id.*' => 'required', 
        ]); 
        $user_info = User::findOrFail($request->user_id);
        
        
        $user_info->update([
            'teacher' => $request->teacher_id,
        ]);
    
        return redirect()->back()->with('success', 'Assigned Teacher successfully.');
    }
    public function teacher(){
        $teachers = Teacher::all();
        return view('admin.teacher', compact('teachers'));
    }
    public function add_teacher(Request $request){
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        Teacher::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return redirect()->back()->with('success', 'Added Teacher successfully.');
        
    }
    public function settings(){
        $file_info = Setting::first(); // Get the only settings row
        return view('admin.settings', compact('file_info'));
    }
    public function file_size(Request $request) {
        $setting = Setting::first();

        if ($setting) {
            // If it exists, update it
            $setting->update([
                'file_size' => $request->file_size,
            ]);
        } else {
            // If it doesn't exist, create it
            Setting::create([
                'file_size' => $request->file_size,
            ]);
        }

        return back()->with('success', 'File size updated successfully!');
    }
    
    public function file_resolution(Request $request){
        $setting = Setting::first();

        if ($setting) {
            // If it exists, update it
            $setting->update([
                'file_size' => $request->file_size,
            ]);
        } else {
            // If it doesn't exist, create it
            Setting::create([
                'file_resolution' => $request->file_resolution,
            ]);
        }

        return back()->with('success', 'File size updated successfully!');
    }
    
    
}
