import './bootstrap';
import './lazy-loading';
import './sweetalert-handler';
import './cursor-love';
import $ from 'jquery';
import 'datatables.net';
import 'datatables.net-bs5';
import Swal from 'sweetalert2';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import 'flatpickr/dist/l10n/id';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.$ = $;
window.Swal = Swal;
window.flatpickr = flatpickr;

// Set default locale to Indonesian
flatpickr.localize(flatpickr.l10ns.id);

// Copy to clipboard
window.addEventListener('copyToClipboard', e => {
    const text = e.detail.text;
    navigator.clipboard.writeText(text).then(() => {
        console.log('Text copied to clipboard');
    }).catch(err => {
        console.error('Failed to copy text: ', err);
    });
});

// Show toast notification
window.addEventListener('showToast', e => {
    const { type, message } = e.detail;
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: type === 'success' ? '#10B981' : '#EF4444',
        icon: type === 'success' ? 'success' : 'warning'
    });
    
    Toast.fire({
        title: message,
        icon: type === 'success' ? 'success' : 'warning'
    });
});

// Copy to clipboard
window.addEventListener('copyToClipboard', e => {
    const text = e.detail.text;
    navigator.clipboard.writeText(text).then(() => {
        console.log('Text copied to clipboard');
    }).catch(err => {
        console.error('Failed to copy text: ', err);
    });
});

// Open in new tab
window.addEventListener('openInNewTab', e => {
    const url = e.detail.url;
    window.open(url, '_blank');
});

// Initialize Flatpickr on all input[type="date"]
document.addEventListener('DOMContentLoaded', function() {
    // Single date picker
    const singleDatePickers = document.querySelectorAll('input[type="date"]:not(.flatpickr-input)');
    singleDatePickers.forEach(picker => {
        flatpickr(picker, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd F Y',
            theme: 'love',
            onChange: function(selectedDates, dateStr, instance) {
                // Trigger Livewire update
                picker.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    });

    // Date range picker (for start-end date pairs)
    const dateRangeGroups = document.querySelectorAll('.date-range-group');
    dateRangeGroups.forEach(group => {
        const startDate = group.querySelector('.date-start');
        const endDate = group.querySelector('.date-end');

        if (startDate && endDate) {
            flatpickr(startDate, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                theme: 'love',
                onChange: function(selectedDates, dateStr, instance) {
                    // Set min date for end date picker
                    if (endDate._flatpickr) {
                        endDate._flatpickr.set('minDate', dateStr);
                    }
                    startDate.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

            flatpickr(endDate, {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd F Y',
                theme: 'love',
                onChange: function(selectedDates, dateStr, instance) {
                    endDate.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        }
    });
});

Alpine.start();
