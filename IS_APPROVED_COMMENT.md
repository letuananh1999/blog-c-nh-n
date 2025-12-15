# 📌 GIẢI THÍCH: is_approved & Service Layer

## 1️⃣ CỘT is_approved DÙNG LÀM GÌ?

### Khái Niệm

```
is_approved = Boolean (true/false)
  ├─ true  → Bình luận đã được admin duyệt → Hiển thị trên website public
  └─ false → Bình luận chờ admin duyệt → KHÔNG hiển thị trên website public
```

### Workflow Thực Tế

```
┌─────────────────────────────────────────────────────────┐
│ USER BÌNH LUẬN Ở WEBSITE PUBLIC                         │
└─────────────────────────────────────────────────────────┘
          ↓
┌─────────────────────────────────────────────────────────┐
│ BẦN LUẬN LƯU VÀO DATABASE VỚI is_approved = FALSE       │
│ (Chờ admin duyệt, KHÔNG hiển thị)                       │
└─────────────────────────────────────────────────────────┘
          ↓
┌─────────────────────────────────────────────────────────┐
│ ADMIN ĐẶT VÀO ADMIN PANEL /admin/comments               │
│ • Xem danh sách bình luận chờ duyệt                     │
│ • Click nút "Duyệt" (Approve)                           │
└─────────────────────────────────────────────────────────┘
          ↓
┌─────────────────────────────────────────────────────────┐
│ is_approved = TRUE                                      │
│ Bình luận được HIỂN THỊ trên website public             │
└─────────────────────────────────────────────────────────┘
```

### Ví Dụ Database

```sql
-- Bình luận mới (chờ duyệt)
INSERT INTO comments VALUES (
  id: 1,
  post_id: 5,
  user_id: 10,
  author_name: "Tuấn Anh",
  content: "Bài viết hay quá!",
  is_approved: false  ← ← ← CHƯA được duyệt
);

-- ADMIN DUYỆT
UPDATE comments SET is_approved = true WHERE id = 1;

-- Bình luận này bây giờ sẽ hiển thị trên website
```

### Code Hiển Thị (Website Public)

```php
// public/show.blade.php - Hiển thị bài viết
$comments = $post->comments()
    ->where('is_approved', true) // ← ← ← CHỈ hiển thị approved
    ->where('parent_id', null)   // ← Chỉ comment gốc
    ->orderBy('created_at', 'desc')
    ->get();

@foreach($comments as $comment)
    <div class="comment">
        <p>{{ $comment->author_name }}</p>
        <p>{{ $comment->content }}</p>

        {{-- Hiển thị replies --}}
        @foreach($comment->children as $reply)
            <div class="reply">{{ $reply->content }}</div>
        @endforeach
    </div>
@endforeach
```

### Code Admin Panel

```php
// admin/comment/index.blade.php
// Hiển thị TẤT CẢ bình luận (approved + pending)
@foreach($comments as $comment)
    <article class="comment-card">
        <!-- Badge hiển thị trạng thái -->
        <span class="{{ $comment->is_approved ? 'approved' : 'pending' }}">
            {{ $comment->is_approved ? '✓ Đã duyệt' : '⏳ Chờ duyệt' }}
        </span>

        <!-- Nút duyệt/bỏ duyệt -->
        @if(!$comment->is_approved)
            <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST">
                @csrf @method('PATCH')
                <button>✓ Duyệt</button> ← Click → is_approved = true
            </form>
        @endif
    </article>
@endforeach
```

---

## 2️⃣ TẠI SAO KHÔNG TÁCH SERVICE?

### Lý Do Thực Tế:

#### ✅ **Khi Nào Nên Dùng Service Layer:**

```
Nếu logic PHỨC TẠP:
  ├─ File upload
  ├─ Xóa file liên quan
  ├─ Cập nhật multiple tables
  ├─ Call external API
  ├─ Tính toán phức tạp
  └─ Logging/Audit chi tiết
```

#### ❌ **Khi Nào KHÔNG Cần Service Layer:**

```
Nếu logic ĐƠN GIẢN:
  ├─ CRUD cơ bản (create, read, update, delete)
  ├─ Chỉ cập nhật 1-2 cột
  ├─ Validation đơn giản
  └─ Không có tác dụng phụ (side effects)
```

### So Sánh: Comment vs User

#### 👤 USER (Cần Service)

```
Tại sao cần:
  • Upload avatar ← File handling
  • Xóa avatar cũ ← File deletion
  • Hash password ← Encryption
  • Validate unique email ← Business rule
  • Set role = 'Admin'|'User'|'Editor' ← Enum logic
  • Toggle status active/blocked ← State management

→ UserService xử lý tất cả điều này
```

#### 💬 COMMENT (Không cần Service)

```
Không cần vì:
  • KHÔNG có file upload/delete
  • KHÔNG cần hash data
  • KHÔNG có validation phức tạp
  • KHÔNG có enum phức tạp
  • Chỉ cần: create, update, delete, approve
  • Chỉ update 1-2 cột (content, is_approved)

→ Controller xử lý trực tiếp là đủ
```

---

## 🎯 CHI TIẾT: Comment Controller Hiện Tại

### Mỗi Method Làm Gì:

```php
// 1️⃣ INDEX - Hiển thị danh sách
public function index(Request $request)
{
    // Lấy comments từ DB
    $query = Comment::with(['post', 'user'])
        ->orderBy('created_at', 'desc');

    // Filter theo status
    if ($request->status === 'approved') {
        $query->where('is_approved', true);
    } elseif ($request->status === 'pending') {
        $query->where('is_approved', false);
    }

    // Tìm kiếm
    if ($request->search) {
        $query->where('content', 'like', '%' . $request->search . '%');
    }

    $comments = $query->paginate(15);

    // Thống kê
    $stats = [
        'total' => Comment::count(),
        'approved' => Comment::where('is_approved', true)->count(),
        'pending' => Comment::where('is_approved', false)->count(),
    ];

    return view('admin.comment.index', compact('comments', 'stats'));
}
```

### ✅ SỬA SAI: KHÔNG PHẢI tất cả logic trong method là "bẩn"

```php
// CÓ thể rút ra Service nếu:
// 1. Logic trở nên phức tạp (50+ dòng)
// 2. Cần reuse ở nhiều Controller
// 3. Có file handling hoặc API calls

// TỪ CHỐI Service nếu:
// 1. Logic đơn giản (like above 15 dòng)
// 2. Chỉ dùng ở 1 Controller
// 3. CRUD cơ bản
// → Comment thỏa điều kiện từ chối → Không cần Service
```

---

## 📚 BEST PRACTICE: Khi Nào Dùng Service?

### Chỉ Số Phức Tạp (Complexity Score)

```
Service Layer NÊN khi:
  Score = 0️⃣ File IO          ← Upload/Delete files
         + 1️⃣ API calls       ← External services
         + 1️⃣ Multiple tables ← Update 3+ tables
         + 1️⃣ Complex logic   ← 50+ lines code
         + 1️⃣ Reusability     ← Dùng ở 2+ Controllers
         ────────────────────
         ≥ 3 = Nên dùng Service
```

### Comment Score

```
Comment:
  ✓ File IO:          0 (không upload)
  ✓ API calls:        0 (không gọi API)
  ✓ Multiple tables:   0 (chỉ update comments table)
  ✓ Complex logic:     0 (< 20 lines per method)
  ✓ Reusability:       0 (chỉ admin dùng)
  ────────────────────
  TOTAL = 0 ← Không cần Service!
```

### User Score

```
User:
  ✓ File IO:          1 (avatar upload)
  ✓ API calls:        0 (không gọi)
  ✓ Multiple tables:   1 (users + files)
  ✓ Complex logic:     1 (email unique + role + avatar)
  ✓ Reusability:       1 (admin + maybe user profile)
  ────────────────────
  TOTAL = 4 ← CÓ cần Service!
```

---

## 🔍 NẾU SAU NÀY COMMENT PHỨC TẠP HÓA?

### Khi Nào Cần Refactor Thành Service:

```php
// Ví dụ: Thêm tính năng "Spam detection"
public function store(StoreCommentRequest $request)
{
    // 🔴 Logic phức tạp bây giờ:

    // 1. Check spam
    if ($this->isSpam($request->content)) {
        $isApproved = false; // Tự động chừng pending
    } else {
        $isApproved = true; // Auto approve
    }

    // 2. Sanitize HTML
    $content = $this->sanitizeHTML($request->content);

    // 3. Send notification email
    if ($isApproved) {
        Mail::send(new CommentApprovedMail($comment));
    }

    // 4. Create comment
    Comment::create([...]);

    // 5. Log activity
    Log::info('Comment created with spam check', [...]);

    // → BAY GIỜ LOGIC PHỨC TẠP, NÊN TÁCH SERVICE
}
```

### Refactor Thành Service:

```php
// app/Services/CommentService.php
class CommentService
{
    public function create(array $data)
    {
        // Check spam
        $isApproved = !$this->isSpam($data['content']);

        // Sanitize
        $data['content'] = $this->sanitizeHTML($data['content']);
        $data['is_approved'] = $isApproved;

        // Create
        $comment = Comment::create($data);

        // Notify
        if ($isApproved) {
            Mail::send(new CommentApprovedMail($comment));
        }

        // Log
        Log::info('Comment created', ['id' => $comment->id]);

        return $comment;
    }

    private function isSpam($content): bool { ... }
    private function sanitizeHTML($content): string { ... }
}

// Controller bây giờ:
public function store(Request $request)
{
    $comment = $this->commentService->create($request->validated());
    return back()->with('success', 'Bình luận thành công');
}
```

---

## 📋 SUMMARY: KQĐ HIỆN TẠI

```
┌──────────────────────────────────────────────────────────┐
│ is_approved = boolean flag cho biết bình luận có được    │
│ admin duyệt hay chưa                                     │
│                                                          │
│ • false (default) → Chờ duyệt, không hiển thị           │
│ • true           → Đã duyệt, hiển thị trên website      │
└──────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────┐
│ Không dùng Service vì Comment logic ĐƠN GIẢN:            │
│                                                          │
│ • Không file upload (như User)                          │
│ • Không xóa file                                        │
│ • CRUD cơ bản thôi                                      │
│ • Controller xử lý trực tiếp là chuẩn nhất              │
│                                                          │
│ NẾU sau này logic phức tạp:                             │
│ • Add spam detection                                    │
│ • Add AI moderation                                     │
│ • Send notification email                              │
│ → BÂY GIỜ NÊN REFACTOR THÀNH SERVICE                    │
└──────────────────────────────────────────────────────────┘
```

---

## ✅ CHECKLIST HIỂU RÕ

-   [ ] Hiểu is_approved dùng để flag bình luận "đã duyệt" hay "chờ duyệt"
-   [ ] Hiểu false → không hiển thị, true → hiển thị
-   [ ] Hiểu Service Layer chỉ dùng khi logic phức tạp
-   [ ] Hiểu Comment controller hiện tại là chuẩn (đơn giản → không cần Service)
-   [ ] Biết khi nào refactor thành Service (nếu thêm spam, email, etc)
