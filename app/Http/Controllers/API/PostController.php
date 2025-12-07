<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
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
   * GET /api/posts - List all posts
   */
  public function index()
  {
    try {
      $posts = Post::with(['category', 'tags', 'user'])
        ->withCount('comments')
        ->orderBy('created_at', 'desc')
        ->paginate(config('blog.post.per_page'));

      return ApiResponseService::success(
        'Posts retrieved successfully',
        $posts
      );
    } catch (\Exception $e) {
      Log::error('Failed to fetch posts', ['error' => $e->getMessage()]);
      return ApiResponseService::serverError();
    }
  }

  /**
   * GET /api/posts/{id} - Get single post
   */
  public function show(Post $post)
  {
    try {
      $post->load(['category', 'tags', 'user', 'comments']);

      return ApiResponseService::success(
        'Post retrieved successfully',
        $post
      );
    } catch (\Exception $e) {
      Log::error('Failed to fetch post', ['post_id' => $post->id, 'error' => $e->getMessage()]);
      return ApiResponseService::serverError();
    }
  }

  /**
   * POST /api/posts - Create new post (Admin only)
   */
  public function store(StorePostRequest $request)
  {
    try {
      $post = $this->postService->create($request->validated());

      return ApiResponseService::success(
        '✓ Bài viết đã được tạo thành công!',
        $post,
        201
      );
    } catch (\Exception $e) {
      Log::error('Failed to create post', ['error' => $e->getMessage()]);
      return ApiResponseService::serverError('❌ Không thể tạo bài viết!');
    }
  }

  /**
   * PUT /api/posts/{id} - Update post (Admin only)
   */
  public function update(StorePostRequest $request, Post $post)
  {
    try {
      if (!$this->authorizeUpdate($post)) {
        return ApiResponseService::unauthorized('❌ Bạn không có quyền cập nhật bài viết này!');
      }

      $updatedPost = $this->postService->update($post, $request->validated());

      return ApiResponseService::success(
        '✓ Bài viết đã được cập nhật thành công!',
        $updatedPost
      );
    } catch (\Exception $e) {
      Log::error('Failed to update post', ['post_id' => $post->id, 'error' => $e->getMessage()]);
      return ApiResponseService::serverError('❌ Không thể cập nhật bài viết!');
    }
  }

  /**
   * DELETE /api/posts/{id} - Delete post (Admin only)
   */
  public function destroy(Post $post)
  {
    try {
      if (!$this->authorizeDelete($post)) {
        return ApiResponseService::unauthorized('❌ Bạn không có quyền xóa bài viết này!');
      }

      $this->logDeletion($post);
      $this->postService->delete($post);

      return ApiResponseService::success('✓ Bài viết đã được xóa thành công!');
    } catch (\Exception $e) {
      return $this->handleDeletionError($post, $e);
    }
  }

  /**
   * GET /api/posts/search?q=keyword - Search posts
   */
  public function search()
  {
    try {
      $query = request()->get('q', '');

      if (strlen($query) < 2) {
        return ApiResponseService::error('Query phải ít nhất 2 ký tự!', null, 422);
      }

      $posts = Post::where('status', 'published')
        ->where(function ($q) use ($query) {
          $q->where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%");
        })
        ->with(['category', 'tags', 'user'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

      return ApiResponseService::success(
        'Search results',
        $posts
      );
    } catch (\Exception $e) {
      Log::error('Search failed', ['error' => $e->getMessage()]);
      return ApiResponseService::serverError();
    }
  }

  /**
   * Check if user is authorized to update post
   */
  private function authorizeUpdate(Post $post): bool
  {
    return $post->user_id === Auth::id();
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
    Log::info('Post deleted via API', [
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
