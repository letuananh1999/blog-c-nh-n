@extends('layouts.dashboard')
@section('title', 'Users')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/index.css') }}">
@endpush
@section('content')
<div class="container">
    <section class="users-wrap">
        <div class="head-row head-title">
            <div class="left">
                <h1 class="cat-title">Người dùng</h1>
                <p class="subtitle">
                    Quản lý người dùng — xem, chỉnh sửa hoặc xóa tài khoản người dùng.
                </p>
            </div>
            <div class="controls">
                  <div class="control-search"><i class='bx bx-search'></i><input placeholder="Tìm theo tên hoặc email"/></div>
                  <select class="filter-select"><option>All</option><option>Active</option><option>Blocked</option></select>
                  <a href="{{ route('admin.users.create') }}">
                    <button class="btn">Thêm người dùng</button>
                  </a>
            </div>
        </div>

          <div class="stats-grid">
            <div class="stat-card"><div class="stat-num">10</div><div class="stat-label">Tổng người dùng</div></div>
            <div class="stat-card"><div class="stat-num">8</div><div class="stat-label">Hoạt động</div></div>
            <div class="stat-card"><div class="stat-num">2</div><div class="stat-label">Bị khoá</div></div>
          </div>

          <div class="cards-grid">
            <div class="user-card">
              <div class="user-avatar">A</div>
              <div class="user-info"><p class="user-name">Admin</p><p class="user-email">admin@example.com</p></div>
              <div class="user-actions"><button class="action-btn ">Sửa</button><button class="action-btn">Khoá</button></div>
            </div>
            <div class="user-card">
              <div class="user-avatar">T</div>
              <div class="user-info"><p class="user-name">Tran</p><p class="user-email">tran@example.com</p></div>
              <div class="user-actions"><button class="action-btn">Sửa</button><button class="action-btn">Khoá</button></div>
            </div>
          </div>

          <div class="table-card card">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Tên</th>
                  <th>Email</th>
                  <th>Vai trò</th>
                  <th>Trạng thái</th>
                  <th>Hành động</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Admin</td>
                  <td>admin@example.com</td>
                  <td>Admin</td>
                  <td>Active</td>
                  <td><button class="btn secondary">Sửa</button></td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Tran</td>
                  <td>tran@example.com</td>
                  <td>User</td>
                  <td>Blocked</td>
                  <td><button class="btn secondary">Sửa</button></td>
                </tr>
              </tbody>
            </table>
          </div>
            
  </section>
    <footer class="foot">© 2025 AdminHub · Giao diện mẫu</footer>

</div>
@endsection 