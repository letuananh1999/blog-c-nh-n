# 📝 HƯỚNG DẪN: COMMENTS - user_id & parent_id

## 📋 Cấu Trúc Bảng Comments

```
┌──────────────────────────────────────────┐
│           TABLE: comments                 │
├──────────────────────────────────────────┤
│ id           │ int (AUTO INCREMENT)      │ ← Khóa chính, tự tăng
│ post_id      │ int (FK → posts.id)       │ ← Bài viết nào
│ user_id      │ int (FK → users.id)       │ ← User nào đó bình luận
│ author_name  │ varchar(255)              │ ← Tên người bình luận
│ author_email │ varchar(255)              │ ← Email người bình luận
│ content      │ text                      │ ← Nội dung bình luận
│ parent_id    │ int (FK → comments.id)    │ ← Trả lời comment nào (null nếu comment gốc)
│ created_at   │ timestamp                 │ ← Thời gian tạo
│ updated_at   │ timestamp                 │ ← Thời gian sửa
└──────────────────────────────────────────┘
```

---

## 🎯 SCENARIO: User Bình Luận Ở Bài Viết

### Tình Huống Thực Tế

```
👤 User: Tuấn Anh (id: 5)
  ↓
📄 Bài viết: "Hướng dẫn Laravel" (post_id: 10)
  ↓
💬 Bình luận: "Nội dung rất hữu ích!" (comment_id: 1)
  ├─ Trả lời comment #1: "Đồng ý!" (comment_id: 2, parent_id: 1)
  └─ Trả lời comment #1: "Thanks!" (comment_id: 3, parent_id: 1)
```

---

## 🔍 CÁCH LẤY user_id & parent_id

### 1️⃣ **user_id - Lấy Từ Session Authentication**

**user_id** được lấy từ **user đang đăng nhập** (session/token), không phải từ form input.

#### ✅ CÁCH ĐÚNG:

```php
// Controller: CommentsController.php
public function store(Request $request, Post $post)
{
    $validated = $request->validate([
        'content' => 'required|string|max:1000',
        'parent_id' => 'nullable|integer|exists:comments,id', // Nếu trả lời comment
    ]);

    // ✅ Lấy user_id từ authentication (user đang đăng nhập)
    $userId = Auth::id(); // Hoặc Auth::user()->id

    // Tạo comment
    Comment::create([
        'post_id' => $post->id,
        'user_id' => $userId, // ← Lấy từ Auth, không phải từ form
        'author_name' => Auth::user()->name, // ← Lấy tên user từ DB
        'author_email' => Auth::user()->email, // ← Lấy email user từ DB
        'content' => $validated['content'],
        'parent_id' => $validated['parent_id'] ?? null, // ← Từ form (nếu có)
    ]);

    return back()->with('success', 'Bình luận thành công');
}
```

**Lưu vào Database:**

```
comment_id: 1
post_id: 10
user_id: 5           ← Lấy từ Auth::id() (user đang đăng nhập)
author_name: Tuấn Anh
author_email: tuan@example.com
content: Nội dung rất hữu ích!
parent_id: null      ← null vì là comment gốc
```

---

### 2️⃣ **parent_id - Lấy Từ URL/Form**

**parent_id** được lấy từ:

-   **URL parameter**: `/posts/10/comments/1/replies` → parent_id = 1
-   **Form input**: `<input name="parent_id" value="1" />`
-   **Query parameter**: `?reply_to=1` → parent_id = 1

#### ✅ CÁCH ĐÚNG - KỊCH BẢN TRẢ LỜI COMMENT:

**BƯỚC 1: Hiển thị form bình luận (view)**

```blade
<!-- resources/views/posts/show.blade.php -->

<!-- Bình luận gốc -->
@foreach($post->comments()->whereNull('parent_id')->get() as $comment)
    <div class="comment" id="comment-{{ $comment->id }}">
        <h5>{{ $comment->author_name }}</h5>
        <p>{{ $comment->content }}</p>

        <!-- Nút trả lời -->
        <button onclick="toggleReplyForm({{ $comment->id }})">Trả lời</button>

        <!-- Form trả lời (ẩn mặc định) -->
        <form id="reply-form-{{ $comment->id }}" style="display:none;" method="POST" action="/posts/{{ $post->id }}/comments">
            @csrf
            <textarea name="content" placeholder="Trả lời..."></textarea>
            <!-- ✅ Truyền parent_id qua hidden input -->
            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
            <button type="submit">Gửi trả lời</button>
        </form>

        <!-- Hiển thị các trả lời -->
        @foreach($comment->replies as $reply)
            <div class="reply" style="margin-left: 20px;">
                <h6>{{ $reply->author_name }}</h6>
                <p>{{ $reply->content }}</p>
            </div>
        @endforeach
    </div>
@endforeach

<script>
function toggleReplyForm(commentId) {
    const form = document.getElementById('reply-form-' + commentId);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>
```

**BƯỚC 2: Controller xử lý form**

```php
// app/Http/Controllers/CommentController.php
public function store(Request $request, Post $post)
{
    // Validate input
    $validated = $request->validate([
        'content' => 'required|string|max:1000',
        'parent_id' => 'nullable|integer|exists:comments,id', // ← Validate parent_id
    ]);

    // Nếu không có user_id từ auth → lấy từ form (guest comment)
    if (Auth::check()) {
        $data = [
            'post_id' => $post->id,
            'user_id' => Auth::id(), // ← User đã đăng nhập
            'author_name' => Auth::user()->name,
            'author_email' => Auth::user()->email,
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null, // ← Lấy từ form
        ];
    } else {
        // Nếu là guest, bắt nhập author_name & author_email
        $validated = $request->validate([
            'author_name' => 'required|string|max:255',
            'author_email' => 'required|email',
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ]);

        $data = [
            'post_id' => $post->id,
            'user_id' => null, // ← Guest comment
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'],
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ];
    }

    Comment::create($data);
    return back()->with('success', 'Bình luận thành công');
}
```

**Lưu vào Database (Trả lời comment #1):**

```
comment_id: 2
post_id: 10
user_id: 5           ← Nếu đã đăng nhập
author_name: Tuấn Anh
author_email: tuan@example.com
content: Đồng ý!
parent_id: 1         ← ← ← Lấy từ <input name="parent_id" value="1" />
```

---

## 📊 FLOW TOÀN BỘ

```
┌─────────────────────────────────────────────────────────────────┐
│ USER BÌNH LUẬN BÀI VIẾT                                         │
└─────────────────────────────────────────────────────────────────┘

1️⃣ USER TRUY CẬP BÀI VIẾT
   ↓
   GET /posts/10
   ↓
   View: posts.show
   ├─ Hiển thị bài viết (title, content)
   └─ Hiển thị form bình luận
      └─ <input name="content" />
      └─ <input type="hidden" name="parent_id" value="" />
      └─ <button>Gửi bình luận</button>

2️⃣ USER NHẬP BÌNH LUẬN
   ↓
   Form:
   {
     "content": "Nội dung rất hay!",
     "parent_id": null  ← Null vì là comment gốc
   }

3️⃣ USER SUBMIT FORM
   ↓
   POST /posts/10/comments
   Body:
   {
     "_token": "xxx",
     "content": "Nội dung rất hay!",
     "parent_id": null
   }

4️⃣ CONTROLLER XỨNG LÝ
   ↓
   public function store(Request $request, Post $post)
   {
       $userId = Auth::id();        // ← Lấy từ session: 5
       $content = $request->content; // ← Lấy từ form: "Nội dung rất hay!"
       $parentId = $request->parent_id ?? null; // ← Lấy từ form: null

       Comment::create([
           'post_id' => 10,
           'user_id' => 5,          // ← Từ Auth
           'author_name' => 'Tuấn Anh',
           'author_email' => 'tuan@example.com',
           'content' => 'Nội dung rất hay!',
           'parent_id' => null      // ← Từ form
       ]);
   }

5️⃣ SAVE VÀO DATABASE
   ↓
   INSERT INTO comments
   (post_id, user_id, author_name, author_email, content, parent_id, created_at, updated_at)
   VALUES
   (10, 5, 'Tuấn Anh', 'tuan@example.com', 'Nội dung rất hay!', null, '2025-12-12 10:00:00', '2025-12-12 10:00:00')

6️⃣ RELOAD PAGE - HIỂN THỊ COMMENT
   ↓
   GET /posts/10
   ↓
   SELECT * FROM comments WHERE post_id = 10
   ↓
   Hiển thị comment mới
```

---

## 💬 VÍ DỤ THỰC TÍNH: TRẢ LỜI COMMENT

### User 5 Trả Lời Comment 1:

**BƯỚC 1: Form Trả Lời**

```blade
<!-- Ở bên dưới comment #1 -->
<form method="POST" action="/posts/10/comments">
    @csrf
    <textarea name="content" placeholder="Trả lời..."></textarea>

    <!-- ✅ Hidden input chứa parent_id -->
    <input type="hidden" name="parent_id" value="1">

    <button type="submit">Trả lời</button>
</form>
```

**BƯỚC 2: Submit Form**

```
POST /posts/10/comments
Body:
{
  "_token": "abc123",
  "content": "Cảm ơn bạn!",
  "parent_id": "1"  ← ← ← Lấy từ hidden input
}
```

**BƯỚC 3: Controller**

```php
public function store(Request $request, Post $post)
{
    $validated = $request->validate([
        'content' => 'required|max:1000',
        'parent_id' => 'nullable|exists:comments,id', // ✅ Validate
    ]);

    Comment::create([
        'post_id' => $post->id,      // 10
        'user_id' => Auth::id(),      // 5
        'author_name' => Auth::user()->name, // Tuấn Anh
        'author_email' => Auth::user()->email, // tuan@example.com
        'content' => $validated['content'], // Cảm ơn bạn!
        'parent_id' => $validated['parent_id'], // 1 ← ← ← Từ form
    ]);
}
```

**BƯỚC 4: Database (Comment mới)**

```
id: 2
post_id: 10
user_id: 5
author_name: Tuấn Anh
author_email: tuan@example.com
content: Cảm ơn bạn!
parent_id: 1 ← ← ← Trả lời comment #1
created_at: 2025-12-12 10:15:00
```

---

## 🔗 RELATIONSHIPS - LẤY DỮ LIỆU

### Model: Comment

```php
// app/Models/Comment.php
class Comment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'author_name', 'author_email', 'content', 'parent_id'];

    // Quan hệ: Comment thuộc Post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Quan hệ: Comment thuộc User
    public function user()
    {
        return $this->belongsTo(User::class)->nullable();
    }

    // Quan hệ: Comment cha
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // Quan hệ: Các comment con (trả lời)
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
```

### Lấy dữ liệu:

```php
// Lấy tất cả bình luận của bài viết
$comments = Post::find(10)->comments;

// Lấy chỉ comment gốc (không phải trả lời)
$rootComments = Post::find(10)->comments()->whereNull('parent_id')->get();

// Lấy user bình luận
$comment = Comment::find(1);
$user = $comment->user; // Hoặc null nếu guest

// Lấy các trả lời của comment
$comment = Comment::find(1);
$replies = $comment->replies; // Lấy tất cả trả lời

// Lấy comment cha
$reply = Comment::find(2);
$parentComment = $reply->parent; // Comment #1
```

---

## 📌 SUMMARIZE - ĐÂU LẤY user_id & parent_id

| Cột              | Nguồn Lấy           | Cách Lấy                                            | Ví Dụ                                    |
| ---------------- | ------------------- | --------------------------------------------------- | ---------------------------------------- |
| **user_id**      | Authentication      | `Auth::id()`                                        | User đang đăng nhập: 5                   |
| **parent_id**    | Form (hidden input) | `$request->parent_id`                               | User click "Trả lời" → form auto fill: 1 |
| **post_id**      | URL Parameter       | `$post->id` hoặc route binding                      | `/posts/10/comments` → post_id = 10      |
| **author_name**  | User DB / Form      | `Auth::user()->name` hoặc `$request->author_name`   | "Tuấn Anh"                               |
| **author_email** | User DB / Form      | `Auth::user()->email` hoặc `$request->author_email` | "tuan@example.com"                       |
| **content**      | Form                | `$request->content`                                 | "Nội dung bình luận"                     |

---

## ⚠️ LỖI THƯỜNG GẶP & CÁCH TRÁNH

### ❌ LỖI 1: Cho User Nhập user_id

```php
// ❌ SAI: Cho form input user_id
<input name="user_id" /> ← Độc hại! User có thể nhập user_id người khác

// ✅ ĐÚNG: Lấy từ authentication
$userId = Auth::id(); // Không lấy từ form
```

### ❌ LỖI 2: Quên Validate parent_id

```php
// ❌ SAI: Không validate parent_id
Comment::create([
    'parent_id' => $request->parent_id, // Có thể nhập parent_id không tồn tại
]);

// ✅ ĐÚNG: Validate parent_id
$validated = $request->validate([
    'parent_id' => 'nullable|exists:comments,id', // ← Kiểm tra tồn tại
]);
```

### ❌ LỖI 3: parent_id Không Khớp post_id

```php
// ❌ SAI: User A reply comment ở post 10, nhưng comment ở post 20
POST /posts/10/comments
{
  "parent_id": 5 // Comment này ở post 20, không ở post 10!
}

// ✅ ĐÚNG: Validate parent_id phải thuộc cùng post
$validated = $request->validate([
    'parent_id' => 'nullable|integer',
]);

if ($validated['parent_id']) {
    $parentComment = Comment::find($validated['parent_id']);
    if ($parentComment->post_id !== $post->id) {
        return response()->json(['error' => 'Parent comment không thuộc bài viết này'], 403);
    }
}
```

### ❌ LỖI 4: User đã delete nhưng comment còn tham chiếu

```sql
-- ❌ SAI: Nếu user delete, comment mồ côi
user_id: 5 → User 5 xóa
comment mồ côi

-- ✅ ĐÚNG: Thiết lập cascade hoặc set null
$table->foreignId('user_id')
    ->nullable()
    ->constrained()
    ->cascadeOnDelete(); // ← Xóa user = xóa comment của user
    // Hoặc
    // ->nullOnDelete(); // ← Xóa user = set user_id = null
```

---

## 🛠️ MIGRATION ĐẦY ĐỦ (RECOMMENDED)

```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();

    // Foreign Keys
    $table->foreignId('post_id')
        ->constrained()
        ->cascadeOnDelete(); // Xóa post = xóa all comments

    $table->foreignId('user_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete(); // Xóa user = set user_id = null (giữ comment)

    // Thông tin tác giả
    $table->string('author_name'); // Nếu guest
    $table->string('author_email'); // Nếu guest

    // Nội dung
    $table->text('content');

    // Comment cha (nested comment)
    $table->foreignId('parent_id')
        ->nullable()
        ->constrained('comments')
        ->cascadeOnDelete(); // Xóa parent = xóa replies

    // Timestamps
    $table->timestamps();

    // Index
    $table->index('post_id');
    $table->index('user_id');
    $table->index('parent_id');
});
```

---

## 📝 CONTROLLER HOÀN CHỈNH

```php
// app/Http/Controllers/CommentController.php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Lưu bình luận mới
     */
    public function store(Request $request, Post $post)
    {
        // Validate input
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id', // ← Validate
        ]);

        // Nếu có parent_id, kiểm tra nó thuộc cùng post
        if ($validated['parent_id']) {
            $parentComment = Comment::find($validated['parent_id']);
            if ($parentComment->post_id !== $post->id) {
                return back()->withErrors(['parent_id' => 'Comment này không thuộc bài viết']);
            }
        }

        try {
            if (Auth::check()) {
                // User đã đăng nhập
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => Auth::id(), // ← Lấy từ auth
                    'author_name' => Auth::user()->name,
                    'author_email' => Auth::user()->email,
                    'content' => $validated['content'],
                    'parent_id' => $validated['parent_id'] ?? null, // ← Lấy từ form
                ]);
            } else {
                // Guest comment - cần thêm validation
                $guest = $request->validate([
                    'author_name' => 'required|string|max:255',
                    'author_email' => 'required|email',
                ]);

                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => null,
                    'author_name' => $guest['author_name'],
                    'author_email' => $guest['author_email'],
                    'content' => $validated['content'],
                    'parent_id' => $validated['parent_id'] ?? null,
                ]);
            }

            return back()->with('success', 'Bình luận thành công');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Xóa bình luận (hoặc soft delete)
     */
    public function destroy(Comment $comment)
    {
        // Kiểm tra quyền
        if (Auth::id() !== $comment->user_id && !Auth::user()?->isAdmin()) {
            return back()->withErrors(['error' => 'Không có quyền xóa']);
        }

        $comment->delete(); // ← Xóa comment này cũng xóa tất cả replies
        return back()->with('success', 'Xóa bình luận thành công');
    }
}
```

---

## 🎓 KẾT LUẬN

```
┌─────────────────────────────────────────────────────────┐
│ user_id và parent_id được LẤY ở ĐÂU:                    │
├─────────────────────────────────────────────────────────┤
│                                                          │
│ user_id → Auth::id()         (từ session)               │
│           ↓                                              │
│           Lấy từ user đang đăng nhập                    │
│           Không bao giờ lấy từ form                     │
│                                                          │
│ parent_id → $request->parent_id (từ form hidden input)  │
│             ↓                                            │
│             Người dùng click "Trả lời"                  │
│             Form tự động fill parent_id                │
│             Cần validate: exists:comments,id            │
│                                                          │
│ post_id → route binding hoặc URL                        │
│           /posts/10/comments → post_id = 10            │
│                                                          │
└─────────────────────────────────────────────────────────┘
```
