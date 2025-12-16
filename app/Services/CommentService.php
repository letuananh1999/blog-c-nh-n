<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentService
{
  /**
   * Lấy danh sách bình luận với filter
   */
  public function getComments($status = null, $search = null, $perPage = 15)
  {
    $query = Comment::with(['post', 'user'])
      ->orderBy('created_at', 'desc');

    // Filter theo trạng thái
    if ($status && $status !== 'all') {
      if ($status === 'approved') {
        $query->where('is_approved', true);
      } elseif ($status === 'pending') {
        $query->where('is_approved', false);
      }
    }

    // Tìm kiếm
    if ($search) {
      $search = '%' . $search . '%';
      $query->where(function ($q) use ($search) {
        $q->where('content', 'like', $search)
          ->orWhere('author_name', 'like', $search);
      });
    }

    return $query->paginate($perPage);
  }

  /**
   * Lấy thống kê bình luận
   */
  public function getStats(): array
  {
    return [
      'total' => Comment::count(),
      'approved' => Comment::where('is_approved', true)->count(),
      'pending' => Comment::where('is_approved', false)->count(),
    ];
  }

  /**
   * Tạo bình luận mới
   */
  public function create(array $data): Comment
  {
    try {
      $user = Auth::user();

      $comment = Comment::create([
        'post_id' => $data['post_id'],
        'user_id' => $user?->id,
        'author_name' => $user?->name ?? ($data['author_name'] ?? 'Khách'),
        'author_email' => $user?->email ?? ($data['author_email'] ?? null),
        'content' => $data['content'],
        'parent_id' => $data['parent_id'] ?? null,
        'is_approved' => false, // Mặc định chờ duyệt
      ]);

      Log::info('Comment created', [
        'comment_id' => $comment->id,
        'post_id' => $data['post_id'],
        'created_by' => $user?->id,
      ]);

      return $comment;
    } catch (\Exception $e) {
      Log::error('Error creating comment', ['error' => $e->getMessage()]);
      throw $e;
    }
  }

  /**
   * Cập nhật bình luận
   */
  public function update(Comment $comment, array $data): Comment
  {
    try {
      // Kiểm tra version (Optimistic Locking)
      if (isset($data['version']) && $comment->version != $data['version']) {
        throw new \Exception('Bình luận này đã được sửa bởi ai đó. Vui lòng tải lại trang!');
      }

      $updateData = [];

      if (isset($data['content'])) {
        $updateData['content'] = $data['content'];
      }

      if (isset($data['author_name'])) {
        $updateData['author_name'] = $data['author_name'];
      }

      if (isset($data['author_email'])) {
        $updateData['author_email'] = $data['author_email'];
      }

      $updateData['version'] = $comment->version + 1;  // Tăng version

      $comment->update($updateData);

      Log::info('Comment updated', [
        'comment_id' => $comment->id,
        'updated_by' => Auth::id(),
      ]);

      return $comment;
    } catch (\Exception $e) {
      Log::error('Error updating comment', ['error' => $e->getMessage()]);
      throw $e;
    }
  }

  /**
   * Duyệt bình luận
   */
  public function approve(Comment $comment): Comment
  {
    try {
      $comment->update(['is_approved' => true]);

      Log::info('Comment approved', [
        'comment_id' => $comment->id,
        'approved_by' => Auth::id(),
        'timestamp' => now(),
      ]);

      return $comment;
    } catch (\Exception $e) {
      Log::error('Error approving comment', ['error' => $e->getMessage()]);
      throw $e;
    }
  }

  /**
   * Bỏ duyệt bình luận
   */
  public function unapprove(Comment $comment): Comment
  {
    try {
      $comment->update(['is_approved' => false]);

      Log::info('Comment unapproved', [
        'comment_id' => $comment->id,
        'unapproved_by' => Auth::id(),
      ]);

      return $comment;
    } catch (\Exception $e) {
      Log::error('Error unapproving comment', ['error' => $e->getMessage()]);
      throw $e;
    }
  }

  /**
   * Trả lời bình luận
   */
  public function reply(Comment $parentComment, array $data): Comment
  {
    try {
      $user = Auth::user();

      if (!$user) {
        throw new \Exception('Bạn phải đăng nhập để trả lời');
      }

      $reply = Comment::create([
        'post_id' => $parentComment->post_id,
        'user_id' => $user->id,
        'author_name' => $user->name,
        'author_email' => $user->email,
        'content' => $data['content'],
        'parent_id' => $parentComment->id,
        'is_approved' => true, // ✅ Admin reply tự động duyệt
      ]);

      Log::info('Comment reply created', [
        'reply_id' => $reply->id,
        'parent_id' => $parentComment->id,
        'replied_by' => $user->id,
      ]);

      return $reply;
    } catch (\Exception $e) {
      Log::error('Error replying to comment', ['error' => $e->getMessage()]);
      throw $e;
    }
  }

  /**
   * Xóa bình luận
   */
  public function delete(Comment $comment): bool
  {
    try {
      $commentId = $comment->id;
      $comment->delete();

      Log::info('Comment deleted', [
        'comment_id' => $commentId,
        'deleted_by' => Auth::id(),
      ]);

      return true;
    } catch (\Exception $e) {
      Log::error('Error deleting comment', ['error' => $e->getMessage()]);
      throw $e;
    }
  }

  /**
   * Lấy bình luận kèm relationships
   */
  public function getWithRelations(int $id): Comment
  {
    return Comment::with(['post', 'user', 'parent', 'children'])
      ->findOrFail($id);
  }
}
