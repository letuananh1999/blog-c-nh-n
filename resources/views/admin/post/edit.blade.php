@extends('layouts.dashboard')
@section('title', 'Post Detail')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/posts/edit.css') }}">  
@endpush
@section('content')
	<div class="edit-post-container">
				<!-- Back Button -->
				<div class="edit-header">
					<a href="detail-posts.html" class="back-link"><i class='bx bx-arrow-back'></i> Quay lại</a>
				</div>

				<!-- Edit Form Card -->
				<div class="edit-card">
					<h2><i class='bx bx-edit'></i> Sửa bài viết</h2>
					
					<form id="edit-post-form">
						<!-- Basic Info -->
						<div class="edit-section">
							<h3>Thông tin cơ bản</h3>

							<div class="form-group">
								<label>Tiêu đề <span class="required">*</span></label>
								<input type="text" id="edit-title" value="10 nguyên tắc UI cơ bản" placeholder="Tiêu đề bài viết" required />
							</div>

							<div class="form-group">
								<label>Mô tả ngắn <span class="required">*</span></label>
								<textarea id="edit-excerpt" placeholder="Mô tả ngắn (dùng cho preview)" required>Tối giản, tương phản và hệ thống lưới — các nguyên tắc căn bản cho UI.</textarea>
							</div>

							<div class="form-row">
								<div class="form-group">
									<label>Danh mục <span class="required">*</span></label>
									<select id="edit-category" required>
										<option value="design">Design</option>
										<option value="dev">Dev</option>
										<option value="marketing">Marketing</option>
										<option value="business">Business</option>
									</select>
								</div>
								<div class="form-group">
									<label>Tags (cách nhau bằng dấu phẩy)</label>
									<input type="text" id="edit-tags" value="design, ui, ux" placeholder="tag1, tag2, tag3" />
								</div>
							</div>
						</div>

						<!-- Thumbnail -->
						<div class="edit-section">
							<h3>Ảnh bìa</h3>
							<div class="thumbnail-upload">
								<div class="thumbnail-preview-area" id="thumbnailPreview">
									<img src="https://via.placeholder.com/1200x400?text=Post+Thumbnail" alt="Current thumbnail" id="thumbnailImg">
								</div>
								<div class="thumbnail-controls">
									<label class="btn-upload-label"><i class='bx bx-image'></i> Chọn ảnh</label>
									<input type="file" id="thumbnailInput" accept="image/*" style="display:none;">
									<button type="button" class="btn-remove"><i class='bx bx-trash'></i> Xóa ảnh</button>
								</div>
							</div>
						</div>

						<!-- Content -->
						<div class="edit-section">
							<h3>Nội dung chi tiết</h3>
							<div class="editor-toolbar">
								<button type="button" class="editor-btn" title="In đậm"><i class='bx bx-bold'></i></button>
								<button type="button" class="editor-btn" title="In nghiêng"><i class='bx bx-italic'></i></button>
								<button type="button" class="editor-btn" title="Heading 2"><i class='bx bx-heading'></i> H2</button>
								<button type="button" class="editor-btn" title="Link"><i class='bx bx-link'></i></button>
								<span class="divider"></span>
								<button type="button" class="editor-btn" id="insert-image-btn" title="Thêm ảnh"><i class='bx bx-image'></i> Ảnh</button>
								<button type="button" class="editor-btn" title="Video"><i class='bx bx-play'></i></button>
								<button type="button" class="editor-btn" title="Code"><i class='bx bx-code'></i></button>
							</div>
							<textarea id="edit-content" class="editor-textarea" placeholder="Nội dung chi tiết bài viết..." required>UI (User Interface) là phần giao diện mà người dùng nhìn thấy và tương tác. Để tạo một giao diện tốt, bạn cần hiểu rõ 10 nguyên tắc cơ bản này:

1. Tối giản
Đừng quá tải giao diện với quá nhiều yếu tố. Mỗi element nên có một mục đích rõ ràng. Loại bỏ những thứ không cần thiết.

2. Tương phản
Sử dụng tương phản màu sắc để tạo sự phân biệt giữa các element. Điều này giúp người dùng dễ dàng nhận biết những phần quan trọng.

3. Hệ thống lưới
Sắp xếp các element theo một hệ thống lưới nhất quán. Điều này tạo cảm giác cân đối và chuyên nghiệp cho thiết kế.
							</textarea>
						</div>

						<!-- SEO Meta -->
						<div class="edit-section">
							<h3><i class='bx bx-search'></i> Thông tin SEO</h3>
							
							<div class="form-group">
								<label>Meta Title</label>
								<input type="text" id="edit-meta-title" value="10 nguyên tắc UI cơ bản - AdminHub" placeholder="Tiêu đề hiển thị trên Google" />
								<small>Khuyến nghị: 50-60 ký tự</small>
							</div>

							<div class="form-group">
								<label>Meta Description</label>
								<textarea id="edit-meta-description" placeholder="Mô tả hiển thị trên Google" maxlength="160">Tối giản, tương phản và hệ thống lưới — các nguyên tắc căn bản cho UI.</textarea>
								<small>Khuyến nghị: 120-160 ký tự</small>
							</div>
						</div>

						<!-- Form Actions -->
						<div class="form-actions">
							<a href="detail-posts.html" class="btn-secondary"><i class='bx bx-x'></i> Hủy</a>
							<button type="submit" class="btn-primary"><i class='bx bx-check'></i> Lưu thay đổi</button>
						</div>
					</form>
				</div>
			</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/posts/edit.js') }}"></script>   
@endpush