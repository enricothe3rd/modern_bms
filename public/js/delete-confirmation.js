/**
 * Delete Confirmation Handler
 * Provides reusable delete confirmation functionality across all views
 */

document.addEventListener('DOMContentLoaded', function() {
    // Handle all delete button clicks
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.delete-form');
            if (!form) return;

            // Get item name from various data attributes
            const itemName = this.getAttribute('data-account-name') ||
                           this.getAttribute('data-sub-account-name') ||
                           this.getAttribute('data-form-name') ||
                           this.getAttribute('data-department-name') ||
                           this.getAttribute('data-sector-name') ||
                           this.getAttribute('data-role-name') ||
                           this.getAttribute('data-user-name') ||
                           this.getAttribute('data-fund-type-name') ||
                           this.getAttribute('data-expense-type-name') ||
                           'this item';

            // Get additional context for form signatories
            const departmentName = this.getAttribute('data-department-name');
            const formName = this.getAttribute('data-form-name');
            
            let message = `Are you sure you want to delete "${itemName}"? This action cannot be undone.`;
            
            // Special message for form signatories
            if (formName && departmentName) {
                message = `Are you sure you want to delete all signatories for "${formName}" in "${departmentName}"? This action cannot be undone.`;
            }

            // Show confirmation modal
            if (typeof showConfirmation === 'function') {
                showConfirmation({
                    title: 'Confirm Delete',
                    message: message,
                    onConfirm: function() {
                        form.submit();
                    }
                });
            } else {
                // Fallback to browser confirm if modal is not available
                if (confirm(message)) {
                    form.submit();
                }
            }
        });
    });

    // Handle remove buttons (for expense types and allocations)
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.remove-form');
            if (!form) return;

            const expenseType = this.getAttribute('data-expense-type') || 'this expense type';
            const message = `Are you sure you want to remove "${expenseType}"? This action cannot be undone.`;

            if (typeof showConfirmation === 'function') {
                showConfirmation({
                    title: 'Confirm Remove',
                    message: message,
                    onConfirm: function() {
                        form.submit();
                    }
                });
            } else {
                if (confirm(message)) {
                    form.submit();
                }
            }
        });
    });
});