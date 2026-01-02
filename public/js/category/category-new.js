/**
 * Category Management Module
 * 
 * Manages CRUD operations for blog categories with clean code architecture:
 * - Constants: Central configuration
 * - Utils: Reusable API & utility functions
 * - Modal: UI component for forms
 * - Handlers: Event listeners & business logic
 */

import { initAllHandlers } from './handlers.js';

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  initAllHandlers();
});
