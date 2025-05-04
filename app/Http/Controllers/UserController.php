<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\File;
use App\Models\Permission;
use App\Models\PermissionPost;
use App\Models\Post;
use App\Models\Reply;
use App\Models\Setting;
use App\Models\TeacherPost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use ZipArchive;

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

        return view('welcome', compact('topUsers', "topRatedPosts","posts"));
    }
    public function download_all($postId){
        $post = Post::findOrFail($postId);

        if ($post->pricing !== 'free') {
            abort(403, 'Downloads allowed for free posts only.');
        }
    
        // Fetch post title (or name) for the ZIP file name
        $zipFileName = $post->title . '_files.zip';  // Use post's title as the name
        $zipPath = public_path('uploads/' . $zipFileName);
    
        // Ensure the uploads directory exists
        $uploadsDir = public_path('uploads');
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0777, true); // Create the uploads directory if it doesn't exist
        }
    
        $files = File::where('post_id', $postId)->get();
    
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($files as $file) {
                // Path to the uploaded file in the public/uploads directory
                $filePath = public_path('uploads/' . $file->file_name);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $file->file_name);
                }
            }
            $zip->close();
        }
    
        // Return the ZIP file as a download response
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
    public function download_file($fileId){
        $file = File::findOrFail($fileId);
        $filePath = public_path('uploads/' . $file->file_name);
    
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
    
        return response()->download($filePath);
    }
    

    public function index() {

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
        $teacher_post = TeacherPost::all();
        
    
        return view('dashboard', compact('posts', 'teacher_post'));
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
        DB::table('users')
            ->where('id', $user->id)
            ->update(['favorites' => json_encode(array_values($favorites))]);
    
        return response()->json(['success' => true, 'favorites' => array_values($favorites)]);
    }
    
    
    public function myUploads(){
        $file_info = Setting::first();
        $userFiles = Post::where('user_id', Auth::id())
        ->with('permissionPosts.teacher')
        ->latest()
        ->get();
    

        foreach ($userFiles as $file) {
            $file->permissionData = $file->permissionPosts->map(function($p) {
                return [
                    'id' => $p->id,
                    'post_id' => $p->post_id,
                    'permission_post_status' => $p->permission_post_status,
                    'teacher_name' => optional($p->teacher)->name ?? 'Unknown Teacher',
                    'permission_note' => $p->permission_note,
                ];
            });
        }

        return view('my_uploads', compact('userFiles', 'file_info'));
    }
    // public function upload_post(Request $request) {
    //     $thumbnailName = null;
    //     if ($request->hasFile('thumbnail')) {
    //         $thumbnail = $request->file('thumbnail');
    //         $thumbnailName = time() . '_thumbnail_' . $thumbnail->getClientOriginalName();
    //         $thumbnail->move(public_path('uploads'), $thumbnailName);
    //     }
    
    //     $post = Post::create([
    //         'user_id' => auth()->id(),
    //         'title' => $request->title,
    //         'description' => $request->description,
    //         'tags' => $request->tags, 
    //         'pricing' => $request->pricing,
    //         'thumbnail' => $thumbnailName,
    //     ]);

    //     if ($request->hasFile('upload_files')) {
    //         $virustotalApiKey = '2aebda056846b0f7308a06dbf512c4980d14a23c5f1af48806f054c73651d4a5';
    //         foreach ($request->file('upload_files') as $file) {
    //             $originalName = $file->getClientOriginalName();
    //             $fileName = time() . '_' . $originalName;
    //             $uploadPath = public_path('uploads');
    //             $extension = strtolower($file->getClientOriginalExtension());
            
    //             // === Step 1: Upload to VirusTotal for scanning ===
    //             $response = Http::withHeaders([
    //                 'x-apikey' => $virustotalApiKey,
    //             ])->attach(
    //                 'file', file_get_contents($file->getRealPath()), $originalName
    //             )->post('https://www.virustotal.com/api/v3/files');
            
    //             if (!$response->successful()) {
    //                 return back()->with('error', 'Failed to scan file with VirusTotal.');
    //             }
            
    //             $scanData = $response->json();
    //             $analysisId = $scanData['data']['id'];
            
    //             // === Step 2: Wait for scan result ===
    //             sleep(15); // You can use a queue/job to do this async in production
            
    //             $result = Http::withHeaders([
    //                 'x-apikey' => $virustotalApiKey,
    //             ])->get("https://www.virustotal.com/api/v3/analyses/{$analysisId}");
            
    //             $resultData = $result->json();
            
    //             // === Step 3: Check if it's malicious ===
    //             $maliciousCount = $resultData['data']['attributes']['stats']['malicious'] ?? 0;
    //             if ($maliciousCount > 0) {
    //                 continue; // Skip saving this file
    //             }
            
    //             // === Step 4: Process the file ===
    //             if (str_starts_with($file->getMimeType(), 'image/')) {
    //                 $image = Image::make($file)
    //                     ->resize(null, 500, function ($constraint) {
    //                         $constraint->aspectRatio();
    //                         $constraint->upsize();
    //                     })
    //                     ->encode('jpg', 70);
            
    //                 if ($request->has('add_watermark')) {
    //                     $image->text('Watermark Text', 100, 100, function ($font) {
    //                         $font->file(public_path('fonts/arial.otf'));
    //                         $font->size(36);
    //                         $font->color('#ffffff');
    //                         $font->align('left');
    //                         $font->valign('top');
    //                     });
    //                 }
            
    //                 $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.jpg';
    //                 $image->save($uploadPath . '/' . $fileName);
    //             } else {
    //                 $file->move($uploadPath, $fileName);
    //             }
            
    //             File::create([
    //                 'post_id' => $post->id,
    //                 'file_name' => $fileName,
    //             ]);
    //         }
    //     }

    
    //     return back()->with('success', 'Post uploaded successfully.');
    // }
    public function upload_post(Request $request) {
        $thumbnailName = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = time() . '_thumbnail_' . $thumbnail->getClientOriginalName();
            $thumbnail->move(public_path('uploads'), $thumbnailName);
        }
    
        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags, 
            'pricing' => $request->pricing,
            'thumbnail' => $thumbnailName,
        ]);
        $file_info = Setting::first();
        if ($request->hasFile('upload_files')) {
            foreach ($request->file('upload_files') as $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . $originalName;
                $uploadPath = public_path('uploads');
                $extension = strtolower($file->getClientOriginalExtension());

                if (str_starts_with($file->getMimeType(), 'image/')) {
                    $image = Image::make($file)
                        ->resize(null, 500, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode('jpg', $file_info->file_resolution); 
                    if ($request->has('add_watermark')) {
                        $image->text('EPCST', 100, 100, function ($font) {
                            $font->file(public_path('fonts/arial.otf'));
                            $font->size(36);
                            $font->color('#ffffff');
                            $font->align('left');
                            $font->valign('top');
                        });
                    }

                    $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.jpg';
                    $image->save($uploadPath . '/' . $fileName);
                } else {
                    $file->move($uploadPath, $fileName);
                }

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
        $comments = Comment::with(['user', 'replies.user']) // eager load user and replies
            ->where('post_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('post_info', compact('post_info', 'file_info', 'comments'));
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
    public function ask_permission($user_id, $post_id, $receiver_id){
        // Check if already exists
        
        $updated = Permission::where('user_id', $user_id)
        ->where('post_id', $post_id)
        ->where('receiver_id', $receiver_id)
        ->update(['permission_status' => 0]);
    
        if (!$updated) {
            Permission::create([
                'user_id' => $user_id,
                'post_id' => $post_id,
                'receiver_id' => $receiver_id,
                'permission_status' => 0
            ]);
        }
    
        return back()->with('success', 'Permission requested successfully.');
    }
    public function reply_comment(Request $request, $comment_id,$user_id, $post_id){
        Reply::create([
            'comment_id' => $comment_id,
            'user_id' => $user_id,
            'post_id' => $post_id,
            'reply' => $request->reply
        ]);
    
        return back()->with('success', 'Reply successfully.');
    }
    
    
    public function uploaded_post_info($id){
        $post_info = Post::find($id);
        $file_info = File::where('post_id', $id)->get(); 
        return view('edit_post',compact('post_info', 'file_info'));
    }
    public function updated_note_post(Request $request){
        $post = Post::find($request->input('updated_post_id'));
        PermissionPost::where('post_id', $request->input('updated_post_id'))->delete();
        File::where('post_id', $request->input('updated_post_id'))->delete();
    
        if (!$post) {
            return back()->with('error', 'Post not found.');
        }
    
        // Handle thumbnail update if uploaded
        if ($request->hasFile('updated_thumbnail')) {
            $thumbnail = $request->file('updated_thumbnail');
            $thumbnailName = time() . '_thumbnail_' . $thumbnail->getClientOriginalName();
            $thumbnail->move(public_path('uploads'), $thumbnailName);
            $post->thumbnail = $thumbnailName;
        }
    
        // Update basic post data
        $post->title = $request->input('updated_title');
        $post->description = $request->input('updated_description');
        $post->tags = $request->input('updated_tags');
        $post->pricing = $request->input('updated_pricing');
        $post->permission_post = 'pending';
        $post->save();
    
        // Handle file uploads if any
        if ($request->hasFile('updated_upload_files')) {
            foreach ($request->file('updated_upload_files') as $file) {
                $originalName = $file->getClientOriginalName();
                $fileName = time() . '_' . $originalName;
                $uploadPath = public_path('uploads');
                $extension = strtolower($file->getClientOriginalExtension());
    
                if (str_starts_with($file->getMimeType(), 'image/')) {
                    $image = Image::make($file)
                        ->resize(null, 500, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode('jpg', 70);
    
                    if ($request->has('updated_add_watermark')) {
                        $image->text('EPCST', 100, 100, function ($font) {
                            $font->file(public_path('fonts/arial.otf'));
                            $font->size(36);
                            $font->color('#ffffff');
                            $font->align('left');
                            $font->valign('top');
                        });
                    }
    
                    $fileName = pathinfo($fileName, PATHINFO_FILENAME) . '.jpg';
                    $image->save($uploadPath . '/' . $fileName);
                } else {
                    $file->move($uploadPath, $fileName);
                }
    
                // Create new file record
                File::create([
                    'post_id' => $post->id,
                    'file_name' => $fileName,
                ]);
            }
        }
    
        return back()->with('success', 'Post updated successfully.');
    }
    
    public function rename_update(Request $request){
        $post_info = Post::findOrFail($request->post_id);
        PermissionPost::where('post_id', $request->post_id)->delete();
        $request->validate([
            'update_title' => 'required', 
        ]);
        $post_info->update([
            'title' => $request->update_title,
            'permission_post' => 'pending'
        ]);
        return redirect()->back()->with('success', 'Rename updated successfully.');
    }
    public function description_update(Request $request){
        $post_info = Post::findOrFail($request->post_id);
        PermissionPost::where('post_id', $request->post_id)->delete();
        $request->validate([
            'update_description' => 'required', 
        ]);
        $post_info->update([
            'description' => $request->update_description,
            'permission_post' => 'pending'
        ]);
        return redirect()->back()->with('success', 'Description updated successfully.');
    }
    
    public function price_update(Request $request){
        $post_info = Post::findOrFail($request->post_id);
        PermissionPost::where('post_id', $request->post_id)->delete();
        $request->validate([
            'update_pricing' => 'required', 
        ]);
        $post_info->update([
            'pricing' => $request->update_pricing,
            'permission_post' => 'pending'
        ]);
        return redirect()->back()->with('success', 'Price updated successfully.');
    }
    public function thumbnail_update(Request $request){
        $post_info = Post::findOrFail($request->post_id);
        PermissionPost::where('post_id', $request->post_id)->delete();
    
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
                'permission_post' => 'pending'
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
    public function account_file($post_id){
        $post_info = Post::find($post_id);
        $file_info = File::where('post_id', $post_id)->get(); 
        $comments = Comment::with(['user', 'replies.user']) // eager load user and replies
            ->where('post_id', $post_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('account_file', compact('post_info', 'file_info', 'comments'));
    }
    public function view_profile($user_id){
        $user_profile = User::find($user_id);
        $post_info = Post::where('user_id', $user_id)
            ->where('permission_post', 'approved')
            ->get();
        
        return view('view_profile', compact("user_profile","post_info"));
    }
    
    public function delete_file($post_id=null , $delete_id=null){
        $post_id = 2;
        $content = Post::find($post_id);
        $file = json_decode($content["file_name"]);
        return view('account');
    }
    public function favorites(){
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
    

    
        return view('favorites', compact('posts'));
    }
    public function notification(){
        $permissions = Permission::where('receiver_id', Auth::id())
            ->where('permission_status', 0)
            ->with(['requester', 'post'])
            ->orderBy('created_at', 'desc')
            ->get();
        $comments = Comment::whereIn('post_id', function($query) {
                $query->select('id')
                        ->from('posts')
                        ->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->get();
        $permission_approved = Permission::where('receiver_id', Auth::id())
            ->where('permission_status', 1)
            ->with(['requester', 'post'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('notification', compact('permissions', 'permission_approved', 'comments'));
    }
    public function approve($id){
        $permission = Permission::findOrFail($id);

        if (Auth::id() !== $permission->receiver_id) {
            abort(403);
        }

        $permission->permission_status = 1; // Approved
        $permission->save();

        return back()->with('success', 'Permission approved.');
    }

    public function deny($id){
        $permission = Permission::findOrFail($id);

        if (Auth::id() !== $permission->receiver_id) {
            abort(403);
        }

        $permission->permission_status = 2; // Denied
        $permission->save();

        return back()->with('error', 'Permission denied.');
    }

    

}
