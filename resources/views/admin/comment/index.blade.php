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
										<div class="control-search">
											<i class='bx bx-search'></i>
											<input placeholder="Tìm kiếm bình luận hoặc tên người viết..." />
										</div>
										<select class="filter-select">
											<option value="all">Tất cả</option>
											<option value="visible">Hiện</option>
											<option value="hidden">Ẩn</option>
										</select>
										<div class="btn-group">
											<button class="btn">Lọc</button>
											<!-- <button class="btn primary">Xóa đã chọn</button> -->
										</div>
									</div>
								</div>

								<div class="stats-grid">
									<span class="stat-card">
                    <h3 class="stat-num">24</h3>
                    <p class="stat-label">Tổng bình luận</p>
                  </span>
									<span class="stat-card">
                    <h3 class="stat-num">18</h3>
                    <p class="stat-label">Đã duyệt</p>
                  </span>
									<span class="stat-card">
                    <h3 class="stat-num">6</h3>
                    <p class="stat-label">Chờ duyệt</p>
                  </span>
								</div>

								<div class="comments-list">
									@foreach($comments as $comment)
									<article class="comment-card" data-comment-id="{{ $comment->id }}">
										<div class="comment-avatar">{{ strtoupper(substr($comment->author_name, 0, 1)) }}</div>
										<div class="comment-body">
											<div class="comment-meta"><strong>{{ $comment->author_name }}</strong>
                        <span>•</span>
                        <span>Bài: {{ Str::limit($comment->post->title, 15) }}</span>
                        <span>•</span>
                        <span>{{ $comment->created_at->format('Y-m-d') }}</span>
                      </div>
											<div class="comment-text">{{ Str::limit($comment->content, 50) }}</div>
											<div class="comment-actions">
												<button class="action-btn positive">Duyệt</button>
												<button class="action-btn">Trả lời</button>
												<button class="action-btn danger">Ẩn</button>
											</div>
										</div>
									</article>
									@endforeach
								</div>

								<!-- Pagination -->
								 <ul class="pagination">
   							 {{ $comments->links() }}
								</ul>

									{{-- <article class="comment-card" data-comment-id="2">
										<div class="comment-avatar">B</div>
										<div class="comment-body">
											<div class="comment-meta"><strong>Bé Bình</strong>
                        <span>•</span>
                        <span> Bài: Node patterns</span>
                        <span>•</span>
                        <span>2025-08-21</span>
                      </div>
											<div class="comment-text">Mình không đồng ý với phần này, mình thấy rằng...</div>
											<div class="comment-actions">
												<button class="action-btn positive">Duyệt</button>
												<button class="action-btn">Trả lời</button>
												<button class="action-btn danger">Ẩn</button>
											</div>
										</div>
									</article> --}}
								</div>

								{{-- <div class="table-card card">
									<table>
										<thead>
											<tr>
												<th>ID</th>
                        <th>Nội dung</th>
                        <th>Người</th>
                        <th>Bài viết</th>
                        <th>Ngày</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
										</thead>
										<tbody>
											@foreach($comments as $comment)
											<tr>
                        <td>{{ $comment->id }}</td>
                        <td>{{ Str::limit($comment->content, 15) }}</td>
                        <td>{{ $comment->author_name }}</td>
                        <td>{{ Str::limit($comment->post->title, 15) }}</td>
                        <td>{{ $comment->created_at->format('Y-m-d') }}</td>
                        <td>{{ $comment->status }}</td>
                        <td>
                          <button class="btn">Sửa</button>
                        </td>
                    	</tr>
											@endforeach
                  
										</tbody>
									</table>
								</div> --}}
							</section>
							<footer class="foot">© 2025 AdminHub · Giao diện mẫu</footer>

							<div id="modal-root"></div>
							<div id="toast-root"></div>
						</div>
							

						
@endsection
@push('scripts')
		<script src="{{ asset('js/comment/comment.js') }}"></script>	
@endpush