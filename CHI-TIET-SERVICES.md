# 📦 GIẢI THÍCH CHI TIẾT SERVICES LAYER

## 🎯 Services Là Gì?

**Services** là một **Business Logic Layer** - nơi chứa tất cả logic xử lý dữ liệu & business rules của ứng dụng.

### 📊 Architecture Layer

```
┌─────────────────────────────────────┐
│   Routes (web.php, api.php)         │  ← Định tuyến URL
├─────────────────────────────────────┤
│   Controllers                       │  ← HTTP handling (Input/Output)
├─────────────────────────────────────┤
│   Services ⭐ (Business Logic)      │  ← Xử lý logic, data processing
├─────────────────────────────────────┤
│   Models                            │  ← Database interaction (ORM)
├─────────────────────────────────────┤
│   Database                          │  ← Data storage
└─────────────────────────────────────┘
```

---

## 📂 Project có 3 Services

```
app/Services/
├── ApiResponseService.php      (API Response handling)
├── PostService.php             (Post CRUD logic)
└── ImageService.php            (File upload handling)
```

---

## 1️⃣ ApiResponseService

### 📖 Mục Đích
Tạo **consistent JSON response format** cho tất cả API endpoints.

### 📍 Vị Trí
```
app/Services/ApiResponseService.php
```

### 💻 Chứa Gì?

| Method | Chức Năng | HTTP Code |
|--------|----------|-----------|
| `success($msg, $data)` | Response thành công | 200 |
| `error($msg, $data)` | Response lỗi chung | 500 |
| `unauthorized($msg)` | Không có quyền | 403 |
| `notFound($msg)` | Tài nguyên không tồn tại | 404 |
| `validationError($errors)` | Validation fail | 422 |
| `serverError($msg)` | Server error | 500 |

### 🔍 Chi Tiết

```php
class ApiResponseService
{
    public static function success(string $message, mixed $data = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data  // Optional
        ], $statusCode);
    }
    
    public static function unauthorized(string $message = '...'): JsonResponse
    {
        return self::error($message, null, 403);
    }
    
    // ... other methods
}
```

### 📝 Ví Dụ Sử Dụng

**Trong Controller:**
```php
// Success
return ApiResponseService::success('✓ Xóa thành công!');

// Error
return ApiResponseService::serverError('❌ Có lỗi xảy ra!');

// Unauthorized
return ApiResponseService::unauthorized('❌ Bạn không có quyền!');

// With data
return ApiResponseService::success('✓ Tạo thành công!', $post);
```

**Response JSON:**
```json
{
  "status": true,
  "message": "✓ Xóa thành công!",
  "data": null
}
```

### 🎯 Lợi Ích
✅ **Consistent Format** - Mọi API response giống nhau  
✅ **Reusable** - Dùng ở tất cả controllers  
✅ **DRY** - Không copy-paste code  
✅ **Easy to Change** - Sửa 1 chỗ, affect tất cả  

---

## 2️⃣ PostService

### 📖 Mục Đích
Xử lý **Business Logic của Post CRUD** - tất cả logic tạo/sửa/xóa bài viết.

### 📍 Vị Trí
```
app/Services/PostService.php (155 lines)
```

### 💻 Chứa Gì?

| Method | Chức Năng |
|--------|----------|
| `create(array $data)` | Tạo bài viết mới |
| `update(Post $post, array $data)` | Cập nhật bài viết |
| `delete(Post $post)` | Xóa bài viết |
| `prepareThumbnail($data, $post)` | Xử lý upload ảnh |
| `preparePostData($data, $post, $thumbnailData)` | Chuẩn bị dữ liệu |
| `deleteThumbnail($post)` | Xóa ảnh từ disk |
| `attachTags($post, $tagIds)` | Gắn tags cho bài mới |
| `syncTags($post, $tagIds)` | Cập nhật tags |

### 🔍 Chi Tiết Từng Method

#### `create(array $data): Post`

**Chức Năng:** Tạo bài viết mới với đủ dữ liệu

**Logic:**
```php
public function create(array $data): Post
{
    try {
        // Bước 1: Xử lý thumbnail (upload, lưu file)
        $postData = $this->prepareThumbnail($data);
        
        // Bước 2: Chuẩn bị dữ liệu bài viết
        $postData = $this->preparePostData($data, null, $postData);
        
        // Bước 3: Lưu vào database
        $post = Post::create($postData);
        
        // Bước 4: Gắn tags
        $this->attachTags($post, $data['tags'] ?? []);
        
        return $post;
    } catch (\Exception $e) {
        throw new \Exception('Failed to create post: ' . $e->getMessage());
    }
}
```

**Input:**
```php
[
    'title' => 'Hướng dẫn Laravel',
    'content' => '...',
    'category_id' => 1,
    'thumbnail' => UploadedFile,
    'status' => 'published',
    'tags' => [1, 2, 3]
]
```

**Output:** `Post model instance`

---

#### `update(Post $post, array $data): Post`

**Chức Năng:** Cập nhật bài viết hiện có

**Logic:**
```php
public function update(Post $post, array $data): Post
{
    try {
        // Xử lý thumbnail (nếu upload ảnh mới)
        $postData = $this->prepareThumbnail($data, $post);
        
        // Chuẩn bị dữ liệu (preserve published_at nếu đã set)
        $postData = $this->preparePostData($data, $post, $postData);
        
        // Update database
        $post->update($postData);
        
        // Sync tags (replace old tags)
        $this->syncTags($post, $data['tags'] ?? []);
        
        return $post;
    } catch (\Exception $e) {
        throw new \Exception('Failed to update post: ' . $e->getMessage());
    }
}
```

**Đặc Điểm:**
- ✅ Xóa ảnh cũ khi upload ảnh mới
- ✅ Preserve `published_at` nếu status vẫn là "published"
- ✅ Replace tags thay vì thêm mới

---

#### `delete(Post $post): bool`

**Chức Năng:** Xóa bài viết (cleanup file, tags)

**Logic:**
```php
public function delete(Post $post): bool
{
    try {
        // Xóa ảnh từ disk
        $this->deleteThumbnail($post);
        
        // Xóa tags relationship
        $post->tags()->detach();
        
        // Xóa bài viết từ database
        return $post->delete();
    } catch (\Exception $e) {
        throw new \Exception('Failed to delete post: ' . $e->getMessage());
    }
}
```

**Cleanup:**
- ✅ Xóa file ảnh khỏi disk
- ✅ Xóa tags relationship
- ✅ Xóa record từ database

---

#### `prepareThumbnail($data, $post = null): array`

**Chức Năng:** Xử lý upload/update ảnh

**Logic:**
```php
private function prepareThumbnail(array $data, ?Post $existingPost = null): array
{
    $thumbnailData = [];
    
    if (!empty($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
        // Nếu update: xóa ảnh cũ
        if ($existingPost && $existingPost->thumbnail) {
            $this->imageService->delete($existingPost->thumbnail);
        }
        
        // Save ảnh mới
        $thumbnailData['thumbnail'] = $this->imageService->save($data['thumbnail']);
    }
    
    return $thumbnailData;
}
```

**Workflow:**
```
Input: UploadedFile (image.jpg)
    ↓
Validate file (size, type)
    ↓
Generate unique filename (1733400000_abc123.jpg)
    ↓
Move to public/img/post/
    ↓
Return: '/img/post/1733400000_abc123.jpg'
```

---

#### `preparePostData($data, $post, $thumbnailData = []): array`

**Chức Năng:** Chuẩn bị dữ liệu bài viết

**Xử Lý:**
1. **Map dữ liệu** - title, slug, content, etc.
2. **Handle published_at** - Logic tùy status
3. **Initialize defaults** - user_id, views_count (only on create)

**Logic:**
```php
private function preparePostData(array $data, ?Post $existingPost = null, array $thumbnailData = []): array
{
    $postData = [
        'title' => $data['title'],
        'slug' => Str::slug($data['title']),
        'excerpt' => $data['excerpt'],
        'content' => $data['content'],
        'category_id' => $data['category_id'],
        'status' => $data['status'] ?? 'draft',
    ];
    
    // Add thumbnail if exists
    if (!empty($thumbnailData)) {
        $postData = array_merge($postData, $thumbnailData);
    }
    
    // Handle published_at based on status
    if ($data['status'] === 'published') {
        // On update: preserve existing date
        $postData['published_at'] = $existingPost?->published_at ?? now();
    } else {
        // Draft/Archived: set to null
        $postData['published_at'] = null;
    }
    
    // Only on creation
    if (!$existingPost) {
        $postData['user_id'] = Auth::id();
        $postData['views_count'] = 0;
        $postData['likes_count'] = 0;
    }
    
    return $postData;
}
```

**Đặc Điểm:**
- ✅ Auto-generate slug từ title
- ✅ published_at logic (set khi publish, null khi draft)
- ✅ Preserve published_at khi update
- ✅ Set user_id chỉ khi create

---

## 3️⃣ ImageService

### 📖 Mục Đích
Xử lý **File Upload & Deletion** - tất cả logic liên quan ảnh.

### 📍 Vị Trí
```
app/Services/ImageService.php (96 lines)
```

### 💻 Chứa Gì?

| Method | Chức Năng |
|--------|----------|
| `save(UploadedFile $file)` | Upload & lưu ảnh |
| `delete(string $filepath)` | Xóa ảnh từ disk |
| `validate(UploadedFile $file)` | Validate file |
| `generateFilename(UploadedFile $file)` | Tạo unique filename |

### 🔍 Chi Tiết Từng Method

#### `save(UploadedFile $file): string`

**Chức Năng:** Upload file và lưu vào disk

**Logic:**
```php
public function save(UploadedFile $file): string
{
    // Validate file
    $this->validate($file);
    
    // Generate unique filename
    $filename = $this->generateFilename($file);
    
    // Get destination path
    $path = public_path(config('blog.post.thumbnail.path'));
    
    // Create directory if needed
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
    
    // Move file to destination
    $file->move($path, $filename);
    
    // Return relative path
    return '/' . config('blog.post.thumbnail.path') . '/' . $filename;
}
```

**Flow:**
```
Input: UploadedFile (image.jpg, 500KB)
    ↓
Validate: Size OK? Type OK?
    ↓
Generate Filename: "1733400000_xyz789.jpg"
    ↓
Create Directory: public/img/post/
    ↓
Move File: /tmp/upload → /public/img/post/
    ↓
Return: "/img/post/1733400000_xyz789.jpg"
```

**Output:** Relative path to access later

---

#### `delete(string $filepath): bool`

**Chức Năng:** Xóa ảnh từ disk

**Logic:**
```php
public function delete(string $filepath): bool
{
    $fullPath = public_path($filepath);
    
    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);  // Delete file
    }
    
    return true;  // File doesn't exist = success
}
```

**Safety:**
- ✅ Check file tồn tại trước khi xóa
- ✅ Check là file thực sự (không directory)
- ✅ Return true nếu file không tồn tại (idempotent)

---

#### `validate(UploadedFile $file): void`

**Chức Năng:** Validate uploaded file

**Checks:**
- File size (max 5MB theo config)
- MIME type (jpeg, png, gif, webp)
- Extension validation

---

#### `generateFilename(UploadedFile $file): string`

**Chức Năng:** Tạo unique filename

**Format:** `{timestamp}_{random}.{ext}`

**Ví Dụ:**
```
Input: "my-photo.jpg"
Output: "1733400000_aB9cD2eF.jpg"
```

**Lợi Ích:**
- ✅ Unique - Tránh conflict
- ✅ Timestamp - Dễ track
- ✅ Preserve extension

---

## 🔄 FLOW HOÀN CHỈNH - Create Post

```
1. User submit form
    ↓
2. FormRequest validation
    ↓
3. PostController::store()
    └─ Call: PostService->create($data)
        ↓
        ├─ PostService::prepareThumbnail()
        │   └─ Call: ImageService->save($file)
        │       ├─ Validate file
        │       ├─ Generate filename
        │       ├─ Create directory
        │       ├─ Move file
        │       └─ Return: "/img/post/1733400000_abc.jpg"
        │
        ├─ PostService::preparePostData()
        │   ├─ Generate slug
        │   ├─ Set published_at = now() (if status='published')
        │   ├─ Set user_id = Auth::id()
        │   └─ Return: complete postData array
        │
        ├─ Post::create($postData)
        │   └─ Insert into database
        │
        └─ PostService::attachTags()
            └─ Attach tags to post
    ↓
4. Return success response (ApiResponseService)
    ↓
5. Frontend: Show success message & redirect
```

---

## 📊 Services vs Controllers

### ❌ Without Services (Messy)

```php
// PostController - 300 lines!
public function store(Request $request)
{
    // Validation
    $validated = $request->validate([...]);
    
    // Handle file upload
    if ($request->hasFile('thumbnail')) {
        $file = $request->file('thumbnail');
        $validate_file_size($file);
        $validate_mime_type($file);
        $filename = generate_unique_name($file);
        $file->move('public/img/post', $filename);
        $validated['thumbnail'] = '/img/post/' . $filename;
    }
    
    // Create post
    $validated['slug'] = Str::slug($validated['title']);
    $validated['user_id'] = Auth::id();
    $post = Post::create($validated);
    
    // Attach tags
    if ($request->has('tags')) {
        $post->tags()->attach($request->get('tags'));
    }
    
    return redirect()->route('posts.index');
}

// 100 lines, all mixed logic
```

### ✅ With Services (Clean)

```php
// PostController - 50 lines!
public function store(StorePostRequest $request)
{
    $post = $this->postService->create($request->validated());
    return redirect()->route('admin.posts.index')
        ->with('success', 'Created!');
}

// PostService - Clear separation
public function create(array $data): Post
{
    $postData = $this->prepareThumbnail($data);
    $postData = $this->preparePostData($data, null, $postData);
    $post = Post::create($postData);
    $this->attachTags($post, $data['tags'] ?? []);
    return $post;
}

// ImageService - File handling only
public function save(UploadedFile $file): string
{
    // Just handle files, nothing else
}
```

---

## 🎯 Tóm Tắt - Services Chức Năng

| Service | Responsibility | Methods |
|---------|----------------|---------|
| **ApiResponseService** | JSON response format | success(), error(), unauthorized() |
| **PostService** | Post CRUD logic | create(), update(), delete() |
| **ImageService** | File operations | save(), delete(), validate() |

---

## 🏗️ SOLID Principles

| Principle | Implementation |
|-----------|-----------------|
| **S** - Single Responsibility | Mỗi service 1 mục đích |
| **O** - Open/Closed | Dễ extend mà không modify cũ |
| **L** - Liskov Substitution | Service interfaces consistent |
| **I** - Interface Segregation | Services nhỏ, không chung chung |
| **D** - Dependency Inversion | Inject services vào controller |

---

## 💡 Benefits

✅ **Testability** - Dễ mock services khi test  
✅ **Reusability** - Services dùng ở nhiều controllers  
✅ **Maintainability** - Code tổ chức rõ ràng  
✅ **Scalability** - Dễ thêm tính năng mới  
✅ **Professional** - Industry best practice  

**Services Layer là core của professional Laravel architecture!** 🚀
