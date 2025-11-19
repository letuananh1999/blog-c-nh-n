@extends('layouts.dashboard')
@section('title', 'Posts')
@push('styles')
		<link rel="stylesheet" href="{{ asset('css/posts/index.css') }}">
@endpush
@section('content')
  {{-- @foreach ($posts as $post)
      <tr>
        <th scope="row">{{$post->id}}</th>
        <td colspan="2">{{$post->title}}</td>
        <td>{{$post->slug}}</td>
        <td>
          <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-primary">Edit</a>
          <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
          </form>
      </tr>
  @endforeach
  <tr><td colspan="5"></td></tr>
  <tr>
    <td colspan="5">
      <a href="{{ route('admin.posts.create') }}" class="btn btn-success">Create New Post</a>
    </td>
  </tr>
  <tr><td colspan="5"></td></tr>
  <tr></tr> --}}
      <div class="posts-header head-title">
          <div class="left">
            <h1 class="side-menu top text">Danh sách bài viết</h1>
            <p class="sub">Bộ sưu tập bài viết theo chủ đề. Dùng bộ lọc bên trên để thu hẹp kết quả.</p>
          </div>
          <div class="posts-controls">
						<a href="{{ route('admin.posts.create') }}">
							<button id="add-post-btn" class="btn-primary"><i class='bx bx-plus'></i> Thêm bài viết</button>
						</a>
            <div class="view-toggle">
              <button id="view-grid" class="active" title="Grid"><i class='bx bx-grid-alt'></i></button>
              <button id="view-list" title="List"><i class='bx bx-list-ul'></i></button>
            </div>
            <div class="count-info" id="results-count">—</div>
          </div>
        </div>

				<section class="posts-area" id="posts-area">
					<!-- Static posts: moved from JS into HTML so markup is editable and can be replaced by server data later -->
					<div id="posts" class="posts-list">
            @foreach($posts as $post)
            <article class="post-card" data-id="{{ $post->id }}" 
              data-title="{{ $post->title }}" 
              data-excerpt="{{ Str::limit($post->content, 50) }}" 
              data-date="{{ $post->created_at->format('Y-m-d') }}" 
              data-views="{{ $post->views }}">
              <div class="content">
						{{-- <article class="post-card" data-id="1" data-title="10 nguyên tắc UI cơ bản" data-excerpt="Tối giản, tương phản và hệ thống lưới — các nguyên tắc căn bản cho UI." data-tags="design,ui,ux" data-date="2025-01-12" data-views="1245">
							<div class="content"> --}}
								<h3>{{ $post->title }}</h3>
								<p class="excerpt">{{ Str::limit($post->content, 50) }}</p>
								<div class="bottom">
									<div class="tags">
                    <span>design</span>
                    <span>ui</span>
                    <span>ux</span>
                  </div>
									<div class="actions">
                    <button class="read-btn" data-id="1">Đọc</button>
                    <button class="edit-btn" data-id="1" title="Sửa"><i class='bx bx-edit'></i></button>
                    <button class="delete-btn" data-id="1" title="Xóa"><i class='bx bx-trash'></i></button>
                  </div>
								</div>
							</div>
							<div class="post-full" style="display:none">Nội dung chi tiết về 10 nguyên tắc UI...</div>
						</article>
            @endforeach

						{{-- <article class="post-card" data-id="2" data-title="Bộ icon miễn phí cho dự án" data-excerpt="Tải bộ icon đẹp dùng cho sản phẩm của bạn." data-tags="design,assets" data-date="2024-11-02" data-views="842">
							<div class="content">
								<h3>Bộ icon miễn phí cho dự án</h3>
								<p class="excerpt">Tải bộ icon đẹp dùng cho sản phẩm của bạn.</p>
								<div class="bottom">
									<div class="tags">
                    <span>design</span>
                    <span>assets</span>
                  </div>
									<div class="actions">
										<a href="{{ route('admin.posts.show', 2) }}">
											<button class="read-btn" data-id="2">Đọc</button>
										</a>
                    <button class="edit-btn" data-id="2" title="Sửa"><i class='bx bx-edit'></i></button>
                    <button class="delete-btn" data-id="2" title="Xóa"><i class='bx bx-trash'></i></button>
                  </div>
								</div>
							</div>
							<div class="post-full" style="display:none">Link và hướng dẫn sử dụng icon...</div>
						</article>

						<article class="post-card" data-id="3" data-title="Tối ưu React rendering" data-excerpt="Memo, useCallback và kỹ thuật tối ưu hoá render." data-tags="dev,react" data-date="2025-03-22" data-views="3120">
							<div class="content">
								<h3>Tối ưu React rendering</h3>
								<p class="excerpt">Memo, useCallback và kỹ thuật tối ưu hoá render.</p>
								<div class="bottom">
									<div class="tags">
                    <span>dev</span>
                    <span>react</span>
                  </div>
									<div class="actions">
                    <button class="read-btn" data-id="3">Đọc</button>
                    <button class="edit-btn" data-id="3" title="Sửa"><i class='bx bx-edit'></i></button>
                    <button class="delete-btn" data-id="3" title="Xóa"><i class='bx bx-trash'></i></button>
                  </div>
								</div>
							</div>
							<div class="post-full" style="display:none">
								Kỹ thuật và ví dụ tối ưu hóa React...
							</div>
						</article>

						<article class="post-card" data-id="4" data-title="Node.js: patterns cho microservice" data-excerpt="Best practices, logging và observability cho microservices." data-tags="dev,node" data-date="2024-09-10" data-views="2100">
							<div class="content">
								<h3>Node.js: patterns cho microservice</h3>
								<p class="excerpt">Best practices, logging và observability cho microservices.</p>
								<div class="bottom">
									<div class="tags">
                    <span>dev</span>
                    <span>node</span>
                  </div>
									<div class="actions">
                    <button class="read-btn" data-id="4">Đọc</button>
                    <button class="edit-btn" data-id="4" title="Sửa"><i class='bx bx-edit'></i></button>
                    <button class="delete-btn" data-id="4" title="Xóa"><i class='bx bx-trash'></i></button>
                  </div>
								</div>
							</div>
							<div class="post-full" style="display:none">Mô tả chi tiết patterns...</div>
						</article>

						<article class="post-card" data-id="5" data-title="Content marketing 2025" data-excerpt="Tập trung vào câu chuyện thương hiệu và trải nghiệm." data-tags="marketing,content" data-date="2025-02-08" data-views="540">
							<div class="content">
								<h3>Content marketing 2025</h3>
								<p class="excerpt">Tập trung vào câu chuyện thương hiệu và trải nghiệm.</p>
								<div class="bottom">
									<div class="tags">
                    <span>marketing</span>
                    <span>content</span>
                  </div>
									<div class="actions">
                    <button class="read-btn" data-id="5">Đọc</button>
                    <button class="edit-btn" data-id="5" title="Sửa"><i class='bx bx-edit'></i></button>
                    <button class="delete-btn" data-id="5" title="Xóa"><i class='bx bx-trash'></i></button>
                  </div>
								</div>
							</div>
							<div class="post-full" style="display:none">Chiến lược content 2025...</div>
						</article>

						<article class="post-card" data-id="6" data-title="Xây dựng MVP hiệu quả" data-excerpt="Tập trung vào giả thuyết và đo lường nhanh." data-tags="business,startup" data-date="2024-12-15" data-views="760">
							<div class="content">
								<h3>Xây dựng MVP hiệu quả</h3>
								<p class="excerpt">Tập trung vào giả thuyết và đo lường nhanh.</p>
								<div class="bottom">
									<div class="tags">
                    <span>business</span>
                    <span>startup</span>
                  </div>
									<div class="actions">
                    <button class="read-btn" data-id="6">Đọc</button>
                    <button class="edit-btn" data-id="6" title="Sửa"><i class='bx bx-edit'></i></button>
                    <button class="delete-btn" data-id="6" title="Xóa"><i class='bx bx-trash'></i></button>
                  </div>
								</div>
							</div>
							<div class="post-full" style="display:none">Hướng dẫn xây dựng MVP...</div>
						</article>

						<article class="post-card" data-id="7" data-title="Viết docs dễ hiểu" data-excerpt="Cách tổ chức tài liệu để người dùng tìm thấy câu trả lời." data-tags="support,docs" data-date="2025-04-03" data-views="420">
							<div class="content">
								<h3>Viết docs dễ hiểu</h3>
								<p class="excerpt">Cách tổ chức tài liệu để người dùng tìm thấy câu trả lời.</p>
								<div class="bottom">
									<div class="tags">
                    <span>support</span>
                    <span>docs</span>
                  </div>
									<div class="actions">
                    <button class="read-btn" data-id="7">Đọc</button>
                    <button class="edit-btn" data-id="7" title="Sửa"><i class='bx bx-edit'></i></button>
                    <button class="delete-btn" data-id="7" title="Xóa"><i class='bx bx-trash'></i></button>
                  </div>
								</div>
							</div>
							<div class="post-full" style="display:none">Cách viết docs...</div>
						</article> --}}
					</div>
				</section>

        <footer class="foot">© 2025 AdminHub · Giao diện mẫu</footer>

        <!-- Additional Sections -->
        <div class="additional-sections">
          <!-- Most Viewed Posts -->
          <div class="section-card">
            <div class="section-header">
              <h3><i class='bx bx-trending-up'></i> Bài viết xem nhiều nhất</h3>
              <a href="#" class="view-all">Xem tất cả</a>
            </div>
            <div class="small-posts-list">
              <div class="small-post-item">
                <div class="small-post-content">
                  <h4>Tối ưu React rendering</h4>
                  <p class="small-meta"><i class='bx bx-show'></i> 3,120 lượt xem</p>
                </div>
                <span class="small-post-badge">1</span>
              </div>
              <div class="small-post-item">
                <div class="small-post-content">
                  <h4>Node.js: patterns cho microservice</h4>
                  <p class="small-meta"><i class='bx bx-show'></i> 2,100 lượt xem</p>
                </div>
                <span class="small-post-badge">2</span>
              </div>
              <div class="small-post-item">
                <div class="small-post-content">
                  <h4>10 nguyên tắc UI cơ bản</h4>
                  <p class="small-meta"><i class='bx bx-show'></i> 1,245 lượt xem</p>
                </div>
                <span class="small-post-badge">3</span>
              </div>
            </div>
          </div>

          <!-- Favorite Posts -->
          <div class="section-card">
            <div class="section-header">
              <h3><i class='bx bxs-heart'></i> Bài viết yêu thích</h3>
              <a href="#" class="view-all">Xem tất cả</a>
            </div>
            <div class="small-posts-list">
              <div class="small-post-item">
                <div class="small-post-content">
                  <h4>Xây dựng MVP hiệu quả</h4>
                  <p class="small-meta"><i class='bx bx-heart'></i> 45 yêu thích</p>
                </div>
                <span class="small-post-badge star"><i class='bx bxs-star'></i></span>
              </div>
              <div class="small-post-item">
                <div class="small-post-content">
                  <h4>Content marketing 2025</h4>
                  <p class="small-meta"><i class='bx bx-heart'></i> 38 yêu thích</p>
                </div>
                <span class="small-post-badge star"><i class='bx bxs-star'></i></span>
              </div>
              <div class="small-post-item">
                <div class="small-post-content">
                  <h4>Viết docs dễ hiểu</h4>
                  <p class="small-meta"><i class='bx bx-heart'></i> 32 yêu thích</p>
                </div>
                <span class="small-post-badge star"><i class='bx bxs-star'></i></span>
              </div>
            </div>
          </div>

          <!-- Deleted Posts -->
          <div class="section-card">
            <div class="section-header">
              <h3><i class='bx bx-trash'></i> Bài viết đã xóa</h3>
              <a href="#" class="view-all">Khôi phục tất cả</a>
            </div>
            <div class="small-posts-list">
              <div class="small-post-item deleted">
                <div class="small-post-content">
                  <h4>SEO tips & tricks</h4>
                  <p class="small-meta"><i class='bx bx-calendar'></i> 2025-01-05</p>
                </div>
                <span class="small-post-badge trash"><i class='bx bx-trash'></i></span>
              </div>
              <div class="small-post-item deleted">
                <div class="small-post-content">
                  <h4>Web Performance Guide</h4>
                  <p class="small-meta"><i class='bx bx-calendar'></i> 2025-01-02</p>
                </div>
                <span class="small-post-badge trash"><i class='bx bx-trash'></i></span>
              </div>
              <div class="small-post-item deleted">
                <div class="small-post-content">
                  <h4>API Design Best Practices</h4>
                  <p class="small-meta"><i class='bx bx-calendar'></i> 2024-12-28</p>
                </div>
                <span class="small-post-badge trash"><i class='bx bx-trash'></i></span>
              </div>
            </div>
          </div>
        </div>
      </main>
    </section>
   

  <!-- Modal -->
  <div id="modal" class="modal" aria-hidden="true">
    <div class="backdrop" data-close="true"></div>
    <div class="panel" role="dialog" aria-modal="true">
      <button class="close" data-close="true">×</button>
      <div id="modal-content">
        <!-- content injected by JS -->
      </div>
    </div>
  </div>
@endsection