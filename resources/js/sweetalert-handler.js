/**
 * SweetAlert2 Event Handler
 * Handles all SweetAlert2 confirmations and alerts
 */

document.addEventListener('DOMContentLoaded', () => {
    // Listen for SweetAlert confirm events
    window.addEventListener('swal:confirm', (event) => {
        const { title, text, icon, showCancelButton, confirmButtonText, cancelButtonText, confirmMethod, componentId } = event.detail;

        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: showCancelButton,
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText,
            confirmButtonColor: '#ec4899',
            cancelButtonColor: '#6b7280',
        }).then((result) => {
            if (result.isConfirmed) {
                // Call the Livewire method
                if (componentId && window.livewire) {
                    window.livewire.find(componentId).call(confirmMethod);
                } else if (window.livewire) {
                    // Fallback to finding the first component if ID is missing
                    const firstComponent = window.livewire.components.getComponents()[0];
                    if (firstComponent) {
                        firstComponent.call(confirmMethod);
                    }
                }
            }
        });
    });

    // Listen for SweetAlert success events
    window.addEventListener('swal:success', (event) => {
        const { title, text } = event.detail;

        Swal.fire({
            title: title,
            text: text,
            icon: 'success',
            confirmButtonColor: '#10b981',
            timer: 3000,
            timerProgressBar: true,
        });
    });

    // Listen for SweetAlert error events
    window.addEventListener('swal:error', (event) => {
        const { title, text } = event.detail;

        Swal.fire({
            title: title,
            text: text,
            icon: 'error',
            confirmButtonColor: '#ef4444',
        });
    });

    // Listen for SweetAlert info events
    window.addEventListener('swal:info', (event) => {
        const { title, text } = event.detail;

        Swal.fire({
            title: title,
            text: text,
            icon: 'info',
            confirmButtonColor: '#3b82f6',
        });
    });

    // Listen for SweetAlert warning events
    window.addEventListener('swal:warning', (event) => {
        const { title, text } = event.detail;

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            confirmButtonColor: '#f59e0b',
        });
    });
});
