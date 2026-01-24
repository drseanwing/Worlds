/**
 * Worlds Application JavaScript
 * 
 * Entry point for Alpine.js and custom JavaScript functionality
 */

import Alpine from 'alpinejs';

// Make Alpine available globally for debugging
window.Alpine = Alpine;

// Initialize Alpine when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});

// Optional: Export for module usage
export default Alpine;
