# 📋 Version Field - Vấn Đề & Giải Pháp Chi Tiết

## 🎯 Vấn Đề Chính

Khi người dùng thêm một danh mục mới qua form, column `version` trong database bị set là `0` thay vì `1`, dẫn đến **không thể sửa** danh mục đó sau.

### ❌ Triệu Chứng
```
1. Click "Thêm danh mục" → Điền tên → Lưu ✅ Thành công
2. Click "Sửa" → Điền nội dung mới → Lưu ❌ Lỗi: "Danh mục này đã được sửa bởi ai đó"
```

---

## 🔍 Nguyên Nhân

### **1. Migration Định Nghĩa Default = 0**

File: `database/migrations/2025_12_17_000001_add_version_to_categories_table.php`

```php
Schema::table('categories', function (Blueprint $table) {
    $table->integer('version')->default(0)->after('updated_at');
});
```

**Kết quả:** Khi tạo category mới, nếu không chỉ định `version`, nó sẽ tự động là `0`.

---

### **2. Factory Thiết Lập Version = 1**

File: `database/factories/CategoryFactory.php`

```php
public function definition(): array
{
    return [
        'name' => ucfirst($name),
        'slug' => Str::slug($name),
        'description' => $this->faker->sentence(),
        'sort' => $this->faker->numberBetween(1, 100),
        'version' => 1,  // ← Thiết lập version = 1
    ];
}
```

**Kết quả:** Khi chạy seeder/factory, category sẽ có `version = 1`.

---

### **3. CategoryService.create() KHÔNG Thiết Lập Version**

File: `app/Services/CategoryService.php` (BAN ĐẦU)

```php
public function create(array $data): Category
{
    return Category::create([
        'name' => $data['name'],
        'description' => $data['description'] ?? null,
        // ❌ THIẾU: 'version' => 1,
    ]);
}
```

**Kết Quả:** Khi add category qua API/form:
- Migration default được dùng → `version = 0`
- Factory KHÔNG được dùng (factory chỉ dùng cho seeder)

---

## ⚡ Quy Trình Chạy Hiện Tại

### **Scenario A: Thêm Category Qua Form**
```
1. User click "Thêm danh mục"
2. Submit form (API: POST /admin/categories)
3. CategoryController::store()
4. CategoryService::create() [KHÔNG thiết lập version]
5. Database INSERT ... values ('name', 'desc', 0)  ← version = 0 (default)
6. Category có version = 0 ❌
7. Khi edit: Service check → version mismatch → FAIL
```

### **Scenario B: Thêm Category Qua Seeder**
```
1. php artisan db:seed
2. CategoryFactory::definition() [set version = 1]
3. Database INSERT ... values ('name', 'desc', 1)  ← version = 1
4. Category có version = 1 ✅
5. Khi edit: Service check → version match → SUCCESS
```

---

## 🔧 Giải Pháp

### **Fix #1: Thêm Version Vào CategoryService.create()**

File: `app/Services/CategoryService.php`

```php
public function create(array $data): Category
{
    return Category::create([
        'name' => $data['name'],
        'description' => $data['description'] ?? null,
        'version' => 1,  // ← Thêm dòng này
    ]);
}
```

**Hiệu Quả:** Từ giờ mỗi category mới add qua form sẽ có `version = 1`.

---

### **Fix #2: (Tuỳ chọn) Sửa Migration Default**

File: `database/migrations/2025_12_17_000001_add_version_to_categories_table.php`

```php
// Từ:
$table->integer('version')->default(0)->after('updated_at');

// Thành:
$table->integer('version')->default(1)->after('updated_at');
```

**Hiệu Quả:** Ngay cả khi quên set version trong code, migration default sẽ đảm bảo version = 1.

---

### **Fix #3: (Tuỳ chọn) Update Dữ Liệu Cũ**

Tạo migration mới:

```php
// database/migrations/2025_12_17_000002_fix_category_versions.php

public function up(): void
{
    DB::table('categories')
        ->where('version', 0)
        ->update(['version' => 1]);
}

public function down(): void
{
    // Revert if needed
}
```

Chạy: `php artisan migrate`

---

## 🧪 Kiểm Tra

### **1. Kiểm Tra Database**

```bash
php artisan tinker
```

```php
> DB::table('categories')->select('id', 'name', 'version')->get()
```

**Kỳ vọng:** Tất cả `version` đều là `1`

---

### **2. Kiểm Tra API Response**

Mở **F12 → Console**, chạy:

```javascript
fetch('/admin/categories/1/version')
  .then(r => r.json())
  .then(d => console.log(d))
```

**Kỳ vọng:**
```json
{
  "success": true,
  "data": {
    "version": 1
  }
}
```

---

### **3. Test Add & Edit**

1. Click "Thêm danh mục"
2. Điền: `Tên = "Test"`, `Mô tả = "Test desc"`
3. Lưu ✅
4. Mở **F12 → Console**, kiểm tra:
   ```javascript
   > DB.table('categories').where('name', 'Test').first().version
   ```
   **Kỳ vọng:** `1`
5. Click "Sửa" trên category vừa tạo
6. Thay đổi tên → Lưu ✅ **Nên thành công**

---

## 📊 So Sánh Trước & Sau

| Thao Tác | Trước Fix | Sau Fix |
|---------|----------|--------|
| Add category qua form | version = 0 ❌ | version = 1 ✅ |
| Edit category mới add | ❌ Lỗi | ✅ Thành công |
| Seeded category | version = 1 ✅ | version = 1 ✅ |
| Edit seeded category | ✅ Thành công | ✅ Thành công |

---

## 🎓 Lý Do Có Version Field

### **Optimistic Locking - Ngăn Chặn Conflict**

Khi 2 người cùng edit 1 category:

```
User A: Load form → version = 1
User B: Load form → version = 1
User A: Sửa xong → Gửi version = 1
  Server check: DB.version (1) == 1 ✅ → UPDATE
  Server set: version = 2
User B: Sửa xong → Gửi version = 1
  Server check: DB.version (2) != 1 ❌ → REJECT
  "Danh mục này đã được sửa bởi ai đó!"
```

**Mục đích:** Đảm bảo không ai bị mất dữ liệu khi sửa đồng thời.

---

## 💡 Best Practices

1. **Luôn set version = 1** cho entity mới (trong create method)
2. **Luôn tăng version** sau mỗi update: `version + 1`
3. **Luôn kiểm tra version** trước update (optimistic locking)
4. **Migration default nên = 1**, không phải = 0
5. **Factory nên match migration default**

---

## 🔗 Các File Liên Quan

| File | Vai Trò |
|------|---------|
| `database/migrations/2025_12_17_000001_add_version_to_categories_table.php` | Định nghĩa column version, default = 0 |
| `database/factories/CategoryFactory.php` | Set version = 1 cho seeder |
| `app/Services/CategoryService.php` | Create & update logic |
| `app/Http/Controllers/CategoryController.php` | Handle HTTP requests |
| `public/js/category/modal.js` | Fetch version từ API |
| `public/js/category/utils.js` | Send version lên server |

---

## ✅ Checklist Sau Khi Fix

- [ ] Sửa `CategoryService.create()` thêm `'version' => 1`
- [ ] Test add category qua form
- [ ] Kiểm tra database: `version = 1`
- [ ] Test edit category mới add
- [ ] Xác nhận: ✅ Sửa thành công
- [ ] (Tuỳ chọn) Sửa migration default từ 0 → 1
- [ ] (Tuỳ chọn) Update dữ liệu cũ có version = 0

---

**Vấn đề đã được giải quyết!** 🎉
