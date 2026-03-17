<div>
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-love-900">Galeri Foto</h1>
        <button
            wire:click="openUploadModal"
            type="button"
            class="btn-primary"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4m8 0l-4-4m4 4l-4 4" />
            </svg>
            Upload Foto Baru
        </button>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-love-100 transition-all duration-300 hover:shadow-xl">
        <div class="flex items-center gap-2 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-love-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <h3 class="text-lg font-bold text-love-900">Filter Gambar</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="filterMonth" class="block text-sm font-semibold text-love-900 mb-2">Filter Bulan</label>
                <select
                    id="filterMonth"
                    wire:model="filterMonth"
                    class="w-full px-4 py-2.5 border-2 border-love-200 rounded-xl font-medium text-sm text-love-900 transition-all focus:border-love-500 focus:ring-4 focus:ring-love-100"
                >
                    <option value="">Semua Bulan</option>
                    @foreach($availableMonths as $month)
                        <option value="{{ $month }}">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filterStartDate" class="block text-sm font-semibold text-love-900 mb-2">Tanggal Mulai</label>
                <input
                    id="filterStartDate"
                    type="date"
                    wire:model="filterStartDate"
                    class="w-full px-4 py-2.5 border-2 border-love-200 rounded-xl font-medium text-sm text-love-900 transition-all focus:border-love-500 focus:ring-4 focus:ring-love-100"
                >
            </div>
            <div>
                <label for="filterEndDate" class="block text-sm font-semibold text-love-900 mb-2">Tanggal Akhir</label>
                <input
                    id="filterEndDate"
                    type="date"
                    wire:model="filterEndDate"
                    class="w-full px-4 py-2.5 border-2 border-love-200 rounded-xl font-medium text-sm text-love-900 transition-all focus:border-love-500 focus:ring-4 focus:ring-love-100"
                >
            </div>
            <div class="flex items-end">
                <button
                    wire:click="resetFilters"
                    type="button"
                    class="btn-secondary w-full"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Images Table area -->
    <div class="bg-white rounded-2xl shadow-lg border border-love-100 mb-6 overflow-hidden transition-all duration-300 hover:shadow-2xl" wire:ignore>
        <div class="p-6 border-b-2 border-love-100 bg-gradient-to-r from-love-50 to-love-100/50">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-love-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <h3 class="text-xl font-bold text-love-900 uppercase tracking-wider">Tabel Galeri Foto</h3>
                <span class="ml-auto text-xs font-semibold text-love-600 bg-love-100 px-3 py-1 rounded-full whitespace-nowrap">Total Items</span>
            </div>
        </div>
        <div class="p-6">
            <div class="dataTables_wrapper">
                <div class="overflow-x-auto">
                    <table id="imagesTable" class="w-full text-left">
                        <thead>
                            <tr>
                                <th class="sorting">Thumbnail</th>
                                <th class="sorting">Caption</th>
                                <th class="sorting">Tanggal Kejadian</th>
                                <th class="sorting">Jumlah Gambar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will populate this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    @if($showUploadModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-love-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="closeUploadModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-love-100">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-love-900" id="modal-title">
                                Upload Foto
                            </h3>
                            <button
                                wire:click="closeUploadModal"
                                class="text-love-400 hover:text-love-600 transition-colors bg-love-50 p-2 rounded-xl"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Upload Component -->
                        <livewire:image-upload-component />
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Image Modal -->
    @if($selectedImage)
        <livewire:image-modal-component :image="$selectedImage" />
    @endif

    <!-- Delete Group Modal -->
    @if($showDeleteModal && $selectedGroup)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-love-900/40 backdrop-blur-sm transition-opacity" wire:click="closeDeleteModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-love-100">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-love-900">Hapus Grup</h3>
                            <button wire:click="closeDeleteModal" class="text-love-400 hover:text-love-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mb-6">
                            <p class="text-love-800 mb-4">
                                Apakah Anda yakin ingin menghapus grup ini?
                            </p>
                            <div class="bg-love-50 rounded-xl p-4">
                                <p class="text-sm text-love-700">
                                    <strong>Caption:</strong> {{ $selectedGroup->caption ?? '-' }}
                                </p>
                                <p class="text-sm text-love-700">
                                    <strong>Tanggal Kejadian:</strong> {{ $selectedGroup->event_date->format('d M Y') }}
                                </p>
                                <p class="text-sm text-love-700">
                                    <strong>Jumlah Gambar:</strong> {{ $selectedGroup->image_count }}
                                </p>
                            </div>
                            <p class="text-sm text-red-600 mt-4">
                                ⚠️ Semua gambar dalam grup ini akan dihapus secara permanen.
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button wire:click="closeDeleteModal" class="flex-1 px-4 py-3 border-2 border-love-200 text-love-900 rounded-xl font-semibold hover:bg-love-50 transition-colors">
                                Batal
                            </button>
                            <button wire:click="deleteGroup" class="flex-1 px-4 py-3 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Gallery Modal Container -->
    @if($showGalleryModal && $selectedGroupUuid)
        <div 
            class="fixed inset-0 z-50"
            style="pointer-events: none;"
        >
            <livewire:image-group-gallery-component 
                :group-uuid="$selectedGroupUuid" 
                :key="$selectedGroupUuid"
            />
        </div>
    @endif

    <!-- DataTables Script -->
    <script>
        let imagesTable = null;

        /**
         * Initialize DataTables
         */
        function initDataTables() {
            if (typeof $ === 'undefined' || typeof $.fn.DataTable === 'undefined') {
                setTimeout(initDataTables, 100);
                return;
            }

            // Jika sudah di-initialize, skip
            if ($.fn.DataTable.isDataTable('#imagesTable')) {
                return;
            }

            const table = $('#imagesTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('livewire.images-table') }}',
                    type: 'GET',
                    data: function(d) {
                        d.filterMonth = @js($filterMonth);
                        d.filterStartDate = @js($filterStartDate);
                        d.filterEndDate = @js($filterEndDate);
                    }
                },
                columns: [
                    { data: 'thumbnails', orderable: false, searchable: false },
                    { data: 'caption', orderable: true, searchable: true },
                    { data: 'event_date', orderable: true, searchable: true },
                    { data: 'image_count', orderable: true, searchable: false },
                    { data: 'actions', orderable: false, searchable: false, className: 'text-center' }
                ],
                columnDefs: [
                    {
                        targets: 0,
                        width: '80px',
                        className: 'text-center'
                    },
                    {
                        targets: 4,
                        width: '140px'
                    }
                ],
                language: {
                    processing: '<div class="flex items-center justify-center p-8"><div class="flex flex-col items-center gap-3"><div class="animate-spin rounded-full h-12 w-12 border-4 border-love-200 border-t-love-500"></div><p class="text-sm text-love-600 font-semibold">Memuat data...</p></div></div>',
                    search: 'Cari:',
                    searchPlaceholder: 'Cari caption...',
                    lengthMenu: '_MENU_ data per halaman',
                    info: 'Menampilkan _START_ hingga _END_ dari _TOTAL_ data',
                    infoEmpty: 'Tidak ada data yang tersedia',
                    infoFiltered: '<span class="text-love-600 font-medium">(disaring dari _MAX_ total data)</span>',
                    loadingRecords: 'Sedang memuat...',
                    zeroRecords: '<div class="flex flex-col items-center justify-center py-16 px-4"><svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-love-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg><p class="text-lg font-semibold text-love-600">Tidak ada gambar yang ditemukan</p><p class="text-sm text-love-400 mt-2">Coba ubah filter atau upload gambar baru</p></div>',
                    paginate: {
                        first: '«',
                        previous: '‹',
                        next: '›',
                        last: '»'
                    }
                },
                order: [[2, 'desc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                dom: '<"dataTables_top"<"dataTables_length_wrapper"l><"dataTables_filter_wrapper"f>>t<"dataTables_bottom"<"dataTables_info_bottom"i><"dataTables_pagination_wrapper"p>>',
                drawCallback: function() {
                    $('#imagesTable tbody tr').addClass('fade-in');
                }
            });

            imagesTable = table;

            // Style adjustments untuk pagination
            setTimeout(function() {
                const searchLabel = document.querySelector('.dataTables_filter label');
                if (searchLabel) {
                    searchLabel.style.color = 'transparent';
                    searchLabel.style.fontSize = '0';
                }

                const paginateContainer = document.querySelector('.dataTables_paginate');
                if (paginateContainer) {
                    paginateContainer.style.display = 'flex';
                    paginateContainer.style.flexDirection = 'row';
                    paginateContainer.style.flexWrap = 'nowrap';
                    paginateContainer.style.justifyContent = 'center';
                    paginateContainer.style.alignItems = 'center';

                    const buttons = paginateContainer.querySelectorAll('span, li, a, button');
                    buttons.forEach(function(btn) {
                        btn.style.display = 'inline-flex';
                        btn.style.alignItems = 'center';
                        btn.style.justifyContent = 'center';
                    });
                }
            }, 100);
        }

        /**
         * Get Livewire component instance
         */
        function getLivewireComponent() {
            const el = document.querySelector('[wire\\:id]');
            return el ? Livewire.find(el.getAttribute('wire:id')) : null;
        }

        /**
         * Setup event listeners - called once on DOM ready
         */
        function setupEventListeners() {
            // Event untuk tombol "Lihat Semua Gambar" (openGallery)
            window.addEventListener('openGallery', function(e) {
                const component = getLivewireComponent();
                if (component && typeof component.openGallery === 'function') {
                    console.log('Event: openGallery dengan UUID:', e.detail);
                    component.openGallery(e.detail);
                }
            });

            // Event untuk tombol "Hapus Grup" (openDeleteGroupModal)
            window.addEventListener('openDeleteGroupModal', function(e) {
                const component = getLivewireComponent();
                if (component && typeof component.openDeleteGroupModal === 'function') {
                    console.log('Event: openDeleteGroupModal dengan UUID:', e.detail);
                    component.openDeleteGroupModal(e.detail);
                }
            });

            // Setup Livewire listeners untuk DataTables reload
            if (typeof Livewire !== 'undefined') {
                Livewire.on('updatedFilterMonth', function() { 
                    if (imagesTable) imagesTable.ajax.reload(null, false); 
                });
                Livewire.on('updatedFilterStartDate', function() { 
                    if (imagesTable) imagesTable.ajax.reload(null, false); 
                });
                Livewire.on('updatedFilterEndDate', function() { 
                    if (imagesTable) imagesTable.ajax.reload(null, false); 
                });
                Livewire.on('refreshGrid', function() { 
                    if (imagesTable) imagesTable.ajax.reload(null, false); 
                });
                
                // IMPORTANT: Listen for gallery close event from child component
                // This ensures parent also resets showGalleryModal and selectedGroupUuid
                Livewire.on('galleryModalClosed', function() {
                    console.log('Gallery modal closed by child component');
                    const component = getLivewireComponent();
                    if (component && typeof component.closeGalleryModal === 'function') {
                        component.closeGalleryModal();
                    }
                });
            }
        }

        // Initialize saat DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            initDataTables();
            setupEventListeners();
        });

        // Reinitialize DataTables jika diperlukan setelah Livewire update
        if (typeof Livewire !== 'undefined') {
            Livewire.on('updated', function() {
                // Jika DataTable belum ada, initialize
                if (!$.fn.DataTable.isDataTable('#imagesTable')) {
                    initDataTables();
                }
            });
        }
    </script>
</div>
