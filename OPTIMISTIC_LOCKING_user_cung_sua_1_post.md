# 🔒 Optimistic Locking - Hướng dẫn cập nhật View Forms

# tình huống khi có 2 user cùng lúc sửa 1 bài viết(danh mục, user,comment ) thì sẽ được thông báo như thế nào

## Tóm tắt

Để sử dụng Optimistic Locking, mỗi form edit cần:

1. Gửi `version` cũ như hidden input
2. Xử lý lỗi version mismatch từ server

---

## 📝 Form Edit Pattern (Chung cho tất cả)

```blade
<form method="POST" action="{{ route('resource.update', $resource->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <!-- 🔑 BẮTBUỘC: Gửi version cũ -->
    <input type="hidden" name="version" value="{{ $resource->version }}">

    <!-- Các field khác -->
    <input type="text" name="title" value="{{ $resource->title }}">

    <button type="submit">Cập nhật</button>
</form>
```

---

## 🎯 Triển khai cho từng Resource

### 1️⃣ **Post Edit** (Sửa bài viết)

**File:** `resources/views/admin/post/edit.blade.php`

```blade
<input type="hidden" name="version" value="{{ $post->version }}">
```

### 2️⃣ **Category Edit** (Sửa danh mục)

**File:** `resources/views/admin/category/edit.blade.php`

```blade
<input type="hidden" name="version" value="{{ $category->version }}">
```

### 3️⃣ **User Edit** (Sửa người dùng)

**File:** `resources/views/admin/user/edit.blade.php`

```blade
<input type="hidden" name="version" value="{{ $user->version }}">
```

### 4️⃣ **Comment Edit** (Sửa bình luận - nếu có)

**File:** `resources/views/admin/comment/edit.blade.php`

```blade
<input type="hidden" name="version" value="{{ $comment->version }}">
```

---

## 🚨 Xử lý Lỗi Version Mismatch

### Server Response (409 Conflict):

```json
{
    "message": "Bài viết này đã được sửa bởi ai đó. Vui lòng tải lại trang!",
    "status": false
}
```

### Blade Template Flash Message:

```blade
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>⚠️ Lỗi:</strong>
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        <strong>⚠️ {{ session('error') }}</strong>
    </div>
@endif
```

---

## 🔄 JavaScript Handling (Optional - cho AJAX)

```javascript
// Fetch API example
async function updatePost(postId, formData) {
    try {
        const response = await fetch(`/admin/posts/${postId}`, {
            method: "PUT",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
            },
        });

        const data = await response.json();

        if (response.status === 409) {
            // Version conflict
            alert("⚠️ " + data.message + " Vui lòng tải lại trang");
            location.reload();
        } else if (!response.ok) {
            alert("❌ Lỗi: " + data.message);
        } else {
            alert("✓ Cập nhật thành công!");
        }
    } catch (error) {
        alert("❌ Lỗi: " + error.message);
    }
}
```

---

## ✅ Checklist Triển khai

-   [ ] Migration chạy: `php artisan migrate`
-   [ ] Model có `version` trong `$fillable`
-   [ ] Service/Controller check version
-   [ ] Validation rule có `version`
-   [ ] Edit form có `<input type="hidden" name="version">`
-   [ ] Error message hiển thị khi version mismatch

---

## 🧪 Test Case

```
Tình huống: 2 user edit cùng bài viết
1. User A: Mở bài viết (version = 0)
2. User B: Mở bài viết (version = 0)
3. User A: Save → version tăng thành 1 ✓
4. User B: Save → Check version (0 != 1) → Lỗi! ✓
   Thông báo: "Bài viết này đã được sửa bởi ai đó..."
```

---

## 📚 Tổng hợp Thay đổi

| Resource       | File                             | Thay đổi                  |
| -------------- | -------------------------------- | ------------------------- |
| **Post**       | PostService.update()             | Kiểm tra version          |
| **Category**   | CategoryController.update()      | Kiểm tra version          |
| **User**       | UserService.update()             | Kiểm tra version          |
| **Comment**    | CommentService.update()          | Kiểm tra version          |
| **All Models** | Post, Category, User, Comment    | Thêm version vào fillable |
| **All Views**  | edit.blade.php của từng resource | Thêm hidden input version |

<!-- Edit Post Form Example - app/resources/views/admin/post/edit.blade.php -->

@extends('layouts.dashboard')
@section('title', 'Sửa bài viết')

@section('content')

<div class="container">
    <h1>Sửa bài viết</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.posts.update', $post->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- 🔑 QUAN TRỌNG: Gửi version cũ -->
        <input type="hidden" name="version" value="{{ $post->version }}">

        <div class="form-group mb-3">
            <label for="title">Tiêu đề:</label>
            <input type="text" name="title" id="title" class="form-control"
                   value="{{ old('title', $post->title) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="content">Nội dung:</label>
            <textarea name="content" id="content" class="form-control" rows="10" required>{{ old('content', $post->content) }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label for="category_id">Danh mục:</label>
            <select name="category_id" id="category_id" class="form-control" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                            {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="status">Trạng thái:</label>
            <select name="status" id="status" class="form-control" required>
                <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Published</option>
                <option value="archived" {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>Archived</option>
            </select>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>

</div>
@endsection
