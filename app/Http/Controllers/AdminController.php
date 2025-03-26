<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(){
        // $users = User::where('status', 'not_verified')->get();
        // return view('admin.dashboard', compact('users'));
        $users = User::all();
        $teachers = Teacher::all();
        return view('admin.dashboard', compact('users', "teachers"));
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
            'teacher_id' => 'required|exists:teachers,id', 
        ]);
    
        $user_info = User::findOrFail($request->user_id);
        
        $user_info->update([
            'teacher' => $request->teacher_id,
        ]);
    
        return redirect()->back()->with('success', 'Assigned Teacher successfully.');
    }
    
    
}
