// Event Handlers - Xử lý sự kiện
import { CATEGORY_CONFIG as CONFIG } from './constants.js';
// Import UI function từ modal.js
import { showFormModal } from './modal.js';
// Import API functions từ utils.js
import { deleteCategory, showNotification, getTextContent } from './utils.js';

/**
 * Khởi tạo event listener cho nút "Thêm danh mục"
 */
export function initAddButton() {
  const addBtn = document.querySelector(CONFIG.SELECTORS.ADD_BTN);
  if (!addBtn) return;

  addBtn.addEventListener('click', () => {
    showFormModal({
      title: CONFIG.TITLES.ADD,
      categoryId: null
    });
  });
}

/**
 * Khởi tạo event listener cho cards grid (sửa category)
 */
export function initCardsGrid() {
  const cardsRoot = document.querySelector(CONFIG.SELECTORS.CARDS_GRID);
  if (!cardsRoot) return;

  cardsRoot.addEventListener('click', (e) => {
    const card = e.target.closest('.cat-card');
    if (!card) return;

    const id = card.dataset.id;
    const name = getTextContent(card, '.card-title');
    const description = getTextContent(card, '.muted');

    showFormModal({
      title: CONFIG.TITLES.EDIT(name),
      name,
      description,
      categoryId: id
    });
  });
}

/**
 * Khởi tạo event listener cho bảng (sửa + xóa)
 */
export function initTableActions() {
  const tbody = document.querySelector(CONFIG.SELECTORS.TABLE_BODY);
  if (!tbody) return;

  tbody.addEventListener('click', async (e) => {
    const button = e.target.closest('button');
    if (!button) return;

    const row = button.closest('tr');
    const categoryId = row?.dataset.id;
    const categoryName = getTextContent(row, 'td:nth-child(2)');

    console.log('🎯 Button clicked:', { buttonText: button.textContent, row, categoryId, categoryName, dataAttr: row?.dataset });

    if (button.textContent.includes('Sửa')) {
      handleEditClick(categoryId, categoryName, row);
    } else if (button.textContent.includes('Xóa')) {
      await handleDeleteClick(categoryId, categoryName);
    }
  });
}

/**
 * Xử lý click nút "Sửa"
 * @private
 */
function handleEditClick(categoryId, categoryName, row) {
  const description = getTextContent(row, 'td:nth-child(3)');

  console.log('📋 Edit clicked:', { categoryId, categoryName, description });

  showFormModal({
    title: CONFIG.TITLES.EDIT(categoryName),
    name: categoryName,
    description,
    categoryId
  });
}

/**
 * Xử lý click nút "Xóa"
 * @private
 */
async function handleDeleteClick(categoryId, categoryName) {
  if (!confirm(CONFIG.MESSAGES.DELETE_CONFIRM(categoryName))) {
    return;
  }

  try {
    const result = await deleteCategory(categoryId);

    if (result.status) {
      showNotification(CONFIG.MESSAGES.DELETE_SUCCESS);
      setTimeout(() => location.reload(), 500);
    } else {
      showNotification(
        CONFIG.MESSAGES.ERROR + ': ' + (result.message || 'Không xác định')
      );
    }
  } catch (error) {
    console.error('Delete error:', error);
    showNotification(CONFIG.MESSAGES.ERROR + ': ' + error.message);
  }
}

/**
 * Khởi tạo responsive table (thêm data-label)
 */
export function initTableResponsive() {
  const table = document.getElementById(CONFIG.SELECTORS.TABLE);
  if (!table) return;

  const headers = Array.from(table.querySelectorAll('thead th'))
    .map(th => th.textContent.trim());
  
  const rows = table.querySelectorAll('tbody tr');
  rows.forEach(tr => {
    Array.from(tr.children).forEach((td, i) => {
      if (!td.hasAttribute('data-label')) {
        td.setAttribute('data-label', headers[i] || '');
      }
    });
  });
}

/**
 * Khởi tạo tất cả event handlers
 */
export function initAllHandlers() {
  initAddButton();
  initCardsGrid();
  initTableActions();
  initTableResponsive();
}
