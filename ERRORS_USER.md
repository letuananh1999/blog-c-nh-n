# 📋 TỔNG HỢP LỖI VÀ HƯỚNG KHẮC PHỤC - USER MANAGEMENT

## 🔴 LỖI 1: Column `status` không tìm thấy

**Triệu chứng:** `SQLSTATE[42S22]: Unknown column 'status' in 'field list'`

**Nguyên nhân gốc:**

-   Migration chưa chạy hoặc chạy nhưng các column không được thêm vào
-   Database schema không được cập nhật

**Hướng khắc phục:**

```bash
# Bước 1: Kiểm tra migration đã tồn tại chưa
php artisan migrate:status

# Bước 2: Chạy migration
php artisan migrate

# Bước 3: Kiểm tra structure bảng
php artisan tinker
>>> DB::select('DESCRIBE users')
```

**Học được:**

-   ✅ Luôn chạy `php artisan migrate` sau khi tạo migration
-   ✅ Kiểm tra database structure bằng tinker hoặc phpMyAdmin
-   ✅ Không bao giờ giả định column đã tồn tại

**Mô tả code:**

```php
// ĐÚNG: Tạo migration khi thêm column mới
php artisan make:migration add_avatar_role_status_to_users_table

// File migration:
Schema::table('users', function (Blueprint $table) {
    $table->string('avatar')->nullable()->after('password');
    $table->enum('role', ['User', 'Editor', 'Admin'])->default('User');
    $table->enum('status', ['active', 'blocked'])->default('active');
});

// Rồi chạy migrate
php artisan migrate
```

---

## 🔴 LỖI 2: Enum Role Value Không Khớp

**Triệu chứng:** `{"message":"Unauthorized - Insufficient permissions"}`
Route: `checkrole:admin` nhưng database lưu `Admin` (viết hoa)

**Nguyên nhân gốc:**

-   Route parameter: `checkrole:admin` → lowercase
-   Database: role = `Admin` → uppercase
-   So sánh: `'Admin' in ['admin']` → **FALSE**

**Hướng khắc phục:**

```php
// SAIT: So sánh trực tiếp
if (! in_array(Auth::user()->role, $roles)) {
    return response()->json(['message' => 'Unauthorized'], 403);
}

// ĐÚNG: So sánh không phân biệt chữ hoa/thường
$userRole = strtolower(Auth::user()->role);
$roles = array_map('strtolower', $roles);

if (! in_array($userRole, $roles)) {
    return response()->json(['message' => 'Unauthorized'], 403);
}
```

**Học được:**

-   ✅ Luôn normalize data trước khi so sánh
-   ✅ Enum trong database nên dùng lowercase (user, editor, admin)
-   ✅ Khi so sánh, luôn convert về cùng format

**Mô tả code:**

```php
// Cách 1: Enum định nghĩa lowercase (RECOMMENDED)
$table->enum('role', ['user', 'editor', 'admin'])->default('user');

// Route có thể dùng bất kỳ format nào
Route::middleware(['auth', 'checkrole:user,editor,admin'])

// Middleware tự động normalize
$userRole = strtolower(Auth::user()->role);
$roles = array_map('strtolower', $roles); // ['user', 'editor', 'admin']
```

---

## 🔴 LỖI 3: Data Truncated for Column Role

**Triệu chứng:** `Data truncated for column 'role' at row 1`
Enum chỉ có ('user', 'admin') nhưng code gửi 'User', 'Editor', 'Admin'

**Nguyên nhân gốc:**

-   Enum values hạn chế: chỉ chấp nhận giá trị cụ thể
-   Code gửi giá trị không nằm trong enum list
-   Database tự động truncate hoặc lỗi

**Hướng khắc phục:**

```php
// SAIT: Enum giá trị không match với form
$table->enum('role', ['user', 'admin'])->default('user'); // Database
// Form gửi: 'User', 'Editor', 'Admin' → ❌ LỖI

// ĐÚNG: Enum phải khớp với form
$table->enum('role', ['User', 'Editor', 'Admin'])->default('User');
// Form gửi: 'User', 'Editor', 'Admin' → ✅ OK

// Hoặc convert cùng format
// Database: ['user', 'editor', 'admin']
// Form gửi: strtolower($request->role) → ['user', 'editor', 'admin']
```

**Học được:**

-   ✅ Enum values phải khớp với dữ liệu form
-   ✅ Validate trước khi insert: `in:User,Editor,Admin`
-   ✅ Kiểm tra enum values trong tinker: `DB::select('DESCRIBE users')`

**Mô tả code:**

```php
// Controller validation
$validated = $request->validate([
    'role' => 'required|in:User,Editor,Admin', // ✅ Validate đầu vào
]);

// Database enum phải match
$table->enum('role', ['User', 'Editor', 'Admin'])->default('User');

// Hoặc nếu dùng lowercase enum
$table->enum('role', ['user', 'editor', 'admin'])->default('user');
// Thì trong controller phải convert
'role' => strtolower($validated['role'])
```

---

## 🔴 LỖI 4: File Upload Avatar Không Lưu

**Triệu chứng:** Avatar = null, không có file trong `public/img/user/`

**Nguyên nhân gốc:**

-   Thư mục `public/img/user/` không tồn tại
-   Permission thư mục không cho phép write
-   Validation avatar nhưng không xử lý trong service

**Hướng khắc phục:**

```bash
# Bước 1: Tạo thư mục
mkdir -p public/img/user

# Bước 2: Set permission (Linux/Mac)
chmod 755 public/img/user

# Bước 3: Kiểm tra thư mục tồn tại
php artisan tinker
>>> file_exists(public_path('img/user'))
```

**Học được:**

-   ✅ Luôn tạo thư mục trước khi upload
-   ✅ Kiểm tra permission thư mục
-   ✅ Validate file type trước upload: `image|mimes:jpeg,png,jpg,gif|max:2048`

**Mô tả code:**

```php
// Controller: Validate file
$validated = $request->validate([
    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // ✅ Validate
]);

// Service: Xử lý upload
private function saveAvatar($file): string {
    try {
        // Kiểm tra thư mục tồn tại
        if (!is_dir(public_path('img/user'))) {
            mkdir(public_path('img/user'), 0755, true);
        }

        $filename = 'user_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('img/user'), $filename);

        return $filename;
    } catch (\Exception $e) {
        throw new \Exception('Lỗi lưu ảnh: ' . $e->getMessage());
    }
}

// Database
protected $fillable = ['avatar', ...]; // ✅ Thêm avatar vào fillable
```

---

## 🔴 LỖI 5: Email_Verified_At Không Được Set

**Triệu chứng:** Email xác minh nhưng `email_verified_at` vẫn null

**Nguyên nhân gốc:**

-   Form không có input cho `email_verified_at`
-   Controller không validate/xử lý checkbox
-   Service không set timestamp

**Hướng khắc phục:**

```php
// SAIT: Không xử lý checkbox
// Form: <input type="checkbox" name="email_verified" />
// Controller: Không lấy dữ liệu
// Service: Không set email_verified_at

// ĐÚNG: Xử lý toàn bộ chuỗi
// 1. Form
<input type="checkbox" name="email_verified" value="1" />

// 2. Controller validation
$validated = $request->validate([
    'email_verified' => 'nullable|boolean', // ✅ Validate checkbox
]);

// 3. Service xử lý
if (!empty($data['email_verified'])) {
    $userData['email_verified_at'] = now(); // ✅ Set timestamp hiện tại
}

// 4. Model casts
protected function casts(): array {
    return [
        'email_verified_at' => 'datetime', // ✅ Cast về datetime
    ];
}
```

**Học được:**

-   ✅ Checkbox HTML mặc định không gửi giá trị nếu không check
-   ✅ Luôn validate checkbox: `nullable|boolean`
-   ✅ Lúc set, dùng `now()` hoặc `Carbon::now()`

**Mô tả code:**

```php
// Form
<label>
    <input type="checkbox" name="email_verified" value="1" />
    Email đã xác minh
</label>

// Controller
$validated = $request->validate([
    'email_verified' => 'nullable|boolean',
]);

// Service
$userData = [
    'email' => $data['email'],
    // ...
];

if (!empty($data['email_verified'])) {
    $userData['email_verified_at'] = now();
} else {
    $userData['email_verified_at'] = null;
}

$user = User::create($userData);
```

---

## 🔴 LỖI 6: Remember Token Không Hoạt động

**Triệu chứ:** Đăng nhập → check "Nhớ mật khẩu" → Logout → Mở lại vẫn phải đăng nhập

**Nguyên nhân gốc:**

-   AuthController `Auth::attempt()` không truyền parameter `$remember`
-   Role check sai: `'role' => 'admin'` thay vì `'Admin'`

**Hướng khắc phục:**

```php
// SAIT: Không xử lý remember
if (Auth::attempt([
    'email' => $request->email,
    'password' => $request->password,
    'role' => 'admin' // ❌ lowercase, database là 'Admin'
])) { // ❌ Không có parameter remember
    return redirect()->route('admin.index');
}

// ĐÚNG: Xử lý remember + role đúng
$remember = $request->filled('remember'); // Lấy checkbox

if (Auth::attempt([
    'email' => $request->email,
    'password' => $request->password,
    'role' => 'Admin' // ✅ Match database
], $remember)) { // ✅ Truyền $remember parameter
    $request->session()->regenerate();
    return redirect()->route('admin.index');
}
```

**Học được:**

-   ✅ `Auth::attempt($credentials, $remember)` - parameter thứ 2 xử lý Remember Me
-   ✅ Role check phải match database value
-   ✅ Checkbox có attribute `name="remember"` → `$request->filled('remember')`

**Mô tả code:**

```php
// Form
<label>
    <input type="checkbox" name="remember" />
    Nhớ mật khẩu
</label>

// Controller
public function login(LoginRequest $request)
{
    $remember = $request->filled('remember'); // true/false

    if (Auth::attempt([
        'email' => $request->email,
        'password' => $request->password,
        'role' => 'Admin' // Match database
    ], $remember)) { // Pass remember parameter
        $request->session()->regenerate();
        return redirect()->route('admin.index');
    }

    return back()->withErrors(['email' => 'Sai thông tin!']);
}
```

---

## 🔴 LỖI 7: Middleware CheckRole Không Hoạt động

**Triệu chứ:** Truy cập /admin/users nhưng hiển thị JSON error thay vì redirect

**Nguyên nhân gốc:**

-   Middleware trả về JSON response thay vì redirect
-   Role check sai format (chữ hoa/thường)

**Hướng khắc phục:**

```php
// SAIT: Không check null auth + so sánh sai
public function handle($request, Closure $next, ...$roles)
{
    if (! in_array(Auth::user()->role, $roles)) { // ❌ Nếu user null → ERROR
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    return $next($request);
}

// ĐÚNG: Check auth + normalize + friendly error
public function handle($request, Closure $next, ...$roles)
{
    // ✅ Kiểm tra user đã đăng nhập
    if (!Auth::check()) {
        return response()->json(['message' => 'Unauthorized - Not logged in'], 401);
    }

    // ✅ Normalize role (không phân biệt chữ hoa/thường)
    $userRole = strtolower(Auth::user()->role);
    $roles = array_map('strtolower', $roles);

    if (! in_array($userRole, $roles)) {
        return response()->json([
            'message' => 'Unauthorized - Your role: ' . Auth::user()->role
        ], 403);
    }

    return $next($request);
}
```

**Học được:**

-   ✅ Luôn check `Auth::check()` trước khi truy cập `Auth::user()`
-   ✅ Normalize data trước khi so sánh
-   ✅ Return message chi tiết để debug

**Mô tả code:**

```php
// Middleware chuẩn
class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        // 1. Kiểm tra đăng nhập
        if (!Auth::check()) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        // 2. Normalize data
        $userRole = strtolower(Auth::user()->role);
        $roles = array_map('strtolower', $roles);

        // 3. Kiểm tra quyền
        if (!in_array($userRole, $roles)) {
            return response()->json(['message' => 'Insufficient permissions'], 403);
        }

        // 4. Cho phép request tiếp tục
        return $next($request);
    }
}
```

---

## 🔴 LỖI 8: Avatar Được Upload Nhưng Không Hiển Thị

**Triệu chứ:** Avatar file có trong `public/img/user/` nhưng view không hiển thị

**Nguyên nhân gốc:**

-   Path sai: dùng `asset('img/user/' . $user->avatar)` nhưng file storage ở khác
-   Avatar column null
-   Kiểm tra file existence sai

**Hướng khắc phục:**

```blade
// SAIT: Không check file tồn tại
<img src="{{ asset('img/user/' . $user->avatar) }}" />

// ĐÚNG: Kiểm tra file + fallback
@if ($user->avatar && file_exists(public_path('img/user/' . $user->avatar)))
    <img src="{{ asset('img/user/' . $user->avatar) }}" alt="{{ $user->name }}">
@else
    <div class="avatar-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
@endif
```

**Học được:**

-   ✅ Luôn check file tồn tại trước hiển thị
-   ✅ Có fallback avatar (placeholder/default image)
-   ✅ Kiểm tra column không null

**Mô tả code:**

```blade
{{-- CHÍNH XÁC --}}
@if ($user->avatar && file_exists(public_path('img/user/' . $user->avatar)))
    {{-- Avatar thực --}}
    <img src="{{ asset('img/user/' . $user->avatar) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; object-fit: cover;">
@elseif ($user->avatar)
    {{-- Avatar tham chiếu nhưng file bị xóa --}}
    <div class="avatar-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
@else
    {{-- Avatar null → dùng placeholder --}}
    <div class="avatar-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
@endif

{{-- Hoặc rút gọn --}}
@if ($user->avatar && file_exists(public_path('img/user/' . $user->avatar)))
    <img src="{{ asset('img/user/' . $user->avatar) }}" alt="{{ $user->name }}">
@else
    <div>{{ strtoupper(substr($user->name, 0, 1)) }}</div>
@endif
```

---

## 🟡 LỖI 9: Form Không Hiển Thị Lỗi Validation

**Triệu chứ:** Submit form với dữ liệu sai nhưng không thấy lỗi

**Nguyên nhân gốc:**

-   Layout dashboard không include flash message component
-   View không có `@error()` directive
-   Validation lỗi nhưng controller không return back

**Hướng khắc phục:**

```blade
// SAIT: Không hiển thị lỗi
@extends('layouts.dashboard')
@section('content')
    <form method="POST" action="/admin/users">
        @csrf
        <input name="email" />
        {{-- Không có @error('email') --}}
        <button>Submit</button>
    </form>
@endsection

// ĐÚNG: Hiển thị lỗi + layout
@extends('layouts.dashboard')
@section('content')
    <form method="POST" action="/admin/users">
        @csrf

        {{-- Input + Error message --}}
        <input name="email" class="@error('email') is-invalid @enderror" />
        @error('email')
            <span class="error">{{ $message }}</span>
        @enderror

        <button>Submit</button>
    </form>
@endsection

// Dashboard layout
<section id="content">
    @include('components.head')
    <main>
        @include('components.flash_message') {{-- ✅ Thêm flash message --}}
        @yield('content')
    </main>
</section>
```

**Học được:**

-   ✅ Luôn include flash message component trong layout
-   ✅ Thêm `@error()` directive cho mỗi input
-   ✅ Controller return `back()->withInput()` nếu validation lỗi

**Mô tả code:**

```php
// Controller
public function store(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
    ]);

    // Nếu validation lỗi, Laravel tự động return back()
    // với $errors và old input

    User::create($validated);
    return redirect()->with('success', 'Tạo user thành công');
}

// Blade
<form method="POST">
    @csrf
    <input name="email" value="{{ old('email') }}" /> {{-- Giữ old value --}}
    @error('email')
        <span style="color: red;">{{ $message }}</span>
    @enderror
</form>
```

---

## 📚 NGUYÊN TẮC TRÁNH LỖI (Best Practices)

### 1. **Luôn Check & Validate Data**

```php
// ❌ SAIT
if ($user->role == 'admin') { }

// ✅ ĐÚNG
if (!Auth::check()) {
    return unauthorized();
}
if (strtolower($user->role) === 'admin') { }
```

### 2. **Normalize Dữ liệu Trước Xử lý**

```php
// ❌ SAIT
if (in_array($role, $roles)) { } // Có thể fail nếu format khác

// ✅ ĐÚNG
$userRole = strtolower($role);
$roles = array_map('strtolower', $roles);
if (in_array($userRole, $roles)) { }
```

### 3. **Luôn Validate Input**

```php
// ❌ SAIT
User::create($request->all()); // Tất cả dữ liệu không được validate

// ✅ ĐÚNG
$validated = $request->validate([
    'email' => 'required|email|unique:users',
    'role' => 'required|in:User,Editor,Admin',
    'avatar' => 'nullable|image|max:2048',
]);
User::create($validated);
```

### 4. **Kiểm Tra File/Thư Mục Trước Thao Tác**

```php
// ❌ SAIT
$file->move(public_path('img/user'), $filename); // Thư mục chưa tồn tại

// ✅ ĐÚNG
if (!is_dir(public_path('img/user'))) {
    mkdir(public_path('img/user'), 0755, true);
}
$file->move(public_path('img/user'), $filename);
```

### 5. **Luôn Xử Lý Exception**

```php
// ❌ SAIT
$this->saveAvatar($file);

// ✅ ĐÚNG
try {
    $this->saveAvatar($file);
} catch (\Exception $e) {
    Log::error('Upload failed', ['error' => $e->getMessage()]);
    return back()->with('error', 'Lỗi upload: ' . $e->getMessage());
}
```

### 6. **Database Enum Nên Dùng Lowercase**

```sql
-- ❌ SAIT: Enum với chữ hoa
ENUM('User', 'Admin', 'Editor')

-- ✅ ĐÚNG: Enum lowercase, code xử lý format
ENUM('user', 'admin', 'editor')

-- Khi xử lý ở code
$role = strtolower($role); // Normalize về lowercase
```

### 7. **Luôn Include Flash Message Component**

```blade
<!-- Layout -->
<section id="content">
    @include('components.head')
    <main>
        @include('components.flash_message') <!-- ✅ KHÔNG QUÊN -->
        @yield('content')
    </main>
</section>
```

### 8. **Checkbox Phải Có Name Attribute**

```blade
<!-- ❌ SAIT -->
<input type="checkbox" /> Nhớ mật khẩu

<!-- ✅ ĐÚNG -->
<input type="checkbox" name="remember" value="1" /> Nhớ mật khẩu

<!-- Controller -->
$remember = $request->filled('remember'); // true/false
```

---

## 🎯 WORKFLOW CHUẨN KHI TẠO FEATURE THÊM/SỬA/XÓA

```
1. TẠO MIGRATION
   └─ Thêm columns cần thiết
   └─ Define enum/constraints đúng
   └─ php artisan migrate

2. CẬP NHẬT MODEL
   └─ Thêm $fillable
   └─ Thêm $casts nếu cần
   └─ Thêm relationship

3. TẠO SERVICE CLASS
   └─ Tách business logic ra khỏi Controller
   └─ Xử lý file upload/delete
   └─ Try-catch + logging

4. UPDATE CONTROLLER
   └─ Validate input đầy đủ
   └─ Gọi service
   └─ Return response/redirect

5. TẠO/UPDATE VIEW
   └─ Form fields đủ
   └─ @error() cho mỗi field
   └─ Checkbox/select đúng format

6. SETUP ROUTES
   └─ Route resource
   └─ Route custom (toggle, bulk delete, etc)
   └─ Middleware auth + role

7. TEST TOÀN BỘ
   └─ Validation lỗi
   └─ Upload file
   └─ Role check
   └─ Delete cascade
```

---

## ✅ CHECKLIST TRƯỚC KHI COMMIT

-   [ ] Migration chạy thành công
-   [ ] Model fillable + casts đúng
-   [ ] Controller validate đầy đủ
-   [ ] Service xử lý exception
-   [ ] View hiển thị error message
-   [ ] Role check middleware
-   [ ] Avatar/file upload được test
-   [ ] Flash message hiển thị
-   [ ] Remember me hoạt động
-   [ ] Enum values match database
-   [ ] Đã test create/update/delete
