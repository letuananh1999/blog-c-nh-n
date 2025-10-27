@extends('layouts.dashboard')
@section('title', 'Post Detail')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/posts/detail.css') }}">
@endpush
@section('content')
  	<div class="detail-posts-container">
				<!-- Back Button & Actions -->
				<div class="detail-header">
					<a href="blog.html" class="back-link"><i class='bx bx-arrow-back'></i> Quay lại</a>
					<div class="detail-actions">
						<a href="edit-post.html" class="btn-action primary"><i class='bx bx-edit'></i> Sửa bài viết</a>
						<button id="delete-btn" class="btn-action danger"><i class='bx bx-trash'></i> Xóa</button>
					</div>
				</div>

				<!-- View Mode -->
				<div id="view-mode" class="detail-view-mode">
					<!-- Post Header with Thumbnail -->
					<div class="post-thumbnail-section">
						<img src="https://via.placeholder.com/1200x400?text=Post+Thumbnail" alt="Post thumbnail" class="post-thumbnail">
						<div class="post-thumbnail-overlay">
							<span class="post-category">Design</span>
							<span class="post-date">12 Tháng 1, 2025</span>
						</div>
					</div>

					<!-- Post Content -->
					<div class="post-detail-card">
						<div class="post-header-info">
							<h1 class="post-title">10 nguyên tắc UI cơ bản</h1>
							<p class="post-excerpt">Tối giản, tương phản và hệ thống lưới — các nguyên tắc căn bản cho UI.</p>
							
							<div class="post-meta">
								<span class="meta-item"><i class='bx bx-user'></i> Admin</span>
								<span class="meta-item"><i class='bx bx-calendar'></i> 12 Tháng 1, 2025</span>
								<span class="meta-item"><i class='bx bx-show'></i> 1,245 lượt xem</span>
								<span class="meta-item"><i class='bx bx-comment'></i> 12 bình luận</span>
							</div>

							<div class="post-tags">
								<span class="tag">design</span>
								<span class="tag">ui</span>
								<span class="tag">ux</span>
							</div>
						</div>

						<!-- Post Body Content -->
						<div class="post-content">
							<h2>Giới thiệu</h2>
							<p>UI (User Interface) là phần giao diện mà người dùng nhìn thấy và tương tác. Để tạo một giao diện tốt, bạn cần hiểu rõ 10 nguyên tắc cơ bản này:</p>

							<h2>1. Tối giản</h2>
							<p>Đừng quá tải giao diện với quá nhiều yếu tố. Mỗi element nên có một mục đích rõ ràng. Loại bỏ những thứ không cần thiết.</p>
							<img src="https://via.placeholder.com/800x400?text=Minimalism+Example" alt="Minimalism example" class="content-image">

							<h2>2. Tương phản</h2>
							<p>Sử dụng tương phản màu sắc để tạo sự phân biệt giữa các element. Điều này giúp người dùng dễ dàng nhận biết những phần quan trọng.</p>

							<h2>3. Hệ thống lưới</h2>
							<p>Sắp xếp các element theo một hệ thống lưới nhất quán. Điều này tạo cảm giác cân đối và chuyên nghiệp cho thiết kế.</p>
							<img src="https://via.placeholder.com/800x400?text=Grid+System" alt="Grid system" class="content-image">

							<h2>Kết luận</h2>
							<p>Những nguyên tắc này là nền tảng của một thiết kế UI tốt. Bằng cách tuân theo chúng, bạn có thể tạo các giao diện vừa đẹp mắt vừa dễ sử dụng.</p>
						</div>

						<!-- SEO Meta Info -->
						<div class="post-seo-info">
							<h3><i class='bx bx-search'></i> Thông tin SEO</h3>
							<div class="seo-grid">
								<div class="seo-item">
									<label>Meta Title:</label>
									<p>10 nguyên tắc UI cơ bản - AdminHub</p>
								</div>
								<div class="seo-item">
									<label>Meta Description:</label>
									<p>Tối giản, tương phản và hệ thống lưới — các nguyên tắc căn bản cho UI.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
@endsection
@push('scripts')
    <script src="{{ asset('js/posts/detail.js') }}"></script> 
@endpush