<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        // Hiển thị danh sách danh mục
        $categories = Category::withCount('posts')
            ->orderBy('sort', 'asc')
            ->paginate(10);
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
            Category::create($validated);
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
        ]);

        try {
            $category = Category::findOrFail($id);
            $category->update($validated);
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
            $category->delete();
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
}
