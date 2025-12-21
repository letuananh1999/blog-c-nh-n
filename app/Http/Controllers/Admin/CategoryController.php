<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        // Hiển thị danh sách danh mục
        $categories = $this->categoryService->getAll();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        // Hiển thị form tạo danh mục mới
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        // Xử lý lưu danh mục mới
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $this->categoryService->create($validated);
            return response()->json([
                'message' => 'Category created successfully',
                'status' => true
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating category: ' . $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

    public function edit($id)
    {
        // Hiển thị form chỉnh sửa danh mục
        return view('admin.categories.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Xử lý cập nhật danh mục
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'version' => 'required|integer',
        ]);

        try {
            $category = Category::findOrFail($id);
            $this->categoryService->update($category, $validated);

            return response()->json([
                'message' => 'Category updated successfully',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating category: ' . $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

    public function destroy($id)
    {
        // Xử lý xóa danh mục
        try {
            $category = Category::findOrFail($id);
            $this->categoryService->delete($category);
            return response()->json([
                'message' => 'Category deleted successfully',
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting category: ' . $e->getMessage(),
                'status' => false
            ], 500);
        }
    }

    public function search(Request $request)
    {
        // Tìm kiếm danh mục
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

    public function show($id)
    {
        // Hiển thị chi tiết danh mục
        $category = Category::withCount('posts')->findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }
}
