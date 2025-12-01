# 📝 HƯỚNG DẪN CHI TIẾT - LƯU BÀI VIẾT (POST SAVE)

## 🔴 5 LỖI TRONG CODE BAN ĐẦU

### ❌ LỖI #1: `Auth()->id = 1` - Cú Pháp SAI

**Code sai:**

```php
'user_id' => Auth()->id = 1,
```

**Vấn đề:**

-   `Auth()->id = 1` là **gán giá trị**, không phải đọc giá trị
-   Cú pháp sai, sẽ throw exception
-   Dù gán được thì cũng sai vì hardcode `1` thay vì user thực tế

**Code đúng:**

```php
'user_id' => Auth::id(),
```

**Giải thích:**

-   `Auth::id()` = Lấy ID user hiện tại đang đăng nhập
-   Nếu user đăng nhập với ID = 5, sẽ lưu `user_id = 5`
-   Nếu user chưa đăng nhập, sẽ return `null` (middleware sẽ block trước)

---

### ❌ LỖI #2: `view_count` và `like_count` - Không nên để user truyền

**Code sai:**

```php
'view_count' => $request->view_count ?? 0,
'like_count' => $request->like_count ?? 0,
```

**Vấn đề:**

-   User có thể gửi `view_count: 1000000` để fake stats
-   Những fields này phải do **backend quản lý**, không cho user tùy ý

**Code đúng:**

```php
'view_count' => 0,      // Bài mới luôn 0 view
'like_count' => 0,      // Bài mới luôn 0 like
```

**Giải thích:**

-   View count tăng khi user xem → Backend tính
-   Like count tăng khi user like → Backend tính
-   Không bao giờ để user set từ form

---

### ❌ LỖI #3: `published_at` - Không nên user truyền trực tiếp

**Code sai:**

```php
'published_at' => $request->published_at,
```

**Vấn đề:**

-   User có thể gửi `published_at: 2020-01-01` để fake đăng bài cũ
-   Backend phải quyết định khi nào publish

**Code đúng:**

```php
'published_at' => $request->status === 'published' ? now() : null,
```

**Giải thích:**

-   Nếu status = "published" → lưu thời gian hiện tại (tự động)
-   Nếu status = "draft" → để null (chưa công bố)
-   Backend tự động set, user không thể giả mạo

**Timeline:**

```
User gửi: status = "published"
  ↓
Backend: if (status === 'published') → published_at = now() (2024-12-01 10:30:45)
  ↓
Database: published_at = "2024-12-01 10:30:45"
```

---

### ❌ LỖI #4: Route name sai

**Code sai:**

```php
return redirect()->route('posts.index');          // ❌ 'posts.index' không tồn tại
```

**Vấn đề:**

-   Routes được define: `Route::resource('posts', PostController::class)`
-   Nằm trong prefix 'admin' → tên phải là 'admin.posts.index'

**Code đúng:**

```php
return redirect()->route('admin.posts.index');    // ✓ 'admin.posts.index'
```

**Resource Routes tự động sinh:**

```
GET    /admin/posts              → admin.posts.index
GET    /admin/posts/create       → admin.posts.create
POST   /admin/posts              → admin.posts.store
GET    /admin/posts/{id}         → admin.posts.show
GET    /admin/posts/{id}/edit    → admin.posts.edit
PUT    /admin/posts/{id}         → admin.posts.update
DELETE /admin/posts/{id}         → admin.posts.destroy
```

---

### ❌ LỖI #5: View path sai và field `is_published` sai

**Code sai:**

```php
// edit() method
return view('posts.edit', ...);              // ❌ Path sai

// update() method
'is_published' => $request->has('is_published'),  // ❌ Field sai
```

**Vấn đề:**

-   View path phải `admin.post.edit` (folder structure khớp)
-   Model Post dùng `status`, không phải `is_published`

**Code đúng:**

```php
// edit() method
return view('admin.post.edit', ...);         // ✓ Path đúng

// update() method
'status' => $request->status ?? 'draft',     // ✓ Field đúng
```

---

## 🎯 FLOW HOÀN CHỈNH - LƯU BÀI VIẾT

### 1️⃣ USER ĐIỀN FORM

```html
<form method="POST" action="/admin/posts">
    @csrf

    <input type="text" name="title" placeholder="Tiêu đề bài viết" />
    <textarea name="content">Nội dung bài viết</textarea>
    <textarea name="excerpt">Mô tả ngắn</textarea>

    <select name="category_id">
        <option value="1">Technology</option>
        <option value="2">Marketing</option>
    </select>

    <select name="tags[]" multiple>
        <option value="1">Laravel</option>
        <option value="2">PHP</option>
    </select>

    <input type="text" name="meta_title" />
    <textarea name="meta_description"></textarea>

    <select name="status">
        <option value="draft">Draft</option>
        <option value="published">Publish</option>
    </select>

    <button type="submit">Lưu</button>
</form>
```

### 2️⃣ FORM GỬI TỚI BACKEND

```
POST /admin/posts HTTP/1.1
Content-Type: application/x-www-form-urlencoded

title=Laravel Tips
content=Some content...
category_id=1
tags[]=1&tags[]=2
status=published
```

### 3️⃣ LARAVEL VALIDATE (StorePostRequest)

```php
// app/Http/Requests/StorePostRequest.php
class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();  // ✓ Phải đăng nhập
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:posts,title',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'status' => 'required|in:draft,published',
            'thumbnail' => 'nullable|image|max:5120',
        ];
    }
}
```

**Giải thích từng rule:**

-   `title` phải có, không trùng trong DB
-   `content` bắt buộc
-   `excerpt` tùy chọn, max 500 ký tự
-   `category_id` bắt buộc, phải tồn tại trong bảng categories
-   `tags` tùy chọn, phải là mảng, từng tag phải tồn tại
-   `status` phải là "draft" hoặc "published"

### 4️⃣ CONTROLLER LƯUDATA

```php
public function store(StorePostRequest $request)
{
    try {
        // ✓ $request->validated() đã qua validation
        $post = Post::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),        // Auto-generate từ title
            'excerpt' => $request->excerpt,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'user_id' => Auth::id(),                      // ✓ User hiện tại
            'thumbnail' => $request->thumbnail,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'status' => $request->status ?? 'draft',     // ✓ Default draft
            'view_count' => 0,                            // ✓ Luôn 0 (backend tính)
            'like_count' => 0,                            // ✓ Luôn 0 (backend tính)
            'published_at' => $request->status === 'published' ? now() : null,
        ]);

        // ✓ Attach tags nếu có
        if ($request->has('tags') && !empty($request->tags)) {
            $post->tags()->attach($request->tags);
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('success', '✓ Tạo bài viết thành công!');

    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->with('error', '❌ Lỗi: ' . $e->getMessage());
    }
}
```

### 5️⃣ DATABASE LƯUSỐ LIỆU

```
INSERT INTO posts (
    title, slug, excerpt, content, category_id, user_id,
    thumbnail, meta_title, meta_description, status,
    view_count, like_count, published_at, created_at, updated_at
) VALUES (
    'Laravel Tips', 'laravel-tips', '...', '...', 1, 5,
    'thumb.jpg', 'Best Laravel Tips', 'Learn Laravel', 'published',
    0, 0, '2024-12-01 10:30:45', '2024-12-01 10:30:45', '2024-12-01 10:30:45'
)
```

### 6️⃣ RELATIONSHIP - ATTACH TAGS

```php
// Bảng trung gian: post_tag
INSERT INTO post_tag (post_id, tag_id) VALUES (1, 1);
INSERT INTO post_tag (post_id, tag_id) VALUES (1, 2);
```

### 7️⃣ REDIRECT & FLASH MESSAGE

```
Redirect → /admin/posts
Header X-Flash: success = "✓ Tạo bài viết thành công!"

View render: admin.post.index
Flash message hiển thị
```

---

## 🔄 FLOW CẬP NHẬT BÀI VIẾT (UPDATE)

```php
public function update(StorePostRequest $request, $id)
{
    try {
        $post = Post::findOrFail($id);  // ✓ Tìm bài, 404 nếu không có

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'category_id' => $request->category_id,
            'slug' => Str::slug($request->title),        // ✓ Update slug
            'thumbnail' => $request->thumbnail,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'status' => $request->status ?? 'draft',
            // ✓ Nếu publish lần đầu, set published_at = now()
            // ✓ Nếu đã published, giữ nguyên thời gian cũ
            'published_at' => $request->status === 'published'
                ? ($post->published_at ?? now())
                : null,
        ]);

        if ($request->has('tags') && !empty($request->tags)) {
            $post->tags()->sync($request->tags);  // ✓ Sync = xóa cũ, thêm mới
        } else {
            $post->tags()->detach();               // ✓ Xóa tất cả tags
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('success', '✓ Cập nhật bài viết thành công!');

    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->with('error', '❌ Lỗi: ' . $e->getMessage());
    }
}
```

**Khác biệt với store():**

-   `update()` giữ `published_at` cũ nếu đã publish (`$post->published_at ?? now()`)
-   `store()` luôn set published_at mới
-   Tags dùng `sync()` thay vì `attach()` (xóa cũ trước khi thêm mới)

---

## 🗑️ FLOW XÓA BÀI VIẾT (DELETE)

```php
public function destroy(Post $post)
{
    try {
        $post->delete();  // ✓ Xóa bài và cascade relationships

        return response()->json([
            'message' => '✓ Xóa bài viết thành công!',
            'status' => true
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => '❌ Lỗi: ' . $e->getMessage(),
            'status' => false
        ], 500);
    }
}
```

---

## 📋 CHECKLIST - LƯU BÀI VIẾT

-   ✅ Auth::id() để lấy user ID
-   ✅ Không cho user set view_count, like_count
-   ✅ published_at tự động từ status
-   ✅ Route name: admin.posts.index
-   ✅ View path: admin.post.edit
-   ✅ Validate input qua StorePostRequest
-   ✅ Try-catch để handle errors
-   ✅ Tags dùng attach() khi tạo, sync() khi cập nhật
-   ✅ Slug auto-generate từ title
-   ✅ Flash message sau redirect

---

## 🧪 TEST CODE

```bash
# Terminal
php artisan tinker

# Tạo bài
>>> $post = \App\Models\Post::create([
    'title' => 'Test Post',
    'slug' => 'test-post',
    'content' => 'Content here',
    'category_id' => 1,
    'user_id' => 1,
    'status' => 'published',
    'published_at' => now()
])

>>> $post->id  // Xem ID bài vừa tạo

# Attach tags
>>> $post->tags()->attach([1, 2, 3])

# Kiểm tra dữ liệu
>>> $post->load('category', 'tags', 'user')
```
