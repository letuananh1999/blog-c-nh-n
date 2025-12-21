<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
  /**
   * Get all categories with count
   */
  public function getAll($paginate = true)
  {
    try {
      $query = Category::withCount('posts')
        ->orderBy('sort', 'asc');

      return $paginate ? $query->paginate(10) : $query->get();
    } catch (\Exception $e) {
      throw new \Exception('Failed to fetch categories: ' . $e->getMessage());
    }
  }

  /**
   * Search categories by name or description
   */
  public function search(string $query, $paginate = true)
  {
    try {
      if (strlen($query) < 1) {
        throw new \Exception('Từ khóa tìm kiếm không được để trống!');
      }
      $queryBuilder = Category::where(function ($q) use ($query) {
        $q->where('name', 'like', "%{$query}%")
          ->orWhere('description', 'like', "%{$query}%");
      })
        ->withCount('posts')
        ->orderBy('sort', 'asc');

      return $paginate ? $queryBuilder->paginate(10) : $queryBuilder->get();
    } catch (\Exception $e) {
      throw new \Exception('Search failed: ' . $e->getMessage());
    }
  }

  /**
   * Create a new category
   */
  public function create(array $data): Category
  {
    try {
      return Category::create([
        'name' => $data['name'],
        'description' => $data['description'] ?? null,
      ]);
    } catch (\Exception $e) {
      throw new \Exception('Failed to create category: ' . $e->getMessage());
    }
  }

  /**
   * Update a category
   */
  public function update(Category $category, array $data): Category
  {
    try {
      // Kiểm tra version (Optimistic Locking)
      if (isset($data['version']) && $category->version != $data['version']) {
        throw new \Exception('Danh mục này đã được sửa bởi ai đó. Vui lòng tải lại trang!');
      }

      $updateData = [
        'name' => $data['name'],
        'description' => $data['description'] ?? null,
        'version' => $category->version + 1,
      ];

      $category->update($updateData);
      return $category;
    } catch (\Exception $e) {
      throw new \Exception('Failed to update category: ' . $e->getMessage());
    }
  }

  /**
   * Delete a category
   */
  public function delete(Category $category): bool
  {
    try {
      return $category->delete();
    } catch (\Exception $e) {
      throw new \Exception('Failed to delete category: ' . $e->getMessage());
    }
  }
}
