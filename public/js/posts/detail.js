// detail-posts.js - Post Detail View Logic with Security

document.addEventListener('DOMContentLoaded', () => {
  const deleteBtn = document.getElementById('delete-btn');

  // Delete button with security checks
  if (deleteBtn) {
    deleteBtn.addEventListener('click', async () => {
      if (confirm('Bạn chắc chắn muốn xóa bài viết này? Hành động này không thể hoàn tác!')) {
        const postId = deleteBtn.dataset.id;
        
        // ✅ SECURITY 1: Validate postID format
        if (!postId || isNaN(postId) || parseInt(postId) <= 0) {
          alert('❌ ID bài viết không hợp lệ!');
          return;
        }
        
        try {
          // ✅ SECURITY 2: Get CSRF token
          const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
          
          if (!csrfToken) {
            alert('❌ CSRF token không tìm thấy. Vui lòng tải lại trang!');
            return;
          }

          // Disable button to prevent double-click
          deleteBtn.disabled = true;
          deleteBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Đang xóa...';
          
          // ✅ SECURITY 3: Send DELETE request with proper headers
          const response = await fetch(`/admin/posts/${postId}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          // ✅ SECURITY 4: Check HTTP response status FIRST
          if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(
              errorData.message || 
              `Lỗi HTTP ${response.status}: ${response.statusText}`
            );
          }

          // ✅ SECURITY 5: Parse response
          const data = await response.json();

          // ✅ SECURITY 6: Verify success status
          if (data.status === true) {
            alert(data.message || '✓ Xóa bài viết thành công!');
            window.location.href = '/admin/posts';
          } else {
            alert(data.message || '❌ Xóa bài viết thất bại!');
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = '<i class="bx bx-trash"></i> Xóa';
          }
        } catch (error) {
          console.error('Delete error:', error);
          alert(`❌ Lỗi: ${error.message}`);
          
          // Re-enable button on error
          deleteBtn.disabled = false;
          deleteBtn.innerHTML = '<i class="bx bx-trash"></i> Xóa';
        }
      }
    });
  }
});
