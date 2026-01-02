<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private CategoryService $categoryService)
    {
    }

    /**
     * Display all categories
     */
    public function index()
    {
        $categories = $this->categoryService->getAll();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store new category
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $this->categoryService->create($request->validated());
            return $this->successResponse('Danh mục đã được thêm thành công', null, 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Lỗi khi thêm danh mục: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        return view('admin.categories.edit', compact('id'));
    }

    /**
     * Update category
     */
    public function update(UpdateCategoryRequest $request, $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $this->categoryService->update($category, $request->validated());
            return $this->successResponse('Category updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error updating category: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete category
     */
    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            $this->categoryService->delete($category);
            return $this->successResponse('Category deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting category: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Search categories
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');

            if (strlen($query) < 1) {
                return redirect()->route('admin.categories.index');
            }

            $categories = $this->categoryService->search($query);
            return view('admin.categories.index', compact('categories'));
        } catch (\Exception $e) {
            return redirect()->route('admin.categories.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display category details
     */
    public function show($id)
    {
        $category = Category::withCount('posts')->findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Get category version (for Optimistic Locking)
     */
    public function getVersion($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);
            return $this->successResponse('Version fetched', [
                'version' => $category->version,
                'name' => $category->name,
                'description' => $category->description
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Error fetching category: ' . $e->getMessage(), 404);
        }
    }
}
