// Category JS Constants & Configuration
export const CATEGORY_CONFIG = {
  // DOM Selectors
  SELECTORS: {
    MODAL_ROOT: '#modal-root',
    ADD_BTN: '#add-cat',
    CARDS_GRID: '.cards-grid',
    TABLE: '#cat-table',
    TABLE_BODY: '#cat-table tbody',
    SEARCH_INPUT: '#cate-search',
    SEARCH_BTN: '#cate-search-btn',
    CSRF_TOKEN: 'meta[name="csrf-token"]'
  },

  // API Endpoints
  ENDPOINTS: {
    CATEGORIES: '/admin/categories',
    VERSION: (id) => `/admin/categories/${id}/version`,
    EDIT: (id) => `/admin/categories/${id}`,
    DELETE: (id) => `/admin/categories/${id}`,
    SEARCH: '/admin/categories/search'
  },

  // Messages
  MESSAGES: {
    EMPTY_NAME: '⚠️ Vui lòng nhập tên danh mục',
    SUCCESS: '🎉 Lưu thành công!',
    ERROR: '❌ Lỗi',
    DELETE_CONFIRM: (name) => `🗑️ Bạn chắc chắn muốn xóa "${name}"?`,
    DELETE_SUCCESS: '🗑️ Xóa thành công!',
    FETCH_ERROR: '❌ Không thể lấy dữ liệu. Vui lòng tải lại trang!',
    VERSION_ERROR: '⚠️ Lỗi lấy dữ liệu. Vui lòng tải lại trang!'
  },

  // Modal Titles
  TITLES: {
    ADD: '➕ Thêm danh mục',
    EDIT: (name) => `✏️ Sửa danh mục: ${name}`
  },

  // HTTP Methods
  METHODS: {
    GET: 'GET',
    POST: 'POST',
    PUT: 'PUT',
    DELETE: 'DELETE'
  },

  // HTTP Status
  STATUS_CODES: {
    OK: 200,
    CREATED: 201,
    NOT_FOUND: 404,
    SERVER_ERROR: 500
  }
};
