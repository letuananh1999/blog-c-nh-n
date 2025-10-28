@extends('layouts.dashboard')
@section('title', 'Categories')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/category/index.css') }}">
@endpush
@section('content')
      <div class="container categories-page">
        <div class="cat-head head-title" role="banner" aria-label="Danh mục header">
          <div class="cat-head-left left">
            <h1 class="cat-title">Danh sách danh mục</h1>
            <p class="cat-desc">
              Quản lý các danh mục bài viết của bạn — thêm, sửa, xóa và xem số lượng bài trong từng mục.
            </p>
          </div>

          <div class="cat-actions" role="region" aria-label="Công cụ danh mục">
            <div class="form-input search-box" role="search" aria-label="Tìm danh mục">
              <input id="cate-search" type="search" placeholder="Tìm danh mục..." aria-label="Tìm danh mục" />
              <button id="cate-search-btn" class="search-btn" aria-label="Tìm">
                <i class='bx bx-search' aria-hidden="true"></i>
              </button>
            </div>

            <button id="add-cat" class="btn primary add-cat-btn" aria-label="Thêm danh mục">+ Thêm danh mục</button>
          </div>
        </div>

        <section class="cards-grid" aria-label="Danh sách thẻ danh mục">
          <article class="cat-card card" data-id="1">
            <div class="card-top">
              <h3 class="card-title">Design</h3>
              <span class="badge">12</span>
            </div>
            <p class="muted">Bài về UI, UX và thiết kế giao diện.</p>
          </article>

          <article class="cat-card card" data-id="2">
            <div class="card-top">
              <h3 class="card-title">Development</h3>
              <span class="badge">8</span>
            </div>
            <p class="muted">Mẹo lập trình, pattern và best practices.</p>
          </article>

          <article class="cat-card card" data-id="3">
            <div class="card-top">
              <h3 class="card-title">Marketing</h3>
              <span class="badge">5</span>
            </div>
            <p class="muted">Chiến lược nội dung và SEO.</p>
          </article>
        </section>

        <div class="card table-card">
          <div class="table-wrapper">
            <table id="cat-table" aria-label="Bảng danh mục">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Tên danh mục</th>
                  <th>Mô tả</th>
                  <th>Số bài</th>
                  <th>Ngày tạo</th>
                  <th>Hành động</th>
                </tr>
              </thead>
              <tbody>
                <!-- Ví dụ: nếu bạn render động bằng JS, script bên dưới sẽ thêm data-label cho từng td -->
                <tr>
                  <td>1</td>
                  <td>Design</td>
                  <td>UI/UX & thiết kế</td>
                  <td>12</td>
                  <td>2025-09-20</td>
                  <td><button class="btn small">Sửa</button> <button class="btn small danger">Xóa</button></td>
                </tr>
                <!-- JS sẽ tự thêm data-label dựa trên thead -->
              </tbody>
            </table>
          </div>
        </div>
      </div>



      <!-- modal + toast roots for cate interactions -->
		<div id="modal-root"></div>
		<div id="toast-root"></div>

		<!-- modal + toast roots for cate interactions -->
		<div id="modal-root"></div>
		<div id="toast-root"></div>
@endsection
@push('scripts')
    <script src="{{ asset('js/category/category.js') }}"></script>
@endpush