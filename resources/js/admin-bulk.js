/**
 * Admin Bulk Operations JavaScript
 * Provides reusable bulk action functionality for admin list pages
 */

/**
 * Initialize bulk actions for a table
 * @param {string} tableSelector - CSS selector for the table
 * @param {string} bulkFormId - ID of the bulk actions form
 */
export function initBulkActions(tableSelector = 'table', bulkFormId = 'bulk-actions-form') {
    const table = document.querySelector(tableSelector);
    if (!table) return;

    const selectAllCheckbox = document.getElementById('select-all');
    const itemCheckboxes = document.querySelectorAll('.select-item');
    const bulkActionsBar = document.getElementById('bulk-actions-bar');
    const bulkForm = document.getElementById(bulkFormId);
    const selectedCount = document.getElementById('selected-count');

    // Select/deselect all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsBar();
        });
    }

    // Individual checkbox change
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateBulkActionsBar();
        });
    });

    /**
     * Update the "select all" checkbox state based on individual selections
     */
    function updateSelectAllState() {
        if (!selectAllCheckbox) return;

        const checkedCount = document.querySelectorAll('.select-item:checked').length;
        const totalCount = itemCheckboxes.length;

        selectAllCheckbox.checked = checkedCount === totalCount && totalCount > 0;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
    }

    /**
     * Show/hide bulk actions bar and update count
     */
    function updateBulkActionsBar() {
        const checkedCount = document.querySelectorAll('.select-item:checked').length;

        if (bulkActionsBar) {
            if (checkedCount > 0) {
                bulkActionsBar.classList.remove('hidden');
                if (selectedCount) {
                    selectedCount.textContent = checkedCount;
                }
            } else {
                bulkActionsBar.classList.add('hidden');
            }
        }
    }

    /**
     * Apply bulk action (called from form submit)
     */
    window.applyBulkAction = function(event) {
        event.preventDefault();

        const actionSelect = document.getElementById('bulk-action');
        const action = actionSelect?.value;

        if (!action) {
            alert('Please select an action');
            return;
        }

        const checkedBoxes = document.querySelectorAll('.select-item:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one item');
            return;
        }

        // Confirmation messages based on action
        const confirmMessages = {
            delete: `Are you sure you want to delete ${selectedIds.length} item(s)? This action cannot be undone.`,
            publish: `Publish ${selectedIds.length} item(s)?`,
            unpublish: `Unpublish ${selectedIds.length} item(s)?`,
            active: `Set ${selectedIds.length} item(s) to active status?`,
            inactive: `Set ${selectedIds.length} item(s) to inactive status?`,
        };

        const confirmMessage = confirmMessages[action] || `Apply this action to ${selectedIds.length} item(s)?`;

        if (!confirm(confirmMessage)) {
            return;
        }

        // Create hidden inputs for selected IDs
        const idsContainer = document.getElementById('bulk-ids-container');
        if (idsContainer) {
            idsContainer.innerHTML = '';
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                idsContainer.appendChild(input);
            });
        }

        // Set action value
        const actionInput = document.getElementById('bulk-action-input');
        if (actionInput) {
            actionInput.value = action;
        }

        // Submit form
        if (bulkForm) {
            bulkForm.submit();
        }
    };

    // Initialize on page load
    updateSelectAllState();
    updateBulkActionsBar();
}

/**
 * Auto-initialize bulk actions on DOM ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on an admin page with bulk actions
    if (document.getElementById('bulk-actions-bar') || document.getElementById('select-all')) {
        initBulkActions();
    }
});
