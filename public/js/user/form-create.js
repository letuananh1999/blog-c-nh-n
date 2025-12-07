/**
 * Avatar upload handler
 * - Preview hình khi chọn
 * - Xóa hình đã chọn
 */

document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('u-avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const removeAvatarBtn = document.getElementById('remove-avatar');

    if (!avatarInput || !avatarPreview || !removeAvatarBtn) {
        console.warn('Avatar form elements not found');
        return;
    }

    /**
     * Xử lý khi người dùng chọn ảnh
     */
    avatarInput.addEventListener('change', function(e) {
        const file = this.files[0];

        if (!file) return;

        // Kiểm tra loại file
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Vui lòng chọn file ảnh hợp lệ (jpeg, png, jpg, gif)');
            this.value = '';
            return;
        }

        // Kiểm tra kích thước (max 2MB)
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            alert('Kích thước ảnh không được vượt quá 2MB');
            this.value = '';
            return;
        }

        // Hiển thị preview
        const reader = new FileReader();
        reader.onload = function(event) {
            avatarPreview.innerHTML = `
                <img src="${event.target.result}" 
                     alt="Avatar preview" 
                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
            `;
        };
        reader.readAsDataURL(file);
    });

    /**
     * Xóa ảnh đã chọn
     */
    removeAvatarBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        avatarInput.value = '';
        
        // Khôi phục placeholder
        if (document.querySelector('.avatar-placeholder')) {
            // Nếu là form create
            avatarPreview.innerHTML = `
                <div class="avatar-placeholder">Ảnh đại diện<br><small>(png, jpg)</small></div>
            `;
        } else {
            // Nếu là form edit, khôi phục hình cũ hoặc placeholder
            const firstLetter = document.getElementById('u-name')?.value?.charAt(0)?.toUpperCase() || 'A';
            avatarPreview.innerHTML = `
                <div class="avatar-placeholder">${firstLetter}</div>
            `;
        }
    });
});
