<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Hiển thị danh sách bình luận
     */
    public function index(Request $request)
    {
        try {
            $comments = $this->commentService->getComments(
                $request->status,
                $request->search
            );

            $stats = $this->commentService->getStats();

            return view('admin.comment.index', compact('comments', 'stats'));
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Duyệt bình luận (Approve)
     */
    public function approve($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Kiểm tra quyền
            if (!Auth::check() || Auth::user()->role !== 'Admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $this->commentService->approve($comment);

            return back()->with('success', 'Duyệt bình luận thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi duyệt bình luận: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối duyệt (Unapprove)
     */
    public function unapprove($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            if (!Auth::check() || Auth::user()->role !== 'Admin') {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $this->commentService->unapprove($comment);

            return back()->with('success', 'Bỏ duyệt bình luận thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    /**
     * Trả lời bình luận
     */
    public function reply(Request $request, $id)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            // Lấy comment gốc
            $parentComment = Comment::findOrFail($id);

            // Kiểm tra quyền
            if (!Auth::check()) {
                return back()->with('error', 'Bạn phải đăng nhập để trả lời');
            }

            // Tạo reply thông qua service
            $this->commentService->reply($parentComment, $validated);

            return back()->with('success', 'Trả lời bình luận thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi trả lời: ' . $e->getMessage());
        }
    }

    public function create()
    {
        //
    }

    public function store(StoreCommentRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->commentService->create($validated);

            return back()->with('success', 'Bình luận thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi tạo bình luận: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $comment = $this->commentService->getWithRelations($id);
            return view('admin.comment.show', compact('comment'));
        } catch (\Exception $e) {
            return back()->with('error', 'Bình luận không tìm thấy');
        }
    }

    public function edit($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            return view('admin.comment.edit', compact('comment'));
        } catch (\Exception $e) {
            return back()->with('error', 'Bình luận không tìm thấy');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            $validated = $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            $this->commentService->update($comment, $validated);

            return back()->with('success', 'Cập nhật bình luận thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            if (!Auth::check() || Auth::user()->role !== 'Admin') {
                return back()->with('error', 'Không có quyền xóa');
            }

            $this->commentService->delete($comment);

            return redirect()->route('admin.comments.index')->with('success', 'Xóa bình luận thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi xóa: ' . $e->getMessage());
        }
    }
}
