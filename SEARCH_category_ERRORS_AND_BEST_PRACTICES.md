# Lỗi Search Category & Best Practices

## ❌ Lỗi bạn mắc phải

### 1. Route Ordering Lỗi (Đã sửa)
- **Vấn đề**: `Route::resource()` được đặt trước `Route::get('/search')`
- **Kết quả**: `/categories/search` match vào `{id}` của resource → **404**
- **Sửa lỗi**: Đặt search route **trước** resource route

```php
// ❌ SAI
Route::resource('categories', CategoryController::class);
Route::get('/categories/search', [CategoryController::class, 'search']);

// ✅ ĐÚNG
Route::get('/categories/search', [CategoryController::class, 'search']);
Route::resource('categories', CategoryController::class);
```

### 2. Search Không Có Pagination (Đã sửa)
- **Vấn đề**: `CategoryService::search()` trả `->get()` (không paginate)
- **Lỗi**: View gọi `$categories->links()` nhưng search() không return Paginator object
- **Sửa lỗi**: Thêm `paginate(10)` vào search method

```php
// ❌ SAI
public function search(string $query) {
    return Category::where('name', 'like', "%{$query}%")->get();
}

// ✅ ĐÚNG
public function search(string $query, $paginate = true) {
    $query = Category::where('name', 'like', "%{$query}%");
    return $paginate ? $query->paginate(10) : $query->get();
}
```

---

## ⚠️ Lỗi Phổ Biến Khi Làm Search Functionality

### 3. Route Conflicts
**Mô tả**: Resource routes tạo ra wildcard pattern `{id}` khiến route search bị match sai

```php
// ❌ SAI - Resource route match {id} trước
Route::resource('posts', PostController::class);
Route::get('/posts/search', [PostController::class, 'search']);
// Result: /posts/search → match /posts/{id} → 404

// ✅ ĐÚNG - Search route trước
Route::get('/posts/search', [PostController::class, 'search']);
Route::resource('posts', PostController::class);
```

**Lý do**: Laravel khớp route từ trên xuống dưới. Resource route có pattern `/posts/{id}` sẽ match `/posts/search` vì `search` được coi là `{id}`

---

### 4. Empty/Null Query Handling
**Mô tả**: Không kiểm tra input search rỗng → trả về tất cả dữ liệu

```php
// ❌ SAI - Không kiểm tra query rỗng
public function search(Request $request) {
    $query = $request->get('q');
    return Category::where('name', 'like', "%$query%")->get(); 
    // Query rỗng → tất cả kết quả
}

// ✅ ĐÚNG - Kiểm tra và validate
public function search(Request $request) {
    $query = $request->get('q', '');
    
    if (strlen($query) < 1) {
        return redirect()->route('admin.categories.index')
            ->with('info', 'Vui lòng nhập từ khóa tìm kiếm');
    }
    
    $categories = $this->categoryService->search($query);
    return view('admin.categories.index', compact('categories'));
}
```

---

### 5. Pagination Inconsistency
**Mô tả**: Index có pagination, Search không → gây lỗi khi gọi `->links()`

```php
// ❌ SAI - Index paginate, Search không paginate
// index() 
$posts = Post::paginate(10);

// search()
$posts = Post::where('title', 'like', "%$q%")->get(); 

// View gọi $posts->links() → error method not found

// ✅ ĐÚNG - Consistent pagination
public function search(string $query) {
    return Post::where('title', 'like', "%{$query}%")
        ->paginate(10);  // Same as index
}
```

---

### 6. SQL Injection Risk
**Mô tả**: Không escape special characters → có thể bị tấn công

```php
// ❌ SAI - String interpolation không an toàn
$query = $_GET['q'];
$sql = "SELECT * FROM categories WHERE name LIKE '%$query%'";
// User nhập: '; DROP TABLE users; -- → xóa table

// ✅ ĐÚNG - Parameterized queries (Laravel xử lý tự động)
where('name', 'like', "%{$query}%") // Laravel auto escape
```

**Cách hoạt động**: Laravel PDO binding tự động escape các ký tự đặc biệt

---

### 7. N+1 Query Problem
**Mô tả**: Loop qua results mà không eager load relationships → nhiều queries

```php
// ❌ SAI - Mỗi lần loop = 1 query
$categories = Category::where('name', 'like', "%$q%")->get();

foreach ($categories as $category) {
    echo $category->posts()->count(); // Query 1
    echo $category->user->name;        // Query 2
}
// Total: 1 + (N * 2) queries

// ✅ ĐÚNG - Eager loading
$categories = Category::where('name', 'like', "%$q%")
    ->withCount('posts')
    ->with('user')
    ->get();

foreach ($categories as $category) {
    echo $category->posts_count; // No query
    echo $category->user->name;  // No query
}
// Total: 1 query
```

---

### 8. Case Sensitivity Issues
**Mô tả**: MySQL mặc định case-insensitive nhưng có thể gây inconsistency

```php
// ❌ SAI - Không kiểm soát case sensitivity
where('name', 'like', "%{$query}%")
// "Laravel" vs "laravel" đều match

// ✅ ĐÚNG - Rõ ràng nếu cần case-sensitive
where('name', 'like', "%{$query}%") // Default: case-insensitive
// Hoặc
whereRaw("BINARY `name` LIKE ?", ["%{$query}%"]) // Case-sensitive
```

---

### 9. Missing CSRF Token
**Mô tả**: Form POST quên token → 419 error

```html
<!-- ❌ SAI - Quên CSRF token (nếu dùng POST) -->
<form method="POST" action="/search">
    <input name="q" />
</form>

<!-- ✅ ĐÚNG - Thêm CSRF token -->
<form method="POST" action="/search">
    @csrf
    <input name="q" />
</form>

<!-- Note: GET method không bắt buộc nhưng tốt -->
<form method="GET" action="/search">
    <!-- GET không cần CSRF -->
    <input name="q" />
</form>
```

---

### 10. Special Characters & Wildcard Escaping
**Mô tả**: Ký tự `%` và `_` là wildcard trong SQL LIKE

```php
// ❌ SAI - User nhập "C++" → match tất cả vì "+" là wildcard
$query = "C++";
where('language', 'like', "%{$query}%")
// Match: "C++", "C1+", "Cpp", v.v.

// ✅ ĐÚNG - Escape special characters
$escaped = str_replace(['%', '_', '\\'], ['\\%', '\\_', '\\\\'], $query);
where('language', 'like', "%{$escaped}%", '\\')
// Match chính xác: "C++" only
```

---

### 11. No Results Handling
**Mô tả**: Không thông báo khi tìm kiếm không có kết quả

```php
// ❌ SAI - Trả blank page
$categories = $this->search($query);
return view('categories.index', compact('categories'));

// ✅ ĐÚNG - Thông báo rõ ràng
@if($categories->isEmpty())
    <div class="alert alert-info">
        Không tìm thấy danh mục nào phù hợp với "<strong>{{ request('q') }}</strong>"
    </div>
@else
    @foreach($categories as $cat)
        <article>{{ $cat->name }}</article>
    @endforeach
@endif
```

---

### 12. Missing "Back to List" Link
**Mô tả**: Người dùng không biết cách quay lại danh sách đầy đủ

```blade
<!-- ❌ SAI - Không có option quay lại -->
<h1>Kết quả tìm kiếm</h1>
@foreach($categories as $cat)
    ...
@endforeach

<!-- ✅ ĐÚNG - Thêm link quay lại -->
@if(request('q'))
    <div class="search-header">
        <h1>Kết quả tìm kiếm: "{{ request('q') }}"</h1>
        <a href="{{ route('admin.categories.index') }}">
            ← Quay lại danh sách
        </a>
    </div>
@else
    <h1>Danh sách danh mục</h1>
@endif
```

---

### 13. Performance - Missing Indexes
**Mô tả**: Database không có index → search chậm với dữ liệu lớn

```php
// ❌ SAI - Không tối ưu hóa query
where('name', 'like', "%{$query}%")
// Full table scan với 100k records → 5 giây

// ✅ ĐÚNG - Thêm index trong migration
Schema::table('categories', function (Blueprint $table) {
    $table->fullText('name', 'description'); // Full-text index
    // Hoặc
    $table->index('name'); // Regular index (prefix match)
});

// Query sẽ chạy dưới 100ms
```

---

### 14. Validation & Sanitization
**Mô tả**: Không validate input → có thể lỗi hoặc exploit

```php
// ❌ SAI - Input không validate
$query = $request->get('q');
$categories = Category::where('name', 'like', "%{$query}%")->get();

// ✅ ĐÚNG - Validate input
$validated = $request->validate([
    'q' => 'nullable|string|max:255',
]);

$query = $validated['q'] ?? '';
if (strlen($query) < 2) {
    return redirect()->route('categories.index')
        ->with('error', 'Tìm kiếm phải từ 2 ký tự trở lên');
}

$categories = Category::where('name', 'like', "%{$query}%")->paginate(10);
```

---

### 15. Search Analytics & Logging
**Mô tả**: Không ghi log tìm kiếm → không biết người dùng tìm cái gì

```php
// ❌ SAI - Không ghi lại search
public function search(Request $request) {
    $query = $request->get('q');
    return Category::where('name', 'like', "%{$query}%")->get();
}

// ✅ ĐÚNG - Ghi log + analytics
public function search(Request $request) {
    $query = $request->get('q');
    
    // Ghi log
    Log::info('Category search', [
        'query' => $query,
        'user_id' => Auth::id(),
        'timestamp' => now()
    ]);
    
    $categories = Category::where('name', 'like', "%{$query}%")->paginate(10);
    
    return view('categories.index', [
        'categories' => $categories,
        'searchQuery' => $query,
        'resultCount' => $categories->total()
    ]);
}
```

---

## 📋 Checklist cho Search Functionality

- [ ] Route search trước resource route
- [ ] Validate input search (không rỗng, max length)
- [ ] Escape special characters (%, _)
- [ ] Consistent pagination giữa index & search
- [ ] Eager load relationships (withCount, with)
- [ ] Thông báo "Không có kết quả"
- [ ] Back to list link
- [ ] CSRF token (nếu POST)
- [ ] Error handling & logging
- [ ] Database indexes cho columns tìm kiếm
- [ ] Performance test với large dataset
- [ ] Mobile responsive search form

---

## 🔍 Summary

**Lỗi chính bạn mắc phải:**
1. Route ordering → 404 error
2. Pagination inconsistency → method not found

**Lỗi phổ biến khác:**
- N+1 queries
- SQL injection risk (nếu không dùng Laravel query builder)
- Missing validation
- No error/empty state handling
- Performance issues (missing indexes)
