/**
 * Global Notification System
 * Provides utilities for triggering slide-down notifications
 * Works with both AJAX responses and Laravel session flash messages
 */

/**
 * Trigger a notification
 * @param {string} message - The message to display
 * @param {string} type - The notification type ('success' or 'error')
 */
window.notify = function(message, type = 'success') {
    window.dispatchEvent(new CustomEvent('notify', {
        detail: { message, type }
    }));
};

/**
 * Auto-detect and display Laravel session flash messages on page load
 * Flash messages are stored in hidden divs with data attributes
 */
document.addEventListener('DOMContentLoaded', () => {
    // Success messages
    const successMessages = document.querySelectorAll('[data-flash-success]');
    successMessages.forEach(el => {
        window.notify(el.dataset.flashSuccess, 'success');
    });

    // Error messages
    const errorMessages = document.querySelectorAll('[data-flash-error]');
    errorMessages.forEach(el => {
        window.notify(el.dataset.flashError, 'error');
    });
});
