/**
 * Cart Management - AJAX Operations
 * Handles add-to-cart functionality without page reload
 */

/**
 * Add item to cart via AJAX
 * @param {HTMLFormElement} form - The add-to-cart form
 * @param {Function} onSuccess - Optional callback after successful addition
 */
window.addToCartAjax = async function(form, onSuccess) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');

    if (submitButton) {
        // Disable button to prevent double-submission
        submitButton.disabled = true;
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';

        try {
            const response = await window.axios.post(form.action, formData, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            // Show success notification
            if (response.data.message) {
                window.notify(response.data.message, 'success');
            }

            // Update cart count
            if (response.data.cartCount !== undefined) {
                window.updateCartCount(response.data.cartCount);
            }

            // GA4: add_to_cart event
            if (typeof gtag === 'function' && response.data.item) {
                gtag('event', 'add_to_cart', {
                    currency: 'USD',
                    value: response.data.item.price * response.data.item.quantity,
                    items: [{
                        item_id: response.data.item.id,
                        item_name: response.data.item.name,
                        price: response.data.item.price,
                        quantity: response.data.item.quantity,
                    }]
                });
            }

            // Meta Pixel: AddToCart event
            if (typeof fbq === 'function' && response.data.item) {
                fbq('track', 'AddToCart', {
                    content_ids: [response.data.item.id],
                    content_type: 'product',
                    value: response.data.item.price * response.data.item.quantity,
                    currency: 'USD'
                });
            }

            // Execute callback
            if (onSuccess) onSuccess(response.data);

        } catch (error) {
            // Handle error
            const message = error.response?.data?.message || 'Failed to add item to cart';
            window.notify(message, 'error');

            // Cart add failed silently — user already notified via window.notify
        } finally {
            // Re-enable button
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    }
};

/**
 * Update cart count badge
 * @param {number} count - New cart item count
 */
window.updateCartCount = function(count) {
    // Dispatch event for Alpine.js components to listen
    window.dispatchEvent(new CustomEvent('cart-updated', {
        detail: { count }
    }));
};

/**
 * Fetch current cart count from server
 */
window.refreshCartCount = async function() {
    try {
        const response = await window.axios.get('/api/cart/count', {
            headers: {
                'Accept': 'application/json'
            }
        });

        if (response.data.count !== undefined) {
            window.updateCartCount(response.data.count);
        }
    } catch (error) {
        // Failed to refresh cart count — non-critical, ignore silently
    }
};

// Refresh cart count on page load
document.addEventListener('DOMContentLoaded', () => {
    window.refreshCartCount();
});
