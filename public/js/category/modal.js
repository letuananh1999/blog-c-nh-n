// Modal UI Component - Tách UI logic ra
import { CATEGORY_CONFIG as CONFIG } from './constants.js';
import { fetchCategoryVersion, saveCategoryData, showNotification, getCsrfToken } from './utils.js';

/**
 * Tạo HTML form modal
 * @param {object} data - {title, name, description}
 * @returns {string}
 */
function createFormHTML(data) {
  const { title, name = '', description = '' } = data;

  return `
    <style>
      @keyframes slideUp { 
        from { opacity: 0; transform: translateY(20px); } 
        to { opacity: 1; transform: translateY(0); } 
      }
      .form-group { margin-bottom: 20px; }
      .form-group label { 
        display: block; 
        font-size: 14px; 
        font-weight: 600; 
        color: #04202a; 
        margin-bottom: 8px; 
      }
      .form-group input, 
      .form-group textarea { 
        width: 100%; 
        padding: 12px 14px; 
        border: 1.5px solid #e5e7eb; 
        border-radius: 10px; 
        font-family: inherit; 
        font-size: 14px; 
        color: #04202a; 
        transition: all 0.2s ease; 
        box-sizing: border-box; 
      }
      .form-group input:focus, 
      .form-group textarea:focus { 
        outline: none; 
        border-color: #3C91E6; 
        box-shadow: 0 0 0 3px rgba(60, 145, 230, 0.1); 
      }
      .form-group textarea { 
        resize: vertical; 
        min-height: 100px; 
      }
    </style>

    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #f0f0f0;">
      <div style="font-size: 24px;">📋</div>
      <div>
        <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #04202a;">${title}</h2>
        <p style="margin: 4px 0 0; font-size: 12px; color: #6b7280;">Quản lý danh mục của bạn</p>
      </div>
    </div>

    <form id="f-form" style="display: flex; flex-direction: column; gap: 0;">
      <div class="form-group">
        <label for="f-name"><i style="color: #3C91E6;">🏷️</i> Tên danh mục *</label>
        <input id="f-name" type="text" placeholder="Nhập tên danh mục (e.g., Design, Marketing...)" value="${name}" />
      </div>

      <div class="form-group">
        <label for="f-desc"><i style="color: #3C91E6;">📝</i> Mô tả</label>
        <textarea id="f-desc" placeholder="Mô tả chi tiết về danh mục này...">${description}</textarea>
      </div>

      <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; padding-top: 16px; border-top: 1px solid #f0f0f0;">
        <button id="f-cancel" type="button" class="btn-cancel">✕ Hủy</button>
        <button id="f-save" type="submit" class="btn-save">✓ Lưu</button>
      </div>
    </form>

    <style>
      .btn-cancel {
        padding: 10px 20px;
        background: transparent;
        border: 1.5px solid #e5e7eb;
        color: #04202a;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s ease;
      }
      .btn-cancel:hover {
        background: #f9fafb;
        border-color: #d1d5db;
      }
      .btn-save {
        padding: 10px 24px;
        background: linear-gradient(90deg, #3C91E6, #6dd5ed);
        color: #04202a;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 15px rgba(60, 145, 230, 0.3);
      }
      .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(60, 145, 230, 0.4);
      }
    </style>
  `;
}

/**
 * Hiển thị modal form sửa/thêm category
 * @param {object} options - {title, name, description, categoryId}
 */
export function showFormModal(options) {
  const { title, name = '', description = '', categoryId = null } = options;
  
  const modalRoot = document.querySelector(CONFIG.SELECTORS.MODAL_ROOT);
  const modal = document.createElement('div');
  
  modal.className = 'modal';
  modal.style.cssText = `
    position: fixed;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    background: linear-gradient(rgba(2, 6, 23, 0.4), rgba(2, 6, 23, 0.5));
    backdrop-filter: blur(2px);
    transition: opacity 0.2s ease;
  `;

  const panel = document.createElement('div');
  panel.style.cssText = `
    background: #ffffff;
    border-radius: 16px;
    padding: 32px;
    min-width: 380px;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(6, 25, 40, 0.15);
    border: 1px solid rgba(60, 145, 230, 0.1);
    animation: slideUp 0.3s ease;
    transition: all 0.2s ease;
  `;

  panel.innerHTML = createFormHTML({ title, name, description });

  modal.appendChild(panel);
  modalRoot.appendChild(modal);

  // Close modal function
  const closeModal = () => {
    panel.style.opacity = '0';
    panel.style.transform = 'translateY(20px)';
    modal.style.opacity = '0';
    setTimeout(() => {
      modal.remove();
      document.removeEventListener('keydown', escHandler);
    }, 200);
  };

  // Event listeners
  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });

  const escHandler = (ev) => {
    if (ev.key === 'Escape') closeModal();
  };

  document.addEventListener('keydown', escHandler);
  panel.querySelector('#f-cancel').onclick = closeModal;

  // Form submit handler
  panel.querySelector('#f-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    await handleFormSubmit(panel, closeModal, categoryId);
  });
}

/**
 * Xử lý form submit
 * @private
 */
async function handleFormSubmit(formPanel, closeModal, categoryId) {
  const name = formPanel.querySelector('#f-name').value.trim();
  const description = formPanel.querySelector('#f-desc').value.trim();

  console.log('📤 Form submit:', { categoryId, name, description });

  if (!name) {
    showNotification(CONFIG.MESSAGES.EMPTY_NAME);
    return;
  }

  try {
    // Lấy version nếu update
    const body = { name, description };
    if (categoryId) {
      const versionData = await fetchCategoryVersion(categoryId);
      if (!versionData.status) {
        showNotification(CONFIG.MESSAGES.VERSION_ERROR);
        return;
      }
      body.version = versionData.data.version;
    }

    // Save data
    const result = await saveCategoryData(body, categoryId);

    if (result.status) {
      showNotification(CONFIG.MESSAGES.SUCCESS);
      closeModal();
      setTimeout(() => location.reload(), 500);
    } else {
      showNotification(
        CONFIG.MESSAGES.ERROR + ': ' + (result.message || 'Không xác định')
      );
    }
  } catch (error) {
    console.error('Form submit error:', error);
    showNotification(CONFIG.MESSAGES.ERROR + ': ' + error.message);
  }
}
