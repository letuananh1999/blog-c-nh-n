@extends('layouts.dashboard')
@section('title', 'Comments')
@section('content')
<div class="container">
							<section class="comments-wrap">
								<div class="head-row head-title">
									<div class="left">
										<h1 class="cat-title">Bình luận</h1>
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
									<article class="comment-card">
										<div class="comment-avatar">A</div>
										<div class="comment-body">
											<div class="comment-meta"><strong>Anh An</strong>
                        <span>•</span>
                        <span>Bài: 10 nguyên tắc UI</span>
                        <span>•</span>
                        <span>2025-09-12</span>
                      </div>
											<div class="comment-text">Bài viết rất hữu ích, cảm ơn tác giả! Tôi có một câu hỏi về việc...</div>
											<div class="comment-actions">
												<button class="action-btn positive">Duyệt</button>
												<button class="action-btn">Trả lời</button>
												<button class="action-btn danger">Ẩn</button>
											</div>
										</div>
									</article>

									<article class="comment-card">
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
									</article>
								</div>

								<div class="table-card card">
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
											<tr>
                        <td>1</td>
                        <td>Rất hay!</td>
                        <td>An</td>
                        <td>10 nguyên tắc UI</td>
                        <td>2025-09-12</td>
                        <td>Hiện</td>
                        <td>
                          <button class="btn">Sửa</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Không chính xác</td>
                        <td>Bình</td>
                        <td>Node patterns</td>
                        <td>2025-08-21</td>
                        <td>Chờ</td>
                        <td>
                          <button class="btn">Sửa</button>
                        </td>
                    </tr>
										</tbody>
									</table>
								</div>

							</section>
							<footer class="foot">© 2025 AdminHub · Giao diện mẫu</footer>
						</div>
@endsection