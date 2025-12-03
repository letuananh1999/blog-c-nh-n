<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Tag;
use App\Services\PostService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of posts with eager loading
     */
    public function index()
    {
        $posts = Post::with(['category', 'tags', 'user'])
            ->withCount('comments')
            ->orderBy('created_at', 'desc')
            ->paginate(config('blog.post.per_page'));

        return view('admin.post.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created post
     */
    public function store(StorePostRequest $request)
    {
        try {
            $post = $this->postService->create($request->validated());
            return redirect()->route('admin.posts.index')
                ->with('success', '✓ Tạo bài viết thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified post
     */
    public function show(Post $post)
    {
        return new PostResource($post->load(['category', 'tags', 'user', 'comments']));
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.post.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified post
     */
    public function update(StorePostRequest $request, Post $post)
    {
        try {
            $data = $request->validated();
            $data['post'] = $post; // Pass post for comparison

            $post = $this->postService->update($post, $data);

            return redirect()->route('admin.posts.index')
                ->with('success', '✓ Cập nhật bài viết thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified post
     */
    public function destroy(Post $post)
    {
        try {
            $this->postService->delete($post);

            return response()->json([
                'message' => '✓ Xóa bài viết thành công!',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => '❌ ' . $e->getMessage(),
                'status' => false
            ], 500);
        }
    }
}
