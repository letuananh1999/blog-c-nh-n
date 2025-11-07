<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Đăng nhập — AdminHub</title>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}"/>
</head>
<body>
  
  <div class="login-container">
    <!-- Left Panel: Illustration -->
    <div class="login-left">
      <div class="left-content">
        <div class="brand-section">
          <div class="brand-icon">
            <i class='bx bxs-smile'></i>
          </div>
          <h1>AdminHub</h1>
        </div>

        <div class="illustration">
          <div class="illustration-card card-1">
            <i class='bx bx-user-check'></i> 
            <p>Quản lý dễ dàng</p>
          </div>
          <div class="illustration-card ">
            <i class='bx bx-shield-alt-2'></i> 
            <p> An toàn & Bảo mật</p>
          </div>
          <div class="illustration-card ">
            <i class='bx bx-pie-chart'></i> 
            <p>Thống kê chính xác</p>
          </div>
        </div>

        <div class="testimonial">
          <p class="testimonial-text">
            "AdminHub giúp tôi quản lý blog hiệu quả hơn. Giao diện thân thiện và dễ sử dụng!"
          </p>
          <div class="testimonial-author">
            <div class="author-avatar">LT</div>
            <div>
              <p class="author-name">Lê Tuấn</p>
              <p class="author-role">Web Developer</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Panel: Form -->
    <div class="login-right">
      <div class="form-wrapper">
        <div class="form-header">
          <h2>Đăng nhập</h2>
          <p>Quay lại cộng đồng AdminHub</p>
        </div>

        <form method="POST" action="{{route('admin.login')}}" id="login-form" class="login-form" novalidate>
          <!-- Email Field -->
          {{-- @csrf --}}
          <div class="form-group">
            <label for="email">
              <i class='bx bxs-envelope'></i> Email *
            </label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              placeholder="Nhập email của bạn"
              value="{{old('email')}}"
              required
            />
            <span class="form-error"></span>
          </div>

          <!-- Password Field -->
          <div class="form-group">
            <label for="password">
              <i class='bx bxs-lock'></i> Mật khẩu *
            </label>
            <div class="password-input-wrap">
              <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Nhập mật khẩu"
                required
                minlength="8"
              />
              <button type="button" class="toggle-password" data-target="password">
                <i class='bx bx-hide'></i>
              </button>
            </div>
            <span class="form-error"></span>
          </div>

          <!-- Remember & Forgot Password -->
          <div class="form-footer">
            <label class="remember-me">
              <input type="checkbox" id="remember" name="remember" />
              <span>Nhớ mật khẩu</span>
            </label>
            <a href="#" class="forgot-password">Quên mật khẩu?</a>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn-submit">
            <span>Đăng nhập</span>
            <i class='bx bx-chevron-right'></i>
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- <script src="{{ asset('js/auth/login.js') }}"></script> --}}
</body>
</html>
