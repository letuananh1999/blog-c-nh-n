// Utility Functions - Tách logic chung
import { CATEGORY_CONFIG as CONFIG } from './constants.js';

/**
 * Lấy CSRF token từ meta tag
 * @returns {string}
 */
export function getCsrfToken() {
  return document.querySelector(CONFIG.SELECTORS.CSRF_TOKEN)?.content || '';
}

/**
 * API Fetch Helper - Giảm duplicate code
 * @param {string} url - API endpoint
 * @param {object} options - fetch options
 * @returns {Promise}
 */
export async function apiCall(url, options = {}) {
  const defaultHeaders = {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': getCsrfToken()
  };

  console.log('📡 API Call:', { url, method: options.method, headers: defaultHeaders });

  const response = await fetch(url, {
    ...options,
    headers: { ...defaultHeaders, ...options.headers }
  });

  console.log('📥 API Response:', { url, status: response.status, statusText: response.statusText, ok: response.ok });

  return response.json();
}

/**
 * Lấy version category từ server
 * @param {number} categoryId
 * @returns {Promise<object>}
 */
export async function fetchCategoryVersion(categoryId) {
  return apiCall(CONFIG.ENDPOINTS.VERSION(categoryId), {
    method: CONFIG.METHODS.GET
  });
}

/**
 * Tạo hoặc cập nhật category
 * @param {object} data - {name, description, version?}
 * @param {number} categoryId - null nếu create
 * @returns {Promise}
 */
export async function saveCategoryData(data, categoryId = null) {
  const isUpdate = categoryId !== null;
  const url = isUpdate ? CONFIG.ENDPOINTS.EDIT(categoryId) : CONFIG.ENDPOINTS.CATEGORIES;
  const method = isUpdate ? CONFIG.METHODS.PUT : CONFIG.METHODS.POST;

  console.log('🚀 saveCategoryData:', { categoryId, isUpdate, url, method, data });

  return apiCall(url, {
    method,
    body: JSON.stringify(data)
  });
}

/**
 * Xóa category
 * @param {number} categoryId
 * @returns {Promise}
 */
export async function deleteCategory(categoryId) {
  return apiCall(CONFIG.ENDPOINTS.DELETE(categoryId), {
    method: CONFIG.METHODS.DELETE
  });
}

/**
 * Hiển thị notification toast
 * @param {string} message
 * @param {string} type - success, error, warning
 */
export function showNotification(message, type = 'info') {
  // TODO: Replace alert() with proper toast/notification system
  // Hiện tại vẫn dùng alert() nhưng đây là nơi để thay thế
  alert(message);
}

/**
 * Extract text content from element safely
 * @param {HTMLElement} element
 * @param {string} selector
 * @returns {string}
 */
export function getTextContent(element, selector) {
  return element?.querySelector(selector)?.textContent?.trim() || '';
}
