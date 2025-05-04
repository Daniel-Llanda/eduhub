<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\File;
use App\Models\PermissionPost;
use App\Models\Post;
use App\Models\TeacherPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
        $usersCount = User::all();
        $postsCount = Post::all();
    
        return view('teacher.dashboard', compact('users', 'posts', 'usersCount', 'postsCount'));
    }
    
    
    
    public function approved_post($userId,$teacherId,$postId){
        $user = User::findOrFail($userId);

        // Decode the teacher field (assuming it's stored as a JSON array)
        $teacherIds = json_decode($user->teacher, true);
    
        // Count how many teacher IDs are there
        $teacherCount = is_array($teacherIds) ? count($teacherIds) : 0;
    
        // Always insert the teacher's response first
        PermissionPost::create([
            'user_id' => $userId,
            'teacher_id' => $teacherId,
            'post_id' => $postId,
            'permission_post_status' => 1, // assuming 1 means approved
        ]);
    
        // After adding, count how many permissions exist
        $permission_count = PermissionPost::where('post_id', $postId)->count();
    
        // Check if any denied (2 means denied)
        $denied_post = PermissionPost::where('post_id', $postId)
                            ->where('permission_post_status', 2)
                            ->exists();
    
        // If this is the last teacher submitting
        if ($teacherCount == $permission_count) {
            $post = Post::findOrFail($postId);
            $post->permission_post = $denied_post ? 'denied' : 'approved';
            $post->save();
        }

        return redirect()->back()->with('success', 'Approved post successfully.');
    }
    public function denied_post(Request $request)
    {
        $user = User::findOrFail($request->input('user_id'));
    
        // Decode the teacher field
        $teacherIds = json_decode($user->teacher, true);
        $teacherCount = is_array($teacherIds) ? count($teacherIds) : 0;
    
        // Save the denial with note
        PermissionPost::create([
            'user_id' => $request->input('user_id'),
            'teacher_id' => $request->input('teacher_id'),
            'post_id' => $request->input('post_id'),
            'permission_post_status' => 2, // denied
            'permission_note' => $request->input('permission_note'),
        ]);
    
        // Count how many permissions have been submitted
        $permission_count = PermissionPost::where('post_id', $request->input('post_id'))->count();
    
        // Check if any teacher has denied
        $denied_post = PermissionPost::where('post_id', $request->input('post_id'))
                            ->where('permission_post_status', 2)
                            ->exists();
    
        // If this is the last teacher
        if ($teacherCount == $permission_count) {
            $post = Post::findOrFail($request->input('post_id'));
            $post->permission_post = $denied_post ? 'denied' : 'approved';
            $post->save();
        }
    
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
                    
        $posts_history = Post::whereIn('permission_post', ['approved', 'denied'])
                    ->whereIn('user_id', $users->pluck('id')) 
                    ->get();
                
    
        return view('teacher.student', compact('users', 'posts' , 'posts_history'));
    }
    public function student_post(){
        $posts = Post::select(
            'posts.*',
            DB::raw('COALESCE(AVG(comments.rating), 0) as avg_rating'),
            DB::raw('COUNT(DISTINCT comments.id) as comments_count'),
            DB::raw('COUNT(DISTINCT replies.id) as replies_count'),
            DB::raw('(COUNT(DISTINCT comments.id) + COUNT(DISTINCT replies.id)) as total_engagement') // <-- total engagement
        )
        ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
        ->leftJoin('replies', 'posts.id', '=', 'replies.post_id')
        ->where('posts.permission_post', 'approved')
        ->with('user')
        ->groupBy('posts.id')
        ->latest('posts.created_at')
        ->get();

        return view('teacher.student_post', compact('posts'));
    }
    public function student_post_info($id){
        $post_info = Post::find($id);
        $file_info = File::where('post_id', $id)->get(); 
        $comments = Comment::with(['user', 'replies.user']) // eager load user and replies
            ->where('post_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('teacher.student_post_info', compact('post_info', 'file_info', 'comments'));
    }
    public function toggle_favorite($post_id){
        $user = auth()->user();
        $favorites = $user->favorites ? json_decode($user->favorites, true) : [];
    
        if (in_array($post_id, $favorites)) {
            $favorites = array_diff($favorites, [$post_id]);
        } else {
            $favorites[] = $post_id;
        }
    
        // Save updated favorites to database
        DB::table('teachers')
            ->where('id', $user->id)
            ->update(['favorites' => json_encode(array_values($favorites))]);
    
        return response()->json(['success' => true, 'favorites' => array_values($favorites)]);
    }
    public function post_favorites(){
        $favoriteIds = json_decode(auth()->user()->favorites ?? '[]', true);
        $posts = Post::select(
            'posts.*',
            DB::raw('COALESCE(AVG(comments.rating), 0) as avg_rating'),
            DB::raw('COUNT(comments.id) as comments_count')
        )
        ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
        ->where('posts.permission_post', 'approved')
        ->whereIn('posts.id', $favoriteIds)
        ->with('user')
        ->groupBy('posts.id')
        ->latest('posts.created_at')
        ->get();
    

    
        return view('teacher.post_favorites', compact('posts'));
    }
    public function teacher_upload_post(Request $request){
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'thumbnail' => 'required|image',
            'upload_files.*' => 'required|file',
            'tags' => 'required|string',
        ]);
    
            // Upload thumbnail
      // Store thumbnail with original filename
        $thumbnailName = time() . '_' . $request->file('thumbnail')->getClientOriginalName();
        $thumbnailPath = $request->file('thumbnail')->storeAs('thumbnails', $thumbnailName, 'public');

        // Store uploaded files with original filenames
        $uploadedFiles = [];
        if ($request->hasFile('upload_files')) {
            foreach ($request->file('upload_files') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $uploadedFiles[] = $file->storeAs('uploads', $filename, 'public');
            }
        }

    
        // Save to database
        TeacherPost::create([
            'teacher_id' => Auth::id(), // assumes the teacher is authenticated
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail' => $thumbnailPath,
            'tags' => $request->tags,
            'file_name' => json_encode($uploadedFiles),
        ]);
    
        return redirect()->back()->with('success', 'Post uploaded successfully!');
    }
    
    public function thumbnail_update(Request $request){
        // Validate the request
        $request->validate([
            'post_id' => 'required|integer|exists:teacher_posts,id',
            'update_thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Find the post
        $post = TeacherPost::findOrFail($request->input('post_id'));
    
        // Handle file upload
        if ($request->hasFile('update_thumbnail')) {
            $file = $request->file('update_thumbnail');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('thumbnails', $filename, 'public');
    
            // Optionally delete old thumbnail here if needed
            // Storage::disk('public')->delete($post->thumbnail);
    
            // Update post's thumbnail path
            $post->thumbnail = $path;
            $post->save();
        }
    
        return back();
    }
    public function title_update(Request $request){
        $request->validate([
            'post_id' => 'required|integer|exists:teacher_posts,id',
            'update_title' => 'required|string',
        ]);
    

        $post = TeacherPost::findOrFail($request->input('post_id'));
    
        $post->title = $request->input('update_title');
        $post->save();
    
        return back();
    }
    public function description_update(Request $request){
        // $request->validate([
        //     'post_id' => 'required|integer|exists:teacher_posts,id',
        //     'description_update' => 'required|string',
        // ]);
    

        $post = TeacherPost::findOrFail($request->input('post_id'));
        // dd( $request->input('description_update'));
    
        $post->description = $request->input('description_update');
        $post->save();
    
        return back();
    }
    
    public function upload_post(){
        $userFiles = TeacherPost::where('teacher_id', Auth::id())
            ->latest()
            ->get();
        return view('teacher.upload_post', compact('userFiles'));
        
    }
    
    
    
    
}
