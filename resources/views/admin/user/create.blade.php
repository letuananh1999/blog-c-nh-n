@extends('layouts.dashboard')
@section('title', 'User Detail')
@push('styles')
		<link rel="stylesheet" href="{{ asset('css/user/create.css') }}">
@endpush
@section('content')
    	<div class="container-form">
				<div class="form-card">
					<div class="form-head">
						<div>
							<h2>Thêm người dùng mới</h2>
							<p>Điền đầy đủ thông tin người dùng sau đó nhấn Lưu để thêm vào hệ thống.</p>
						</div>
					</div>
					<form id="add-user-form">
						<div class="form-grid">
							<!-- avatar column -->
							<div class="avatar-card">
								<div class="avatar-preview" id="avatarPreview">
									<div class="avatar-placeholder">Ảnh đại diện<br><small>(png, jpg)</small></div>
								</div>
								<label class="file-input-label" for="u-avatar"><i class='bx bx-image'></i> Chọn ảnh</label>
								<input type="file" accept="image/*" id="u-avatar" class="file-input">
								<div class="avatar-actions">
									<button type="button" class="btn secondary" id="remove-avatar">Xóa</button>
								</div>
							</div>

							<div class="fields-col">
								<div class="form-group">
									<label for="u-name">Tên</label>
									<input id="u-name" name="name" class="input" type="text" placeholder="Họ và tên" required />
								</div>
								<div class="form-group">
									<label for="u-email">Email</label>
									<input id="u-email" name="email" class="input" type="email" placeholder="email@domain.com" required />
								</div>
								<div class="form-group">
									<label for="u-password">Mật khẩu</label>
									<div class="password-row">
										<input id="u-password" name="password" class="input" type="password" placeholder="Mật khẩu" required />
										<button type="button" class="pw-toggle" id="pw-toggle" aria-label="Hiện/ẩn mật khẩu"><i class='bx bx-low-vision'></i></button>
									</div>
								</div>
								<div class="form-group">
									<label for="u-role">Vai trò</label>
									<select id="u-role" name="role" class="input">
										<option value="User">User</option>
										<option value="Editor">Editor</option>
										<option value="Admin">Admin</option>
									</select>
								</div>
							</div>
						</div>
						<div class="form-actions">
							<button type="button" class="btn secondary" id="cancel">Hủy</button>
							<button type="submit" class="btn primary">Lưu</button>
						</div>
					</form>
				</div>
			</div>
@endsection

@push('scripts')
		<script src="{{ asset('js/user/form-create.js') }}"></script>
@endpush
