<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\File;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function landingPage(){
        $topUsers = User::withCount('posts')
            ->orderByDesc('posts_count')
            ->limit(3)
            ->get();
        $topRatedPosts = Post::select('posts.id', 'posts.title', 'users.name as creator_name', DB::raw('COALESCE(AVG(comments.rating), 0) as avg_rating'))
            ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
            ->join('users', 'posts.user_id', '=', 'users.id')
            ->groupBy('posts.id', 'posts.title', 'users.name')
            ->orderByDesc('avg_rating')
            ->limit(3)
            ->get();

        return view('welcome', compact('topUsers', "topRatedPosts"));
    }
    public function index(){
        $posts = Post::with('user')
            ->where('user_id', '!=', auth()->id())
            ->where('permission_post','approved')
            ->latest()
            ->get();

        return view('dashboard', compact('posts'));
    }
    public function myUploads(){
        $userFiles = Post::where('user_id', Auth::id())->latest()->get();
        return view('my_uploads', compact('userFiles'));
    }
    public function upload_post(Request $request) {
        // $request->validate([
        //     'title' => 'required', 
        //     'tags' => 'required', 
        //     'pricing' => 'required|in:free,premium',
        //     'thumbnail' => 'required|mimes:jpg,jpeg,png|max:2048',
        //     'upload_files.*' => 'required|mimes:jpg,jpeg,png,pdf,docx,xlsx,mp4|max:2048',
        // ]);
      
        // dd($request->pricing);
        $thumbnailName = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = time() . '_thumbnail_' . $thumbnail->getClientOriginalName();
            $thumbnail->move(public_path('uploads'), $thumbnailName);
        }
    
        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'tags' => $request->tags, 
            'pricing' => $request->pricing,
            'thumbnail' => $thumbnailName,
        ]);
    
        if ($request->hasFile('upload_files')) {
            foreach ($request->file('upload_files') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $fileName);
    
                File::create([
                    'post_id' => $post->id,
                    'file_name' => $fileName,
                ]);
            }
        }
    
        return back()->with('success', 'Post uploaded successfully.');
    }
    
    
    public function post_info($id){
        $post_info = Post::find($id);
        $file_info = File::where('post_id', $id)->get(); 
        $comments = Comment::where('post_id', $id)->orderBy('created_at', 'desc')->get();
        return view('post_info', compact('post_info', 'file_info','comments'));
    }
    public function add_comment(Request $request, $user_id, $post_id){
        $request->validate([
            'comment' => 'required', 
            'rating' => 'required', 
        ]);

        Comment::create([
            'post_id' => $post_id,
            'user_id' => $user_id,
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);
        return back()->with('success', 'Post comment successfully.');
    }
    
    public function uploaded_post_info($id){
        $post_info = Post::find($id);
        $file_info = File::where('post_id', $id)->get(); 
        return view('edit_post',compact('post_info', 'file_info'));
    }
    public function rename_update(Request $request){
        $post_info = Post::findOrFail($request->post_id);
        $request->validate([
            'update_title' => 'required', 
        ]);
        $post_info->update([
            'title' => $request->update_title,
        ]);
        return redirect()->back()->with('success', 'Rename updated successfully.');
    }
    public function price_update(Request $request){
        $post_info = Post::findOrFail($request->post_id);
        $request->validate([
            'update_pricing' => 'required', 
        ]);
        $post_info->update([
            'pricing' => $request->update_pricing,
        ]);
        return redirect()->back()->with('success', 'Price updated successfully.');
    }
    public function thumbnail_update(Request $request){
        $post_info = Post::findOrFail($request->post_id);
    
        $request->validate([
            'update_thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($request->hasFile('update_thumbnail')) {
            if ($post_info->thumbnail && file_exists(public_path('uploads/' . $post_info->thumbnail))) {
                unlink(public_path('uploads/' . $post_info->thumbnail));
            }
            $thumbnail = $request->file('update_thumbnail');
            $thumbnailName = time() . '_thumbnail_' . $thumbnail->getClientOriginalName();
            $thumbnail->move(public_path('uploads'), $thumbnailName);
            $post_info->update([
                'thumbnail' => $thumbnailName,
            ]);
        }
        return redirect()->back()->with('success', 'Thumbnail updated successfully.');
    }
    public function delete_post($id){
        $post = Post::findOrFail($id);;
        if (!$post) {
            return redirect()->back()->with('error', 'Post not found.');
        }
        $post->delete();
        return redirect()->back()->with('success', 'Post deleted successfully.');
    }
    public function add_files(Request $request){
        $post_info = Post::findOrFail($request->post_id);
    
        $request->validate([
            'upload_files.*' => 'required|mimes:jpg,jpeg,png,pdf,docx,xlsx,mp4|max:2048',
            'tags' => 'required',
        ]);
        if ($request->hasFile('upload_files')) {
            foreach ($request->file('upload_files') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $fileName);
    
                File::create([
                    'post_id' => $post_info->id,
                    'file_name' => $fileName,
                ]);
            }
        }

        $existingTags = $post_info->tags ? explode(',', $post_info->tags) : [];
        $newTags = explode(',', $request->tags);
        $mergedTags = array_unique(array_merge($existingTags, $newTags)); 
    
        $post_info->update([
            'tags' => implode(',', $mergedTags),
        ]);
    
        return back()->with('success', 'Files uploaded successfully.');
    }
    
    public function delete_files($id){
        $file = File::findOrFail($id);;
        if (!$file) {
            return redirect()->back()->with('error', 'Post not found.');
        }
        $file->delete();
        return redirect()->back()->with('success', 'Post deleted successfully.');
    }
    
    public function account(){
        $userFiles = Post::with('user')
        ->where('user_id', '=', auth()->id()) 
        ->where('permission_post', '=', 'approved') 
        ->latest()
        ->get();
        return view('account', compact("userFiles"));
    }
    public function view_profile($user_id){
        $user_profile = User::find($user_id);
        $post_info = Post::where('user_id', $user_id)->get(); 
        return view('view_profile', compact("user_profile","post_info"));
    }
    
    public function delete_file($post_id=null , $delete_id=null){
        $post_id = 2;
        $content = Post::find($post_id);
        $file = json_decode($content["file_name"]);
        return view('account');
    }
    public function chatRoom(){
        return view('chat_room');
    }
    

}
