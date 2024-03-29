<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Jobs\AddNewPost;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(){
        $posts = Post::whereIn("status",["active","disActive"])->withCount("replies")->latest()->get();
        return view('dashboard.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('dashboard.posts.create');

    }//end of create

    public function store(Request $request)
    {
        $request->validate([
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'status' => 'required|in:active,disActive',
            'image' => 'nullable|mimes:jpeg,png,jpg|max:4096',
        ]);
        $postData = $request->only(["title_en","title_ar","description_en","description_ar","status"]);
        $postData["user_id"] = auth()->id();
        try{
            DB::beginTransaction();
            $post = Post::query()->firstOrCreate($postData);
            if ($request->has("image") && !is_null($request->image)){
                deleteOldFiles("uploads/posts/".$post->id."/image");
                $post->update(["image" => uploadImage($request->image,"uploads/posts/".$post->id."/image/".generateBcryptHash($post->id)."/image")]);
            }
            DB::commit();
            session()->flash('success', __('site.updated_successfully'));
            $recipients = User::where("role","student")->whereNotNull("device_token")->chunk(50,function ($data) use ($post){
                dispatch(new AddNewPost($data,$post));
            });
            return redirect()->route('dashboard.posts.index');
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception);
        }
    }//end of store

    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('dashboard.posts.edit', compact('post'));
    }//end of user
    public function update(Request $request, $id)
    {
        $request->validate([
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'status' => 'required|in:active,disActive',
            'image' => 'nullable|mimes:jpeg,png,jpg|max:4096',
        ]);
        $postData = $request->only(["title_en","title_ar","description_en","description_ar","status"]);

        try{
            DB::beginTransaction();
            $post = Post::query()->whereId($id)->first();
            $post->update($postData);
            if ($request->has("image") && !is_null($request->image)){
                deleteOldFiles("uploads/posts/".$post->id."/image");
                $post->update(["image" => uploadImage($request->image,"uploads/posts/".$post->id."/image/".generateBcryptHash($post->id)."/image")]);
            }
            DB::commit();
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.posts.index');
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception);
        }
    }//end of update
    public  function destroy($id)
    {
        $post = Post::find($id);
        $post->update(["status" => "deleted"]);
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.posts.index');
    }//end of destroy


}//end of controller
