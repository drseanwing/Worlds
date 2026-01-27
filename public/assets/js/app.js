/**
 * Worlds Application JavaScript
 *
 * Custom JavaScript functionality for the Worlds application.
 * Alpine.js is loaded via CDN in the base template.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Application initialization
    initializeApp();
});

/**
 * Initialize application-specific functionality
 */
function initializeApp() {
    // Initialize keyboard shortcuts
    initKeyboardShortcuts();

    // Initialize auto-save for forms
    initAutoSave();
}

/**
 * Initialize keyboard shortcuts
 */
function initKeyboardShortcuts() {
    document.addEventListener('keydown', (event) => {
        // Ctrl/Cmd + S: Save form
        if ((event.ctrlKey || event.metaKey) && event.key === 's') {
            const form = document.querySelector('form[data-autosave]');
            if (form) {
                event.preventDefault();
                form.submit();
            }
        }

        // Escape: Close modals
        if (event.key === 'Escape') {
            const modal = document.querySelector('[x-data][x-show]');
            if (modal && window.Alpine) {
                // Let Alpine handle modal closing
            }
        }
    });
}

/**
 * Initialize auto-save functionality for forms
 */
function initAutoSave() {
    const forms = document.querySelectorAll('form[data-autosave]');

    forms.forEach((form) => {
        const inputs = form.querySelectorAll('input, textarea, select');

        inputs.forEach((input) => {
            input.addEventListener('change', () => {
                // Store form data in localStorage for recovery
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                const key = `autosave_${form.id || form.action}`;
                localStorage.setItem(key, JSON.stringify(data));
            });
        });
    });
}
