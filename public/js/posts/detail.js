// detail-posts.js - Post Detail View Logic

document.addEventListener('DOMContentLoaded', () => {
  const deleteBtn = document.getElementById('delete-btn');

  // Delete button
  if (deleteBtn) {
    deleteBtn.addEventListener('click', () => {
      if (confirm('Bạn chắc chắn muốn xóa bài viết này?')) {
        console.log('✅ Post deleted');
        alert('✅ Bài viết đã được xóa');
        // window.location.href = 'blog.html';
      }
    });
  }
});
