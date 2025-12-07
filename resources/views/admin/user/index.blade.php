@extends('layouts.dashboard')
@section('title', 'Users')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/index.css') }}">
@endpush
@section('content')
<div class="containerr">
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
            @foreach ($users as $user)
            <div class="user-card">
              <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
              <div class="user-info">
                <p class="user-name">{{ $user->name }}</p>
                <p class="user-email">{{ $user->email }}</p>
                <p class="user-role">{{ $user->role }}</p>
              </div>
              <div class="user-actions">
                <a href="{{ route('admin.users.edit', $user->id) }}" class="action-btn">Sửa</a>
                <form method="POST" action="{{ route('admin.users.toggleStatus', $user->id) }}" style="display:inline;">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="action-btn" onclick="return confirm('Bạn chắc chắn?')">
                    {{ $user->status === 'active' ? 'Khóa' : 'Mở' }}
                  </button>
                </form>
              </div>
            </div>
            @endforeach
             <ul class="pagination">
              {{$users->links()}}
             </ul>
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
                @foreach ($users as $user)
                <tr>
                  <td>
                    @if ($user->avatar && file_exists(public_path('img/user/' . $user->avatar)))
                      <img src="{{ asset('img/user/' . $user->avatar) }}" alt="{{ $user->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                    @else
                      <div style="width: 40px; height: 40px; border-radius: 50%; background: #ccc; display: flex; align-items: center; justify-content: center;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                      </div>
                    @endif
                  </td>
                  <td>{{ $user->name }}</td>
                  <td>{{ $user->email }}</td>
                  <td>{{ $user->role }}</td>
                  <td>
                    <span class="status-badge {{ $user->status === 'active' ? 'active' : 'blocked' }}">
                      {{ $user->status === 'active' ? '✓ Hoạt động' : '✗ Bị khóa' }}
                    </span>
                  </td>
                  <td>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn secondary">Sửa</a>
                    <form method="POST" action="{{ route('admin.users.toggleStatus', $user->id) }}" style="display:inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn secondary" onclick="return confirm('Bạn chắc chắn?')">
                        {{ $user->status === 'active' ? 'Khóa' : 'Mở khóa' }}
                      </button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" style="display:inline;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn danger" onclick="return confirm('Xóa vĩnh viễn?')">Xóa</button>
                    </form>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
             {{-- <ul class="pagination">
              {{$users->links()}}   
            </ul> --}}
          </div>
            
  </section>
    <footer class="foot">© 2025 AdminHub · Giao diện mẫu</footer>

</div>
@endsection 