<?php

namespace App\Services;

use App\Models\Category;
use Exception;

class CategoryService
{
    private const PAGINATION_PER_PAGE = 10;
    private const MIN_SEARCH_LENGTH = 1;

    /**
     * Get all categories with post count
     */
    public function getAll($paginate = true)
    {
        $query = Category::withCount('posts')->orderBy('sort', 'asc');

        return $paginate ? $query->paginate(self::PAGINATION_PER_PAGE) : $query->get();
    }

    /**
     * Search categories by name or description
     */
    public function search(string $query, $paginate = true)
    {
        if (strlen($query) < self::MIN_SEARCH_LENGTH) {
            throw new Exception('Từ khóa tìm kiếm không được để trống!');
        }

        $queryBuilder = Category::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })
            ->withCount('posts')
            ->orderBy('sort', 'asc');

        return $paginate ? $queryBuilder->paginate(self::PAGINATION_PER_PAGE) : $queryBuilder->get();
    }

    /**
     * Create a new category
     */
    public function create(array $data): Category
    {
        return Category::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'version' => 1,
        ]);
    }

    /**
     * Update a category with optimistic locking
     */
    public function update(Category $category, array $data): Category
    {
        if (isset($data['version']) && $category->version != $data['version']) {
            throw new Exception('Danh mục này đã được sửa bởi ai đó. Vui lòng tải lại trang!');
        }

        $category->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'version' => $category->version + 1,
        ]);

        return $category;
    }

    /**
     * Delete a category
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
