<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserService
{
  protected const AVATAR_PATH = 'public/img/user';

  /**
   * Tạo user mới
   */
  public function create(array $data): User
  {
    try {
      $userData = [
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => $data['password'],
        'role' => $data['role'] ?? 'User',
        'status' => $data['status'] ?? 'active',
      ];

      // Xử lý email verification
      if (!empty($data['email_verified'])) {
        $userData['email_verified_at'] = now();
      }

      // Xử lý avatar nếu có
      if (!empty($data['avatar'])) {
        $userData['avatar'] = $this->saveAvatar($data['avatar']);
      }

      $user = User::create($userData);

      Log::info('User created', [
        'user_id' => $user->id,
        'email' => $user->email,
        'created_by' => auth()->id(),
      ]);

      return $user;
    } catch (\Exception $e) {
      Log::error('Failed to create user', ['error' => $e->getMessage()]);
      throw $e;
    }
  }

  /**
   * Cập nhật user
   */
  public function update(User $user, array $data): User
  {
    try {
      $updateData = [
        'name' => $data['name'] ?? $user->name,
        'email' => $data['email'] ?? $user->email,
        'role' => $data['role'] ?? $user->role,
      ];

      // Xử lý mật khẩu nếu có
      if (!empty($data['password'])) {
        $updateData['password'] = $data['password'];
      }

      // Xử lý email verification
      if (isset($data['email_verified'])) {
        $updateData['email_verified_at'] = $data['email_verified'] ? now() : null;
      }

      // Xử lý avatar mới
      if (!empty($data['avatar'])) {
        // Xóa avatar cũ nếu có
        if ($user->avatar) {
          $this->deleteAvatar($user->avatar);
        }
        $updateData['avatar'] = $this->saveAvatar($data['avatar']);
      }

      $user->update($updateData);

      Log::info('User updated', [
        'user_id' => $user->id,
        'updated_by' => auth()->id(),
      ]);

      return $user;
    } catch (\Exception $e) {
      Log::error('Failed to update user', ['error' => $e->getMessage()]);
      throw $e;
    }
  }

  /**
   * Khóa/Mở khóa user
   */
  public function toggleStatus(User $user): User
  {
    try {
      $newStatus = $user->status === 'active' ? 'blocked' : 'active';
      $user->update(['status' => $newStatus]);

      Log::info('User status changed', [
        'user_id' => $user->id,
        'new_status' => $newStatus,
        'changed_by' => auth()->id(),
      ]);

      return $user;
    } catch (\Exception $e) {
      Log::error('Failed to toggle user status', ['error' => $e->getMessage()]);
      throw $e;
    }
  }

  /**
   * Xóa user
   */
  public function delete(User $user): bool
  {
    try {
      $userId = $user->id;

      // Xóa avatar nếu có
      if ($user->avatar) {
        $this->deleteAvatar($user->avatar);
      }

      $user->delete();

      Log::info('User deleted', [
        'user_id' => $userId,
        'deleted_by' => auth()->id(),
      ]);

      return true;
    } catch (\Exception $e) {
      Log::error('Failed to delete user', ['error' => $e->getMessage()]);
      throw $e;
    }
  }

  /**
   * Lấy danh sách user với filter
   */
  public function getUsers(string $status = 'all', int $perPage = 10)
  {
    $query = User::orderBy('created_at', 'desc');

    if ($status !== 'all') {
      $query->where('status', $status);
    }

    return $query->paginate($perPage);
  }

  /**
   * Lưu avatar file
   * @param \Illuminate\Http\UploadedFile $file
   * @return string tên file đã lưu
   */
  private function saveAvatar($file): string
  {
    try {
      $filename = 'user_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

      // Lưu file vào public/img/user
      $file->move(public_path('img/user'), $filename);

      return $filename;
    } catch (\Exception $e) {
      Log::error('Failed to save avatar', ['error' => $e->getMessage()]);
      throw new \Exception('Lỗi lưu ảnh đại diện: ' . $e->getMessage());
    }
  }

  /**
   * Xóa avatar file
   */
  private function deleteAvatar(string $filename): void
  {
    try {
      $filepath = public_path('img/user/' . $filename);
      if (file_exists($filepath)) {
        unlink($filepath);
        Log::info('Avatar deleted', ['filename' => $filename]);
      }
    } catch (\Exception $e) {
      Log::warning('Failed to delete avatar', ['filename' => $filename, 'error' => $e->getMessage()]);
    }
  }

  /**
   * Lấy URL avatar
   */
  public function getAvatarUrl(?string $avatar): string
  {
    if ($avatar && file_exists(public_path('img/user/' . $avatar))) {
      return asset('img/user/' . $avatar);
    }
    return asset('img/default-avatar.png');
  }
}
