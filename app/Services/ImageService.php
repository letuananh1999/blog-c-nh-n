<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageService
{
  /**
   * Save and optimize an image
   * 
   * @param UploadedFile $file
   * @return string Path to saved image
   * @throws \Exception
   */
  public function save(UploadedFile $file): string
  {
    $this->validate($file);

    $filename = $this->generateFilename($file);
    $path = public_path(config('blog.post.thumbnail.path'));

    // Create directory if it doesn't exist
    if (!file_exists($path)) {
      mkdir($path, 0777, true);
    }

    // Move file to destination
    $file->move($path, $filename);

    return '/' . config('blog.post.thumbnail.path') . '/' . $filename;
  }

  /**
   * Delete an image file
   * 
   * @param string $filepath
   * @return bool
   */
  public function delete(string $filepath): bool
  {
    $fullPath = public_path($filepath);

    if (file_exists($fullPath) && is_file($fullPath)) {
      return unlink($fullPath);
    }

    return true;
  }

  /**
   * Validate uploaded file
   * 
   * @param UploadedFile $file
   * @throws \Exception
   */
  private function validate(UploadedFile $file): void
  {
    // Check size (in bytes)
    $maxSize = config('blog.post.thumbnail.max_size') * 1024;
    if ($file->getSize() > $maxSize) {
      throw new \Exception(
        'Kích thước hình ảnh quá lớn. Tối đa ' .
          config('blog.post.thumbnail.max_size') . 'MB'
      );
    }

    // Check MIME type
    $allowedFormats = config('blog.post.thumbnail.allowed_formats');
    $ext = strtolower($file->getClientOriginalExtension());

    if (!in_array($ext, $allowedFormats)) {
      throw new \Exception(
        'Định dạng hình ảnh không hợp lệ. Chỉ hỗ trợ: ' .
          implode(', ', $allowedFormats)
      );
    }
  }

  /**
   * Generate unique filename
   * 
   * @param UploadedFile $file
   * @return string
   */
  private function generateFilename(UploadedFile $file): string
  {
    $timestamp = time();
    $unique = Str::random(8);
    $ext = $file->getClientOriginalExtension();

    return "{$timestamp}_{$unique}.{$ext}";
  }
}
