@extends('layouts.dashboard')
@section('title', 'Create Post')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/posts/create.css') }}">
@endpush
@section('content')
     <div class="container form-blog-page">
        <div class="form-blog-header">
          <h1><i class='bx bx-pen'></i> Thêm bài viết mới</h1>
          <p>Tạo bài viết mới với tiêu đề, nội dung, hình ảnh, và tối ưu hóa SEO.</p>
        </div>

        <form id="add-blog-form" class="form-blog-card" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <!-- Basic Info Section -->
          <div class="form-section">
            <div class="form-section-title"><i class='bx bx-info-circle'></i> Thông tin cơ bản</div>
            
            <div class="form-group">
              <label>Tiêu đề bài viết <span class="required">*</span></label>
              <input type="text" name="title" placeholder="Nhập tiêu đề bài viết" required />
            </div>

            <div class="form-group">
              <label>Mô tả ngắn <span class="required">*</span></label>
              <input type="text" name="excerpt" placeholder="Mô tả ngắn 1-2 dòng để hiển thị trong danh sách" required />
            </div>

            <div class="form-group">
              <label>Danh mục</label>
              <select name="category_id">
                <option value="">-- Chọn danh mục --</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label>Tags</label>
              <select name="tags[]" multiple>
                @foreach($tags as $tag)
                  <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="form-group">
              <label>Trạng thái</label>
              <select name="status" required>
                <option value="draft">Draft (Nháp)</option>
                <option value="published">Published (Công bố)</option>
              </select>
            </div>
          </div>

          <!-- Thumbnail Section -->
          <div class="form-section">
            <div class="form-section-title"><i class='bx bx-image'></i> Hình ảnh đại diện</div>
            
            <div class="thumbnail-section">
              <div class="thumbnail-preview-wrap">
                <div class="thumbnail-preview" id="thumbnailPreview">
                  <div class="thumbnail-placeholder">
                    <i class='bx bx-image'></i>
                    <p>Chưa có ảnh</p>
                  </div>
                </div>
                <div class="thumbnail-actions">
                  <label class="file-input-label" for="thumbnail-input">
                    <i class='bx bx-upload'></i> Chọn ảnh
                  </label>
                  <button type="button" class="btn-remove-thumb" id="remove-thumbnail" style="display:none;">
                    <i class='bx bx-trash'></i> Xóa
                  </button>
                </div>
              </div>
              <input type="file" id="thumbnail-input" class="file-input" name="thumbnail" accept="image/*" />

              <div class="form-group full" style="margin: 0;">
                <p style="font-size: 12px; color: var(--muted); margin: 0;">
                  💡 Gợi ý: Chọn ảnh 1200x630px hoặc tỷ lệ 16:9 để tốt nhất trên mạng xã hội.
                </p>
              </div>
            </div>
          </div>

          <!-- Description Section with Editor -->
          <div class="form-section">
            <div class="form-section-title"><i class='bx bx-align-left'></i> Nội dung chi tiết</div>
            
            <div class="form-group full">
              <label>Mô tả chi tiết <span class="required">*</span></label>
              <div class="editor-wrap">
                <div class="editor-toolbar">
                  <button type="button" title="Bold"><i class='bx bx-bold'></i> B</button>
                  <button type="button" title="Italic"><i class='bx bx-italic'></i> I</button>
                  <button type="button" title="Underline"><i class='bx bx-underline'></i> U</button>
                  <button type="button" title="Thêm link"><i class='bx bx-link'></i> Link</button>
                  <button type="button" title="Thêm ảnh"><i class='bx bx-image'></i> Ảnh</button>
                  <button type="button" title="Danh sách"><i class='bx bx-list-ul'></i> Danh sách</button>
                  <button type="button" title="Trích dẫn"><i class='bx bx-quote'></i> Trích dẫn</button>
                  <button type="button" title="Code"><i class='bx bx-code'></i> Code</button>
                </div>
                <textarea name="content" class="editor-content" placeholder="Nhập nội dung chi tiết của bài viết tại đây. Bạn có thể dùng toolbar để định dạng text, thêm link, ảnh, v.v." required></textarea>
              </div>
            </div>
          </div>

          <!-- Meta Section for SEO -->
          <div class="form-section">
            <div class="form-section-title"><i class='bx bx-search-alt'></i> Tối ưu hóa SEO</div>
            
            <div class="meta-section">
              <p>Điền thông tin SEO để bài viết dễ được tìm thấy trên công cụ tìm kiếm.</p>
              
              <div class="form-row">
                <div class="form-group">
                  <label>Meta Title</label>
                  <input type="text" name="meta_title" placeholder="Tiêu đề SEO (55-60 ký tự)" maxlength="60" />
                </div>
                <div class="form-group">
                  <label>Meta Description</label>
                  <textarea name="meta_description" placeholder="Mô tả cho công cụ tìm kiếm (150-160 ký tự)" maxlength="160" style="min-height: 60px;"></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <button type="button" class="btn-draft" id="save-draft">
              <i class='bx bx-save'></i> Lưu nháp
            </button>
            <button type="button" class="btn-cancel" id="cancel-form">
              <i class='bx bx-x'></i> Hủy
            </button>
            <button type="submit" class="btn-submit">
              <i class='bx bx-check'></i> Đăng bài
            </button>
          </div>
        </form>
      </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/posts/create.js') }}"></script>
@endpush