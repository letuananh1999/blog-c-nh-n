<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with(['category', 'tags', 'user'])->latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        //
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['title']);
        $post = Post::create($data);

        $post = Post::create($data);

        if ($request->has('tags')) {
            $post->tags()->sync($data['tags']);
        }

        return redirect()->route('posts.index')->with('success', 'Tạo bài viết thành công.');
    }

    public function show(Post $post)
    {
        return new PostResource($post->load(['category', 'tags', 'user', 'comments']));
    }

    public function edit(Post $post)
    {
        return new PostResource($post->load(['category', 'tags', 'user']));
    }


    public function update(StorePostRequest $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->update($request->all());
        return response()->json($post);
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Xóa bài viết thành công.']);
    }
}
