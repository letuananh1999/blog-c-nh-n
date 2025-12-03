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
   * Create a new post with all relationships
   * 
   * @param array $data
   * @return Post
   * @throws \Exception
   */
  public function create(array $data): Post
  {
    try {
      // Save thumbnail first if provided
      $thumbnailPath = null;
      if (!empty($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
        $thumbnailPath = $this->imageService->save($data['thumbnail']);
      }

      // Add thumbnail path to data (null for $existingPost means creation)
      $postData = $this->preparePostData($data, null);
      if ($thumbnailPath) {
        $postData['thumbnail'] = $thumbnailPath;
      }

      // Create post with all data
      $post = Post::create($postData);

      // Attach tags if provided
      $this->attachTags($post, $data['tags'] ?? []);

      return $post;
    } catch (\Exception $e) {
      throw new \Exception('Failed to create post: ' . $e->getMessage());
    }
  }

  /**
   * Update an existing post
   * 
   * @param Post $post
   * @param array $data
   * @return Post
   * @throws \Exception
   */
  public function update(Post $post, array $data): Post
  {
    try {
      // Handle thumbnail update
      if (!empty($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
        // Delete old thumbnail
        if ($post->thumbnail) {
          $this->imageService->delete($post->thumbnail);
        }
        // Save new thumbnail
        $thumbnailPath = $this->imageService->save($data['thumbnail']);

        $postData = $this->preparePostData($data, $post);
        $postData['thumbnail'] = $thumbnailPath;
      } else {
        $postData = $this->preparePostData($data, $post);
      }

      $post->update($postData);

      // Sync tags
      $this->syncTags($post, $data['tags'] ?? []);

      return $post;
    } catch (\Exception $e) {
      throw new \Exception('Failed to update post: ' . $e->getMessage());
    }
  }

  /**
   * Delete a post with cleanup
   * 
   * @param Post $post
   * @return bool
   */
  public function delete(Post $post): bool
  {
    try {
      // Delete thumbnail
      if ($post->thumbnail) {
        $this->imageService->delete($post->thumbnail);
      }

      // Delete tags relationship
      $post->tags()->detach();

      // Delete post
      return $post->delete();
    } catch (\Exception $e) {
      throw new \Exception('Failed to delete post: ' . $e->getMessage());
    }
  }

  /**
   * Prepare post data for creation/update
   * 
   * @param array $data
   * @param Post|null $existingPost
   * @return array
   */
  private function preparePostData(array $data, ?Post $existingPost = null): array
  {
    $postData = [
      'title'              => $data['title'],
      'slug'               => Str::slug($data['title']),
      'excerpt'            => $data['excerpt'],
      'content'            => $data['content'],
      'category_id'        => $data['category_id'],
      'meta_title'         => $data['meta_title'] ?? null,
      'meta_description'   => $data['meta_description'] ?? null,
      'status'             => $data['status'] ?? config('blog.post.default_status'),
    ];

    // Handle published_at based on status
    if ($data['status'] === 'published') {
      if ($existingPost && $existingPost->published_at) {
        // On update: preserve existing published_at
        $postData['published_at'] = $existingPost->published_at;
      } else {
        // On creation: set published_at to now
        $postData['published_at'] = now();
      }
    } else {
      // Draft or archived: set to null
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
   * Attach tags to a new post
   * 
   * @param Post $post
   * @param array $tagIds
   */
  private function attachTags(Post $post, array $tagIds): void
  {
    if (!empty($tagIds)) {
      $post->tags()->attach($tagIds);
    }
  }

  /**
   * Sync tags (replace existing tags)
   * 
   * @param Post $post
   * @param array $tagIds
   */
  private function syncTags(Post $post, array $tagIds): void
  {
    if (!empty($tagIds)) {
      $post->tags()->sync($tagIds);
    } else {
      $post->tags()->detach();
    }
  }
}
