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

Alpine.start();
