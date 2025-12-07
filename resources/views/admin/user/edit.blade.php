@extends('layouts.dashboard')
@section('title', 'Edit User')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/create.css') }}">
@endpush
@section('content')
    <div class="container-form">
        <div class="form-card">
            <div class="form-head">
                <div class="form-blog-header">
                    <h1><i class='bx bx-pen'></i> Chỉnh sửa người dùng</h1>
                    <p>Cập nhật thông tin người dùng.</p>
                </div>
            </div>
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <!-- avatar column -->
                    <div class="avatar-card">
                        <div class="avatar-preview" id="avatarPreview">
                            @if ($user->avatar && file_exists(public_path('img/user/' . $user->avatar)))
                                <img src="{{ asset('img/user/' . $user->avatar) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                            @else
                                <div class="avatar-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            @endif
                        </div>
                        <label class="file-input-label" for="u-avatar"><i class='bx bx-image'></i> Thay ảnh</label>
                        <input type="file" accept="image/*" id="u-avatar" name="avatar" class="file-input" />
                        <div class="avatar-actions">
                            <button type="button" class="btn secondary" id="remove-avatar">Xóa</button>
                        </div>
                        @error('avatar')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="fields-col">
                        <div class="form-group">
                            <label for="u-name">Tên</label>
                            <input id="u-name" name="name" class="input @error('name') is-invalid @enderror" 
                                   type="text" value="{{ old('name', $user->name) }}" required />
                            @error('name')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="u-email">Email</label>
                            <input id="u-email" name="email" class="input @error('email') is-invalid @enderror" 
                                   type="email" value="{{ old('email', $user->email) }}" required />
                            @error('email')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="u-password">Mật khẩu mới (để trống nếu không đổi)</label>
                            <div class="password-row">
                                <input id="u-password" name="password" class="input @error('password') is-invalid @enderror" 
                                       type="password" placeholder="Nhập mật khẩu mới..." />
                                <button type="button" class="pw-toggle" id="pw-toggle" aria-label="Hiện/ẩn mật khẩu"><i class='bx bx-low-vision'></i></button>
                            </div>
                            @error('password')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="u-role">Vai trò</label>
                            <select id="u-role" name="role" class="input @error('role') is-invalid @enderror">
                                <option value="User" {{ old('role', $user->role) === 'User' ? 'selected' : '' }}>User</option>
                                <option value="Editor" {{ old('role', $user->role) === 'Editor' ? 'selected' : '' }}>Editor</option>
                                <option value="Admin" {{ old('role', $user->role) === 'Admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="u-email-verified" name="email_verified" value="1" 
                                       {{ old('email_verified', $user->email_verified_at ? 1 : 0) ? 'checked' : '' }} />
                                <span style="margin-left: 8px;">✓ Email đã được xác minh</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <a href="{{ route('admin.users.index') }}" class="btn secondary">Quay lại</a>
                    <button type="submit" class="btn primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/user/form-create.js') }}"></script>
    <script>
        // Toggle password visibility
        const pwToggle = document.getElementById('pw-toggle');
        const pwInput = document.getElementById('u-password');
        
        if (pwToggle && pwInput) {
            pwToggle.addEventListener('click', function(e) {
                e.preventDefault();
                pwInput.type = pwInput.type === 'password' ? 'text' : 'password';
            });
        }
    </script>
@endpush
