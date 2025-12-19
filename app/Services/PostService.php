<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PostService
{
  private ImageService $imageService;

  public function __construct(ImageService $imageService)
  {
    $this->imageService = $imageService;
  }

  /**
   * Create a new post
   */
  public function create(array $data): Post
  {
    try {
      $postData = $this->prepareThumbnail($data);
      $postData = $this->preparePostData($data, null, $postData);

      $post = Post::create($postData);
      $this->attachTags($post, $data['tags'] ?? []);

      return $post;
    } catch (\Exception $e) {
      throw new \Exception('Failed to create post: ' . $e->getMessage());
    }
  }

  /**
   * Update an existing post
   */
  public function update(Post $post, array $data): Post
  {
    try {
      // Kiểm tra version (Optimistic Locking)
      if (isset($data['version']) && $post->version != $data['version']) {
        throw new \Exception('Bài viết này đã được sửa bởi ai đó. Vui lòng tải lại trang!');
      }

      $postData = $this->prepareThumbnail($data, $post);
      $postData = $this->preparePostData($data, $post, $postData);
      $postData['version'] = $post->version + 1;  // Tăng version

      $post->update($postData);
      $this->syncTags($post, $data['tags'] ?? []);

      return $post;
    } catch (\Exception $e) {
      throw new \Exception('Failed to update post: ' . $e->getMessage());
    }
  }

  /**
   * Get all posts with pagination
   */
  public function getAll($paginate = true)
  {
    try {
      $query = Post::with(['category', 'tags', 'user'])
        ->withCount('comments')
        ->orderBy('created_at', 'desc');

      return $paginate ? $query->paginate(config('blog.post.per_page')) : $query->get();
    } catch (\Exception $e) {
      throw new \Exception('Failed to fetch posts: ' . $e->getMessage());
    }
  }

  /**
   * Get single post by ID
   */
  public function getById(int $id): Post
  {
    try {
      return Post::with(['category', 'tags', 'user', 'comments'])
        ->findOrFail($id);
    } catch (\Exception $e) {
      throw new \Exception('Post not found: ' . $e->getMessage());
    }
  }

  /**
   * Search posts by title or content
   */
  public function search(string $query)
  {
    try {
      if (strlen($query) < 2) {
        throw new \Exception('Query phải ít nhất 2 ký tự!');
      }

      return Post::where('status', 'published')
        ->where(function ($q) use ($query) {
          $q->where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%");
        })
        ->with(['category', 'tags', 'user'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    } catch (\Exception $e) {
      throw new \Exception('Search failed: ' . $e->getMessage());
    }
  }

  /**
   * Delete a post with cleanup
   */
  public function delete(Post $post): bool
  {
    try {
      $this->deleteThumbnail($post);
      $post->tags()->detach();
      return $post->delete();
    } catch (\Exception $e) {
      throw new \Exception('Failed to delete post: ' . $e->getMessage());
    }
  }

  /**
   * Handle thumbnail processing (save/update/delete)
   */
  private function prepareThumbnail(array $data, ?Post $existingPost = null): array
  {
    $thumbnailData = [];

    if (!empty($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
      // Delete old thumbnail if updating
      if ($existingPost && $existingPost->thumbnail) {
        $this->imageService->delete($existingPost->thumbnail);
      }

      // Save new thumbnail
      $thumbnailData['thumbnail'] = $this->imageService->save($data['thumbnail']);
    }

    return $thumbnailData;
  }

  /**
   * Prepare post data for create/update
   */
  private function preparePostData(array $data, ?Post $existingPost = null, array $thumbnailData = []): array
  {
    $postData = [
      'title'            => $data['title'],
      'slug'             => Str::slug($data['title']),
      'excerpt'          => $data['excerpt'],
      'content'          => $data['content'],
      'category_id'      => $data['category_id'],
      'meta_title'       => $data['meta_title'] ?? null,
      'meta_description' => $data['meta_description'] ?? null,
      'status'           => $data['status'] ?? config('blog.post.default_status'),
    ];

    // Add thumbnail if provided
    if (!empty($thumbnailData)) {
      $postData = array_merge($postData, $thumbnailData);
    }

    // Handle published_at based on status
    if ($data['status'] === 'published') {
      $postData['published_at'] = $existingPost?->published_at ?? now();
    } else {
      $postData['published_at'] = null;
    }

    // Only set user_id on creation
    if (!$existingPost) {
      $postData['user_id'] = Auth::id();
      $postData['views_count'] = 0;
      $postData['likes_count'] = 0;
    }

    return $postData;
  }

  /**
   * Delete thumbnail file from disk
   */
  private function deleteThumbnail(Post $post): void
  {
    if ($post->thumbnail) {
      $this->imageService->delete($post->thumbnail);
    }
  }

  /**
   * Attach tags to a new post
   */
  private function attachTags(Post $post, array $tagIds): void
  {
    if (!empty($tagIds)) {
      $post->tags()->attach($tagIds);
    }
  }

  /**
   * Sync tags (replace existing tags)
   */
  private function syncTags(Post $post, array $tagIds): void
  {
    $post->tags()->sync($tagIds);
  }
}
