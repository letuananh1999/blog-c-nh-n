@extends('layouts.dashboard')
@section('title', 'Posts')
@push('styles')
		<link rel="stylesheet" href="{{ asset('css/posts/index.css') }}">
@endpush
@section('content')
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
							{{-- <div class="post-full" style="display:none">Nội dung chi tiết về 10 nguyên tắc UI...</div> --}}
						</article>
            @endforeach
					</div>
                <ul style="margin-top: 10px" class="pagination">
										{{ $posts->links() }}
								</ul>
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