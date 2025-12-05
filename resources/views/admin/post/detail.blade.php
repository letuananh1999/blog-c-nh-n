@extends('layouts.dashboard')
@section('title', 'Post Detail')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/posts/detail.css') }}">
@endpush
@section('content')
  	<div class="detail-posts-container">
				<!-- Back Button & Actions -->
				<div class="detail-header">
					<a href="{{ route('admin.posts.index') }}" class="back-link"><i class='bx bx-arrow-back'></i> Quay lại</a>
					<div class="detail-actions">
						<a href="{{route('admin.posts.edit',$post->id)}}" class="btn-action primary"><i class='bx bx-edit'></i> Sửa bài viết</a>
						<button id="delete-btn" data-id="{{ $post->id }}" class="btn-action danger"><i class='bx bx-trash'></i> Xóa</button>
					</div>
				</div>

				<!-- View Mode -->
				<div id="view-mode" class="detail-view-mode">
					<!-- Post Header with Thumbnail -->
					<div class="post-thumbnail-section">
						<img src="{{ $post->thumbnail }}" alt="Post thumbnail" class="post-thumbnail">
						<div class="post-thumbnail-overlay">
							
							<span class="post-category">{{ $post->category->name }}</span>
							<span class="post-date">{{ $post->created_at->format('d F, Y') }}</span>
						</div>
					</div>

					<!-- Post Content -->
					<div class="post-detail-card">
						<div class="post-header-info">

							<h1 class="post-title">{{ $post->title }}</h1>
							<p class="post-excerpt">{{ $post->excerpt }}</p>
							
							<div class="post-meta">
								
								<span class="meta-item"><i class='bx bx-user'></i> {{ $post->user->name }}</span>
								<span class="meta-item"><i class='bx bx-calendar'></i> {{ $post->created_at->format('d F, Y') }}</span>
								<span class="meta-item"><i class='bx bx-show'></i> {{ number_format($post->views_count) }} lượt xem</span>
								<span class="meta-item"><i class='bx bx-comment'></i> {{ $post->comments_count }} bình luận</span>
							</div>

							<div class="post-tags">

								@foreach($post->tags as $tag)
									<span class="tag">{{ $tag->name }}</span>
								@endforeach
							</div>
						</div>

						<!-- Post Body Content -->
						<div class="post-content">
							{!! $post->content !!}
						</div>

						<!-- SEO Meta Info -->
						<div class="post-seo-info">
							<h3><i class='bx bx-search'></i> Thông tin SEO</h3>
							<div class="seo-grid">
								<div class="seo-item">
									<label>Meta Title:</label>
									<p>{{ $post->meta_title }}</p>
								</div>
								<div class="seo-item">
									<label>Meta Description:</label>
									<p>{{ $post->meta_description }}</p>
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