@extends('layouts.dashboard')
@section('title', 'Edit Post')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/posts/edit.css') }}">  
@endpush
@section('content')
	<div class="edit-post-container">
		<!-- Back Button -->
		<div class="edit-header">
			<a href="{{ route('admin.posts.index') }}" class="back-link"><i class='bx bx-arrow-back'></i> Quay lại</a>
		</div>

		<!-- Edit Form Card -->
		<div class="edit-card">
			<h2><i class='bx bx-edit'></i> Sửa bài viết</h2>
			
			<form action="{{ route('admin.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
				@csrf
				@method('PUT')
						<!-- Basic Info -->
						<div class="edit-section">
							<h3>Thông tin cơ bản</h3>

							<div class="form-group">
								<label>Tiêu đề <span class="required">*</span></label>
								<input type="text" name="title" value="{{ old('title', $post->title) }}" placeholder="Tiêu đề bài viết" required />
								@error('title')
									<span class="error">{{ $message }}</span>
								@enderror
							</div>

							<div class="form-group">
								<label>Mô tả ngắn <span class="required">*</span></label>
								<textarea name="excerpt" placeholder="Mô tả ngắn (dùng cho preview)" required>{{ old('excerpt', $post->excerpt) }}</textarea>
								@error('excerpt')
									<span class="error">{{ $message }}</span>
								@enderror
							</div>

							<div class="form-row">
								<div class="form-group">
									<label>Danh mục <span class="required">*</span></label>
									<select name="category_id" required>
										<option value="">-- Chọn danh mục --</option>
										@foreach($categories as $category)
											<option value="{{ $category->id }}" 
												{{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
												{{ $category->name }}
											</option>
										@endforeach
									</select>
									@error('category_id')
										<span class="error">{{ $message }}</span>
									@enderror
								</div>
								<div class="form-group">
									<label>Tags</label>
									<select name="tags[]" multiple>
										@foreach($tags as $tag)
											<option value="{{ $tag->id }}"
												{{ in_array($tag->id, $post->tags->pluck('id')->toArray()) ? 'selected' : '' }}>
												{{ $tag->name }}
											</option>
										@endforeach
									</select>
									@error('tags')
										<span class="error">{{ $message }}</span>
									@enderror
								</div>
							</div>

							<div class="form-group">
								<label>Trạng thái</label>
								<select name="status" required>
									<option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft (Nháp)</option>
									<option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published (Công bố)</option>
								</select>
								@error('status')
									<span class="error">{{ $message }}</span>
								@enderror
							</div>
						</div>

						<!-- Thumbnail -->
						<div class="edit-section">
							<h3>Ảnh bìa</h3>
							<div class="thumbnail-upload">
								<div class="thumbnail-preview-area" id="thumbnailPreview">
									@if($post->thumbnail)
										<img src="{{ asset($post->thumbnail) }}" alt="Current thumbnail" id="thumbnailImg">
									@else
										<img src="https://via.placeholder.com/1200x400?text=No+Thumbnail" alt="No thumbnail" id="thumbnailImg">
									@endif
								</div>
								<div class="thumbnail-controls">
									<label class="btn-upload-label"><i class='bx bx-image'></i> Chọn ảnh</label>
									<input type="file" name="thumbnail" id="thumbnailInput" accept="image/*" style="display:none;">
									<button type="button" class="btn-remove" id="remove-thumbnail"><i class='bx bx-trash'></i> Xóa ảnh</button>
								</div>
								<p style="font-size: 12px; color: #999; margin-top: 8px;">Để trống nếu không muốn thay đổi ảnh hiện tại</p>
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
							<textarea name="content" class="editor-textarea" placeholder="Nội dung chi tiết bài viết..." required>{{ old('content', $post->content) }}</textarea>
							@error('content')
								<span class="error">{{ $message }}</span>
							@enderror
						</div>

						<!-- SEO Meta -->
						<div class="edit-section">
							<h3><i class='bx bx-search'></i> Thông tin SEO</h3>
							
							<div class="form-group">
								<label>Meta Title</label>
								<input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" placeholder="Tiêu đề hiển thị trên Google" />
								<small>Khuyến nghị: 50-60 ký tự</small>
								@error('meta_title')
									<span class="error">{{ $message }}</span>
								@enderror
							</div>

							<div class="form-group">
								<label>Meta Description</label>
								<textarea name="meta_description" placeholder="Mô tả hiển thị trên Google" maxlength="160">{{ old('meta_description', $post->meta_description) }}</textarea>
								<small>Khuyến nghị: 120-160 ký tự</small>
								@error('meta_description')
									<span class="error">{{ $message }}</span>
								@enderror
							</div>
						</div>

						<!-- Form Actions -->
						<div class="form-actions">
							<a href="{{ route('admin.posts.index') }}" class="btn-secondary"><i class='bx bx-x'></i> Hủy</a>
							<button type="submit" class="btn-primary"><i class='bx bx-check'></i> Lưu thay đổi</button>
						</div>
					</form>
				</div>
			</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/posts/edit.js') }}"></script>   
@endpush