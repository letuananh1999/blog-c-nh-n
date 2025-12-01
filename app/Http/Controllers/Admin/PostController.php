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
        try {
            $post = Post::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'excerpt' => $request->excerpt,
                'content' => $request->content,
                'category_id' => $request->category_id,
                'user_id' => Auth::id(),
                'thumbnail' => $request->thumbnail,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'status' => $request->status ?? 'draft',
                'view_count' => 0,
                'like_count' => 0,
                'published_at' => $request->status === 'published' ? now() : null,
            ]);

            if ($request->has('tags') && !empty($request->tags)) {
                $post->tags()->attach($request->tags);
            }

            // xử lý lưu hình ảnh
            if

            return redirect()->route('admin.posts.index')->with('success', '✓ Tạo bài viết thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Lỗi: ' . $e->getMessage());
        }
    }

    public function show(Post $post)
    {
        return new PostResource($post->load(['category', 'tags', 'user', 'comments']));
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.edit', compact('post', 'categories', 'tags'));
    }


    public function update(StorePostRequest $request, $id)
    {
        try {
            $post = Post::findOrFail($id);

            $post->update([
                'title' => $request->title,
                'content' => $request->content,
                'excerpt' => $request->excerpt,
                'category_id' => $request->category_id,
                'slug' => Str::slug($request->title),
                'thumbnail' => $request->thumbnail,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'status' => $request->status ?? 'draft',
                'published_at' => $request->status === 'published' ? ($post->published_at ?? now()) : null,
            ]);

            if ($request->has('tags') && !empty($request->tags)) {
                $post->tags()->sync($request->tags);
            } else {
                $post->tags()->detach();
            }

            return redirect()->route('admin.posts.index')->with('success', '✓ Cập nhật bài viết thành công!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Lỗi: ' . $e->getMessage());
        }
    }

    public function destroy(Post $post)
    {
        try {
            $post->delete();
            return response()->json([
                'message' => '✓ Xóa bài viết thành công!',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => '❌ Lỗi: ' . $e->getMessage(),
                'status' => false
            ], 500);
        }
    }
}
