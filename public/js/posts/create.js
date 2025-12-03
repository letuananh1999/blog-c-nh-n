/**
 * Create Post Form Handler
 * Manages form interactions, thumbnail preview, and submission
 */

document.addEventListener('DOMContentLoaded', function() {
    const thumbnailInput = document.getElementById('thumbnail-input');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    const removeButton = document.getElementById('remove-thumbnail');
    const addBlogForm = document.getElementById('add-blog-form');
    const saveDraftBtn = document.getElementById('save-draft');
    const cancelBtn = document.getElementById('cancel-form');

    /**
     * Handle thumbnail file selection
     */
    if (thumbnailInput) {
        thumbnailInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    // Clear placeholder
                    thumbnailPreview.innerHTML = '';
                    
                    // Create image element
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.alt = file.name;
                    
                    thumbnailPreview.appendChild(img);
                    
                    // Show remove button
                    if (removeButton) {
                        removeButton.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    /**
     * Handle thumbnail removal
     */
    if (removeButton) {
        removeButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Clear file input
            if (thumbnailInput) {
                thumbnailInput.value = '';
            }
            
            // Reset preview
            thumbnailPreview.innerHTML = `
                <div class="thumbnail-placeholder">
                    <i class='bx bx-image'></i>
                    <p>Chưa có ảnh</p>
                </div>
            `;
            
            // Hide remove button
            this.style.display = 'none';
        });
    }

    /**
     * Handle save draft
     */
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Set status to draft if not already set
            const statusSelect = document.querySelector('select[name="status"]');
            if (statusSelect) {
                statusSelect.value = 'draft';
            }
            
            // Submit form
            addBlogForm.submit();
        });
    }

    /**
     * Handle cancel
     */
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Bạn có chắc chắn muốn hủy? Tất cả dữ liệu sẽ bị mất!')) {
                window.location.href = '/admin/posts';
            }
        });
    }

    /**
     * Handle form submission with loading state
     */
    if (addBlogForm) {
        addBlogForm.addEventListener('submit', function(e) {
            // Get submit button
            const submitBtn = this.querySelector('button[type="submit"]');
            
            if (submitBtn) {
                // Add loading state
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Đang xử lý...';
                
                // Optional: restore after 3 seconds if not submitted
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                }, 3000);
            }
        });
    }

    /**
     * Auto-generate slug from title (optional - server-side does this)
     */
    const titleInput = document.querySelector('input[name="title"]');
    const slugInput = document.querySelector('input[name="slug"]');
    
    if (titleInput && slugInput) {
        titleInput.addEventListener('blur', function() {
            // This is optional since server auto-generates slug
            // But we can show it to the user
            const slug = this.value
                .toLowerCase()
                .trim()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            
            if (!slugInput.value) {
                slugInput.value = slug;
            }
        });
    }

    /**
     * Auto-resize textarea as user types
     */
    const contentTextarea = document.querySelector('textarea[name="content"]');
    if (contentTextarea) {
        contentTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    /**
     * Character count for meta fields
     */
    const metaTitleInput = document.querySelector('input[name="meta_title"]');
    const metaDescInput = document.querySelector('textarea[name="meta_description"]');

    if (metaTitleInput) {
        metaTitleInput.addEventListener('input', function() {
            const count = this.value.length;
            const label = this.parentElement.querySelector('label');
            if (label) {
                label.textContent = `Meta Title (${count}/60)`;
            }
        });
    }

    if (metaDescInput) {
        metaDescInput.addEventListener('input', function() {
            const count = this.value.length;
            const label = this.parentElement.querySelector('label');
            if (label) {
                label.textContent = `Meta Description (${count}/160)`;
            }
        });
    }

    /**
     * Editor toolbar functionality (basic)
     */
    const editorToolbar = document.querySelector('.editor-toolbar');
    if (editorToolbar) {
        const buttons = editorToolbar.querySelectorAll('button');
        buttons.forEach((btn, index) => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                // Basic editor functions can be added here
                console.log('Editor button clicked:', index);
            });
        });
    }
});
