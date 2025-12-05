<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Services\PostService;
use App\Services\ApiResponseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    private PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of posts
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
        return view('admin.post.create', [
            'categories' => Category::all(),
            'tags' => Tag::all(),
        ]);
    }

    /**
     * Store a newly created post
     */
    public function store(StorePostRequest $request)
    {
        try {
            $this->postService->create($request->validated());

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
        $post->load(['category', 'tags', 'user', 'comments']);

        return view('admin.post.detail', compact('post'));
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit(Post $post)
    {
        return view('admin.post.edit', [
            'post' => $post,
            'categories' => Category::all(),
            'tags' => Tag::all(),
        ]);
    }

    /**
     * Update the specified post
     */
    public function update(StorePostRequest $request, Post $post)
    {
        try {
            $this->postService->update($post, $request->validated());

            return redirect()->route('admin.posts.index')
                ->with('success', '✓ Cập nhật bài viết thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete the specified post (API Endpoint)
     */
    public function destroy(Post $post)
    {
        try {
            if (!$this->authorizeDelete($post)) {
                return ApiResponseService::unauthorized('❌ Bạn không có quyền xóa bài viết này!');
            }

            $this->logDeletion($post);
            $this->postService->delete($post);

            return ApiResponseService::success('✓ Xóa bài viết thành công!');
        } catch (\Exception $e) {
            return $this->handleDeletionError($post, $e);
        }
    }

    /**
     * Check if user is authorized to delete post
     */
    private function authorizeDelete(Post $post): bool
    {
        return $post->user_id === Auth::id();
    }

    /**
     * Log post deletion for audit trail
     */
    private function logDeletion(Post $post): void
    {
        Log::info('Post deleted', [
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'post_title' => $post->title,
            'timestamp' => now()
        ]);
    }

    /**
     * Handle deletion error
     */
    private function handleDeletionError(Post $post, \Exception $e)
    {
        Log::error('Post deletion failed', [
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'error' => $e->getMessage()
        ]);

        return ApiResponseService::serverError('❌ Có lỗi xảy ra khi xóa bài viết!');
    }
}
