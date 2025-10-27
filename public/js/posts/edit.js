// edit-post.js - Edit Post Form Logic

document.addEventListener('DOMContentLoaded', () => {
  const thumbnailInput = document.getElementById('thumbnailInput');
  const thumbnailPreview = document.getElementById('thumbnailPreview');
  const btnRemove = document.querySelector('.btn-remove');
  const editPostForm = document.getElementById('edit-post-form');

  // Image upload preview
  if (thumbnailInput) {
    document.querySelector('.btn-upload-label').addEventListener('click', (e) => {
      e.preventDefault();
      thumbnailInput.click();
    });

    thumbnailInput.addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (event) => {
          document.getElementById('thumbnailImg').src = event.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // Remove thumbnail
  if (btnRemove) {
    btnRemove.addEventListener('click', (e) => {
      e.preventDefault();
      document.getElementById('thumbnailImg').src = 'https://via.placeholder.com/1200x400?text=No+Image';
    });
  }

  // Form submit
  if (editPostForm) {
    editPostForm.addEventListener('submit', (e) => {
      e.preventDefault();

      // Collect form data
      const formData = {
        title: document.getElementById('edit-title').value,
        excerpt: document.getElementById('edit-excerpt').value,
        category: document.getElementById('edit-category').value,
        tags: document.getElementById('edit-tags').value,
        thumbnail: document.getElementById('thumbnailImg').src,
        content: document.getElementById('edit-content').value,
        metaTitle: document.getElementById('edit-meta-title').value,
        metaDescription: document.getElementById('edit-meta-description').value
      };

      console.log('✅ Form data:', formData);

      // TODO: Send to API
      // await fetch('/api/posts/1', { method: 'PUT', body: JSON.stringify(formData) })

      alert('✅ Thay đổi đã được lưu thành công!');
      // Redirect back to detail page
      // window.location.href = 'detail-posts.html';
    });
  }
});
