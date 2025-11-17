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
            ->get();
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
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Lưu danh mục vào cơ sở dữ liệu
        Category::create($data);

        return redirect()->route('admin.categories.index');
    }

    public function edit($id)
    {
        // Hiển thị form chỉnh sửa danh mục
        return view('admin.categories.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Xử lý cập nhật danh mục
    }

    public function destroy($id)
    {
        // Xử lý xóa danh mục
    }
}
