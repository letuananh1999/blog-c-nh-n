@extends('layouts.dashboard')
@section('title', 'Chi tiết bình luận')
@push('styles')
		<link rel="stylesheet" href="{{ asset('css/comment/show.css') }}">	
@endpush
@section('content')
    <div class="containerr">
        <section class="comment-detail-wrap">
            <div class="head-row">
                <div class="form-blog-header">
                    <h1>Chi tiết bình luận</h1>
                    <p class="subtitle">Xem và quản lý bình luận</p>
                </div>
            </div>

            @include('components.flash_message')

            <div class="comment-detail-card">
                <!-- Header: Thông tin bình luận -->
                <div class="detail-header">
                    <div class="author-info">
                        <div class="avatar-circle">{{ strtoupper(substr($comment->author_name, 0, 1)) }}</div>
                        <div class="author-details">
                            <h2>{{ $comment->author_name }}</h2>
                            <p>{{ $comment->author_email }}</p>
                            <small>{{ $comment->created_at->format('Y-m-d H:i:s') }}</small>
                        </div>
                    </div>
                    <div class="status-info">
                        <span class="status-badge {{ $comment->is_approved ? 'approved' : 'pending' }}">
                            {{ $comment->is_approved ? '✓ Đã duyệt' : '⏳ Chờ duyệt' }}
                        </span>
                        @if($comment->user)
                            <span class="user-badge">
                                Đăng nhập: {{ $comment->user->name }}
                            </span>
                        @else
                            <span class="guest-badge">Khách</span>
                        @endif
                    </div>
                </div>

                <!-- Bài viết liên quan -->
                <div class="post-reference">
                    <strong>Bài viết:</strong>
                    <a href="{{ route('admin.posts.show', $comment->post_id) }}" target="_blank">
                        {{ $comment->post->title }}
                    </a>
                </div>

                <!-- Comment cha (nếu là reply) -->
                @if($comment->parent)
                    <div class="parent-comment">
                        <strong>Trả lời comment từ:</strong>
                        <div class="parent-card">
                            <p><strong>{{ $comment->parent->author_name }}</strong></p>
                            <p>{{ Str::limit($comment->parent->content, 200) }}</p>
                        </div>
                    </div>
                @endif

                <!-- Nội dung bình luận -->
                <div class="comment-content">
                    <h3>Nội dung:</h3>
                    <div class="content-box">
                        {{ $comment->content }}
                    </div>
                </div>

                <!-- Các bình luận trả lời -->
                @if($comment->children->count() > 0)
                    <div class="replies-section">
                        <h3>{{ $comment->children->count() }} trả lời</h3>
                        @foreach($comment->children as $reply)
                            <div class="reply-detail">
                                <div class="reply-avatar">{{ strtoupper(substr($reply->author_name, 0, 1)) }}</div>
                                <div class="reply-content">
                                    <strong>{{ $reply->author_name }}</strong>
                                    <small>{{ $reply->created_at->format('Y-m-d H:i') }}</small>
                                    <p>{{ $reply->content }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Actions -->
                <div class="detail-actions">
                    @if(!$comment->is_approved)
                        <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST" style="display: inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn primary">✓ Duyệt bình luận</button>
                        </form>
                    @else
                        <form action="{{ route('admin.comments.unapprove', $comment->id) }}" method="POST" style="display: inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn warning">⚠ Bỏ duyệt</button>
                        </form>
                    @endif

                    @if(!$comment->parent_id)
                        <button class="btn" onclick="document.getElementById('reply-section').style.display = 'block'">↩ Trả lời</button>
                    @endif

                    {{-- <a href="{{ route('admin.comments.edit', $comment->id) }}" class="btn">✏ Sửa</a> --}}

                    <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn danger">🗑 Xóa</button>
                    </form>

                    <a href="{{ route('admin.comments.index') }}" class="btn">← Quay lại</a>
                </div>

                <!-- Form trả lời -->
                @if(!$comment->parent_id)
                    <div id="reply-section" style="display: none; margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee;">
                        <h3>Trả lời bình luận</h3>
                        <form method="POST" action="{{ route('admin.comments.reply', $comment->id) }}">
                            @csrf
                            <div class="form-group">
                                <label for="reply-content">Nội dung trả lời:</label>
                                <textarea id="reply-content" name="content" required maxlength="1000" rows="5" placeholder="Nhập nội dung trả lời..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                                <small>Tối đa 1000 ký tự</small>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button type="submit" class="btn primary">Gửi trả lời</button>
                                <button type="button" class="btn" onclick="document.getElementById('reply-section').style.display = 'none'">Hủy</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <footer class="foot">© 2025 AdminHub · Giao diện mẫu</footer>
        </section>
    </div>
@endsection
