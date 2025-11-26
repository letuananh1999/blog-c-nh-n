<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::withCount(['category', 'tags', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        // return PostResource::collection($posts); // nếu là API
        return view('admin.post.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.create', compact('categories', 'tags'));
    }

    public function store(StorePostRequest $request)
    {
        $post = Post::create([
            'title' => $request->title,
            // 'content' => $request->content,
            'category_id' => $request->category_id,
            'user_id' => Auth()->id = 1,
            'slug' => Str::slug($request->title),
            'is_published' => $request->has('is_published'),
        ]);
        if ($request->has('tags')) {
            $post->tags()->attach($request->tags);
        }
        return redirect()->route('posts.index')->with('success', 'Tạo bài viết thành công.');
    }

    public function show(Post $post)
    {
        return new PostResource($post->load(['category', 'tags', 'user', 'comments']));
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('posts.edit', compact('post', 'categories', 'tags'));
    }


    public function update(StorePostRequest $request, $id)
    {
        $post = Post::findOrFail($id);
        $post->update([
            'title' => $request->title,
            // 'content' => $request->content,
            'category_id' => $request->category_id,
            'slug' => Str::slug($request->title),
            'is_published' => $request->has('is_published'),
        ]);
        if ($request->has('tags')) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->detach();
        }
        return redirect()->route('posts.index')->with('success', 'Cập nhật bài viết thành công.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Xóa bài viết thành công.']);
    }
}
