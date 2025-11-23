<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with(['post', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.comment.index', compact('comments'));
    }

    public function create()
    {
        // Hiển thị form tạo bình luận mới
    }

    public function store(StoreCommentRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

        try {
            Comment::create([
                'post_id' => $validated['post_id'],
                'user_id' => $user?->id,
                'author_name' => $user?->name ?? 'Khách',
                'author_email' => $user?->email,
                'content' => $validated['content'],
                'parent_id' => $validated['parent_id'] ?? null,
                'status' => 0,
            ]);

            return back()->with('success', 'Bình luận thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi tạo bình luận: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $comment = Comment::findOrFail($id);
        return view('admin.comment.edit', compact('comment'));
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update($validated);

        return back()->with('success', 'Cập nhật bình luận thành công!');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return back()->with('success', 'Xóa bình luận thành công!');
    }
}
