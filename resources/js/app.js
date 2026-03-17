import './bootstrap';
import './lazy-loading';
import './sweetalert-handler';
import './cursor-love';
import $ from 'jquery';
import 'datatables.net';
import 'datatables.net-bs5';
import Swal from 'sweetalert2';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.$ = $;
window.Swal = Swal;

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

Alpine.start();
