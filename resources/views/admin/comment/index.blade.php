@extends('layouts.dashboard')
@section('title', 'Comments')
@push('styles')
		<link rel="stylesheet" href="{{ asset('css/comment/index.css') }}">	
@endpush
@section('content')
						<div class="containerr">
							<section class="comments-wrap">
								<div class="head-row">
									<div class="form-blog-header">
										<h1 >Bình luận</h1>
										<p class="subtitle">Quản lý bình luận — duyệt, trả lời hoặc ẩn những bình luận không phù hợp.</p>
									</div>
									<div class="controls">
										<form method="GET" action="{{ route('admin.comments.index') }}" class="search-filter-form">
											<div class="control-search">
												<i class='bx bx-search'></i>
												<input name="search" placeholder="Tìm kiếm bình luận hoặc tên người viết..." value="{{ request('search') }}" />
											</div>
											<select name="status" class="filter-select" onchange="this.form.submit()">
												<option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Tất cả</option>
												<option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
												<option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
											</select>
											{{-- <div class="btn-group">
												<button type="submit" class="btn">Lọc</button>
											</div> --}}
										</form>
									</div>
								</div>

								<div class="stats-grid">
									<span class="stat-card">
										<h3 class="stat-num">{{ $stats['total'] }}</h3>
										<p class="stat-label">Tổng bình luận</p>
									</span>
									<span class="stat-card">
										<h3 class="stat-num">{{ $stats['approved'] }}</h3>
										<p class="stat-label">Đã duyệt</p>
									</span>
									<span class="stat-card">
										<h3 class="stat-num">{{ $stats['pending'] }}</h3>
										<p class="stat-label">Chờ duyệt</p>
									</span>
								</div>

								@include('components.flash_message')

								<div class="comments-list">
									@forelse($comments as $comment)
									<article class="comment-card" data-comment-id="{{ $comment->id }}">
										<div class="comment-avatar">{{ strtoupper(substr($comment->author_name, 0, 1)) }}</div>
										<div class="comment-body">
											<div class="comment-meta">
												<strong>{{ $comment->author_name }}</strong>
												<span>•</span>
												<span>Bài: <a href="{{ route('admin.posts.show', $comment->post_id) }}" target="_blank">{{ Str::limit($comment->post->title, 15) }}</a></span>
												<span>•</span>
												<span>{{ $comment->created_at->format('Y-m-d H:i') }}</span>
												<span>•</span>
												<span class="status-badge {{ $comment->is_approved ? 'approved' : 'pending' }}">
												{{ $comment->is_approved ? '✓ Đã duyệt' : '⏳ Chờ duyệt' }}
												</span>
											</div>

											{{-- Nếu là comment trả lời --}}
											@if($comment->parent_id)
												<div class="reply-indicator">
													<span>Trả lời comment từ:</span>
													<strong>{{ $comment->parent->author_name }}</strong>
												</div>
											@endif

											<div class="comment-text">{{ Str::limit($comment->content, 100) }}</div>

											<div class="comment-actions">
												{{-- Nút Duyệt/Bỏ duyệt --}}
												@if(!$comment->is_approved)
													<form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST" style="display: inline;">
														@csrf @method('PATCH')
														<button type="submit" class="action-btn positive" title="Duyệt bình luận này">✓ Duyệt</button>
													</form>
												@else
													<form action="{{ route('admin.comments.unapprove', $comment->id) }}" method="POST" style="display: inline;">
														@csrf @method('PATCH')
														<button type="submit" class="action-btn warning" title="Bỏ duyệt">⚠ Bỏ duyệt</button>
													</form>
												@endif

												{{-- Nút Xem chi tiết --}}
												<a href="{{ route('admin.comments.show', $comment->id) }}" class="action-btn">👁 Xem</a>

												{{-- Nút Xóa --}}
												<form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
													@csrf @method('DELETE')
													<button type="submit" class="action-btn danger">🗑 Xóa</button>
												</form>
											</div>

											{{-- Hiển thị các reply --}}
											@if($comment->children->count() > 0)
												<div class="replies-section">
													<div class="replies-header">
														<span>{{ $comment->children->count() }} trả lời</span>
													</div>
													@foreach($comment->children as $reply)
														<div class="reply-card">
															<div class="reply-avatar">{{ strtoupper(substr($reply->author_name, 0, 1)) }}</div>
															<div class="reply-content">
																<div class="reply-meta">
																	<strong>{{ $reply->author_name }}</strong>
																	<span>{{ $reply->created_at->format('Y-m-d H:i') }}</span>
																</div>
																<p>{{ Str::limit($reply->content, 100) }}</p>
																<div class="reply-actions">
																	<form action="{{ route('admin.comments.destroy', $reply->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Xóa?');">
																		@csrf @method('DELETE')
																		<button type="submit" class="action-btn small danger">Xóa</button>
																	</form>
																</div>
															</div>
														</div>
													@endforeach
												</div>
											@endif
										</div>
									</article>
									@empty
										<div class="no-comments">
											<p>Chưa có bình luận nào</p>
										</div>
									@endforelse
								</div>

								<!-- Pagination -->
								<div class="pagination-wrapper">
									 {{ $comments->links() }}
								</div>
							</section>
							<footer class="foot">© 2025 AdminHub · Giao diện mẫu</footer>

							<div id="modal-root"></div>
							<div id="toast-root"></div>
						</div>

						{{-- Modal Trả lời bình luận --}}
						<div id="reply-modal" class="modal" style="display: none;">
							<div class="modal-content">
								<div class="modal-header">
									<h3>Trả lời bình luận</h3>
									<button type="button" class="close-btn" onclick="closeReplyModal()">✕</button>
								</div>
								<form id="reply-form" method="POST">
									@csrf
									<div class="form-group">
										<label>Từ: <span id="reply-from" style="font-weight: bold;"></span></label>
									</div>
									<div class="form-group">
										<label for="reply-content">Nội dung trả lời:</label>
										<textarea id="reply-content" name="content" required maxlength="1000" rows="5" placeholder="Nhập nội dung trả lời..."></textarea>
										<small>Tối đa 1000 ký tự</small>
									</div>
									<div class="modal-actions">
										<button type="button" class="btn" onclick="closeReplyModal()">Hủy</button>
										<button type="submit" class="btn primary">Gửi trả lời</button>
									</div>
								</form>
							</div>
						</div>
							

						
@endsection
@push('scripts')
		<script src="{{ asset('js/comment/comment.js') }}"></script>	
		<script>
			// Modal functions
			function openReplyModal(commentId, authorName) {
				document.getElementById('reply-from').textContent = authorName;
				document.getElementById('reply-form').action = `/admin/comments/${commentId}/reply`;
				document.getElementById('reply-modal').style.display = 'flex';
				document.getElementById('reply-content').focus();
			}

			function closeReplyModal() {
				document.getElementById('reply-modal').style.display = 'none';
				document.getElementById('reply-content').value = '';
			}

			// Đóng modal khi click ngoài
			document.getElementById('reply-modal')?.addEventListener('click', function(e) {
				if (e.target === this) {
					closeReplyModal();
				}
			});
		</script>
@endpush