// commen.js - Quản lý bình luận với popup trả lời
(function () {

  /* DOM ELEMENTS */
  const modalRoot = document.getElementById('modal-root');
  const replyButtons = document.querySelectorAll('.action-btn:nth-of-type(2)'); // Nút "Trả lời"

  /* ===== Hiển thị popup form trả lời ===== */
  function showReplyForm(commentAuthor, commentId) {
    const modal = document.createElement('div');
    modal.className = 'reply-modal';
    modal.style.cssText = `
      position: fixed;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      background: linear-gradient(rgba(2, 6, 23, 0.4), rgba(2, 6, 23, 0.5));
      backdrop-filter: blur(2px);
      animation: fadeIn 0.2s ease;
    `;

    const panel = document.createElement('div');
    panel.style.cssText = `
      background: #ffffff;
      border-radius: 16px;
      padding: 32px;
      min-width: 420px;
      max-width: 550px;
      box-shadow: 0 20px 60px rgba(6, 25, 40, 0.15);
      border: 1px solid rgba(60, 145, 230, 0.1);
      animation: slideUp 0.3s ease;
      transition: all 0.2s ease;
    `;

    panel.innerHTML = `
      <style>
        @keyframes fadeIn {
          from { opacity: 0; }
          to { opacity: 1; }
        }
        @keyframes slideUp {
          from {
            opacity: 0;
            transform: translateY(20px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
        .reply-form-group { margin-bottom: 20px; }
        .reply-form-group label {
          display: block;
          font-size: 14px;
          font-weight: 600;
          color: #04202a;
          margin-bottom: 8px;
        }
        .reply-form-group textarea {
          width: 100%;
          padding: 12px 14px;
          border: 1.5px solid #e5e7eb;
          border-radius: 10px;
          font-family: inherit;
          font-size: 14px;
          color: #04202a;
          transition: all 0.2s ease;
          box-sizing: border-box;
          resize: vertical;
          min-height: 120px;
        }
        .reply-form-group textarea:focus {
          outline: none;
          border-color: #3C91E6;
          box-shadow: 0 0 0 3px rgba(60, 145, 230, 0.1);
        }
        .reply-original {
          background: #f8fafc;
          border-left: 3px solid #3C91E6;
          padding: 12px;
          border-radius: 8px;
          margin-bottom: 20px;
        }
        .reply-original-author {
          font-weight: 600;
          color: #04202a;
          font-size: 13px;
        }
        .reply-original-text {
          color: #6b7280;
          font-size: 13px;
          margin-top: 4px;
          line-height: 1.4;
        }
      </style>

      <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #f0f0f0;">
        <div style="font-size: 24px;">💬</div>
        <div>
          <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #04202a;">Trả lời bình luận</h2>
          <p style="margin: 4px 0 0; font-size: 12px; color: #6b7280;">Viết câu trả lời của bạn bên dưới</p>
        </div>
      </div>

      <div class="reply-original">
        <div class="reply-original-author">Trả lời cho: <strong>${commentAuthor}</strong></div>
        <div class="reply-original-text">Bình luận ban đầu sẽ hiển thị ở đây...</div>
      </div>

      <form id="reply-form" style="display: flex; flex-direction: column; gap: 0;">
        <div class="reply-form-group">
          <label for="reply-content"><i style="color: #3C91E6;">✍️</i> Nội dung trả lời *</label>
          <textarea 
            id="reply-content" 
            placeholder="Viết câu trả lời của bạn tại đây..."
            required
          ></textarea>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f0f0f0;">
          <button 
            id="reply-cancel" 
            type="button"
            style="padding: 10px 20px; background: transparent; border: 1.5px solid #e5e7eb; color: #04202a; border-radius: 10px; cursor: pointer; font-weight: 500; font-size: 14px; transition: all 0.2s ease;"
            onmouseover="this.style.background='#f9fafb';this.style.borderColor='#d1d5db';"
            onmouseout="this.style.background='transparent';this.style.borderColor='#e5e7eb';"
          >
            ✕ Hủy
          </button>
          <button 
            id="reply-submit" 
            type="submit"
            style="padding: 10px 24px; background: linear-gradient(90deg, #3C91E6, #6dd5ed); color: #04202a; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease; box-shadow: 0 4px 15px rgba(60, 145, 230, 0.3);"
            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(60, 145, 230, 0.4)';"
            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 15px rgba(60, 145, 230, 0.3)';"
          >
            ✓ Gửi trả lời
          </button>
        </div>
      </form>
    `;

    modal.appendChild(panel);
    modalRoot.appendChild(modal);

    // Đóng modal mượt
    const closeModal = () => {
      panel.style.opacity = '0';
      panel.style.transform = 'translateY(20px)';
      modal.style.opacity = '0';
      
      setTimeout(() => {
        modal.remove();
        document.removeEventListener('keydown', escHandler);
      }, 200);
    };

    // Click backdrop để đóng
    modal.addEventListener('click', e => {
      if (e.target === modal) closeModal();
    });

    // Nhấn ESC để đóng
    const escHandler = ev => {
      if (ev.key === 'Escape') closeModal();
    };
    document.addEventListener('keydown', escHandler);

    // Nút hủy
    panel.querySelector('#reply-cancel').onclick = closeModal;

    // Form submit
    panel.querySelector('#reply-form').addEventListener('submit', (e) => {
      e.preventDefault();
      
      const replyContent = panel.querySelector('#reply-content').value.trim();

      if (!replyContent) {
        alert('⚠️ Vui lòng nhập nội dung trả lời');
        return;
      }

      console.log('📝 Reply data:', {
        commentId,
        author: commentAuthor,
        content: replyContent
      });
      
      alert('✅ Trả lời đã được gửi. Xem console (F12) để chi tiết.');
      closeModal();
    });
  }

  /* ===== EVENT: Click nút "Trả lời" ===== */
  document.addEventListener('click', (e) => {
    const replyBtn = e.target.closest('.action-btn');
    
    if (replyBtn && replyBtn.textContent.includes('Trả lời')) {
      // Lấy thông tin từ comment card
      const commentCard = replyBtn.closest('.comment-card');
      if (commentCard) {
        const authorElement = commentCard.querySelector('.comment-meta strong');
        const author = authorElement ? authorElement.textContent : 'Người dùng';
        const commentId = commentCard.getAttribute('data-comment-id') || '1';
        
        showReplyForm(author, commentId);
      }
    }
  });

})();
