<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        // Hiển thị danh sách bình luận
        return view('admin.comment.index');
    }

    public function create()
    {
        // Hiển thị form tạo danh mục mới
    }

    public function store(Request $request)
    {
        // Xử lý lưu danh mục mới
    }

    public function edit($id)
    {
        // Hiển thị form chỉnh sửa danh mục
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
