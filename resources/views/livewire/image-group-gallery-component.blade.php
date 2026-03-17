<div>
    <!-- Gallery Modal -->
    @if($showGalleryModal)
        <div 
            class="fixed inset-0 z-50 overflow-y-auto" 
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
            @keydown.escape="closeGallery"
            x-data x-on:keydown.escape="$dispatch('close-modal')"
        >
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop overlay - dengan pointer-events yang jelas -->
                @if($showGalleryModal)
                    <div 
                        class="fixed inset-0 bg-black/90 backdrop-blur-sm transition-opacity" 
                        wire:click="closeGallery"
                        style="pointer-events: auto;"
                    ></div>
                @endif

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div 
                    class="relative inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full"
                    style="pointer-events: auto; position: relative; z-index: 10;"
                >
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white">
                            {{ $group->caption ?? 'Galeri Foto' }}
                        </h3>
                        <div class="flex items-center gap-4">
                            <span class="text-white text-sm">
                                {{ $selectedImageIndex + 1 }} / {{ $group->image_count }}
                            </span>
                            <button wire:click="closeGallery" class="text-white hover:text-gray-200">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Main Image Viewer -->
                    <div class="relative bg-gray-900" style="min-height: 500px;">
                        @if($this->currentImage)
                            <img
                                src="{{ asset('storage/' . $this->currentImage->path) }}"
                                alt="{{ $this->currentImage->original_filename }}"
                                class="w-full h-full object-contain max-h-[600px]"
                            >
                        @endif

                        <!-- Navigation Buttons -->
                        <button
                            wire:click="previousImage"
                            class="absolute left-4 top-1/2 transform -translate-y-1/2 p-3 bg-white/20 hover:bg-white/40 rounded-full text-white transition-colors {{ $selectedImageIndex === 0 ? 'opacity-30 cursor-not-allowed' : '' }}"
                            {{ $selectedImageIndex === 0 ? 'disabled' : '' }}
                        >
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button
                            wire:click="nextImage"
                            class="absolute right-4 top-1/2 transform -translate-y-1/2 p-3 bg-white/20 hover:bg-white/40 rounded-full text-white transition-colors {{ $selectedImageIndex === $group->image_count - 1 ? 'opacity-30 cursor-not-allowed' : '' }}"
                            {{ $selectedImageIndex === $group->image_count - 1 ? 'disabled' : '' }}
                        >
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Thumbnail Strip -->
                    <div class="bg-gray-100 p-4">
                        <div class="flex gap-2 overflow-x-auto pb-2">
                            @foreach($group->images as $index => $image)
                                <div class="relative group flex-shrink-0">
                                    <button
                                        wire:click="selectImage({{ $index }})"
                                        class="w-20 h-20 rounded-lg overflow-hidden border-2 transition-all {{ $selectedImageIndex === $index ? 'border-indigo-500 scale-105' : 'border-transparent hover:border-gray-300' }}"
                                    >
                                        <img
                                            src="{{ asset('storage/' . $image->thumbnail_path) }}"
                                            alt="{{ $image->original_filename }}"
                                            class="w-full h-full object-cover"
                                        >
                                    </button>
                                    <!-- Tombol Hapus Thumbnail -->
                                    <button
                                        wire:click="confirmDeleteThumbnail({{ $index }})"
                                        class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg opacity-0 group-hover:opacity-100"
                                        title="Hapus gambar ini"
                                    >
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                    <!-- Tombol Pindah Thumbnail -->
                                    <button
                                        wire:click="openMoveModal({{ $index }})"
                                        class="absolute top-1 left-1 w-6 h-6 bg-purple-500 text-white rounded-full flex items-center justify-center hover:bg-purple-600 transition-colors shadow-lg opacity-0 group-hover:opacity-100"
                                        title="Pindahkan gambar ini"
                                    >
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4-4m-4 4l4 4" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Footer Info -->
                    <div class="bg-white px-6 py-4 border-t">
                        @if($this->currentImage)
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900 mb-1">
                                        {{ $this->currentImage->original_filename }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $this->currentImage->formatted_size }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">
                                        {{ $group->event_date->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Caption Section -->
                    <div class="bg-white px-6 py-4 border-t">
                        @if($isEditing)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Caption Grup
                                </label>
                                <textarea
                                    wire:model="caption"
                                    rows="3"
                                    placeholder="Tambahkan caption..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                ></textarea>
                                <label class="block text-sm font-medium text-gray-700 mb-2 mt-4">
                                    Tanggal Kejadian
                                </label>
                                <input
                                    type="date"
                                    wire:model="editEventDate"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                >
                                <div class="flex justify-end gap-4 mt-4">
                                    <button
                                        wire:click="disableEditMode"
                                        class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        wire:click="confirmUpdateCaption"
                                        class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-semibold rounded-xl shadow-lg hover:from-indigo-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all"
                                    >
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-700 mb-2">
                                        Caption Grup
                                    </p>
                                    <p class="text-gray-900">
                                        {{ $group->caption ?? 'Tidak ada caption' }}
                                    </p>
                                </div>
                                <button
                                    wire:click="enableEditMode"
                                    class="ml-4 px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414 0l-4 4m0 0l4-4m4 4V4" />
                                    </svg>
                                    Edit
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="bg-white px-6 py-4 border-t">
                        <div class="flex flex-wrap gap-3">
                            <a
                                href="{{ route('image-groups.download', $group->uuid) }}"
                                class="flex-1 sm:flex-none px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4 4h4M4 16l4-4m0 0l-4 4m4 4v-4" />
                                </svg>
                                Download ZIP
                            </a>
                            @if(!$isEditing)
                                <button
                                    wire:click="openShareModal"
                                    class="flex-1 sm:flex-none px-6 py-3 border border-pink-300 text-pink-700 font-semibold rounded-xl hover:bg-pink-50 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-all"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 9.316a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                    </svg>
                                    Share
                                </button>
                                @if(!$showDeleteConfirm)
                                    <button
                                        wire:click="confirmDeleteImage"
                                        class="flex-1 sm:flex-none px-6 py-3 border border-red-300 text-red-700 font-semibold rounded-xl hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0118.138 21H7.862a2 2 0 01-1.995-1.858L5 9m0 0l5.867-12.142A2 2 0 012.138-21H7.862a2 2 0 01-1.995 1.858L5 9m0 0l5.867 12.142A2 2 0 002.138 21H17.862a2 2 0 001.995-1.858L19 7zM14 7l-4 4m0 0l4-4m4 4V7" />
                                        </svg>
                                        Hapus Gambar
                                    </button>
                                @else
                                    <div class="flex-1 sm:flex-none flex gap-3">
                                        <button
                                            wire:click="hideDeleteConfirmation"
                                            class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"
                                        >
                                            Batal
                                        </button>
                                        <button
                                            wire:click="deleteImage"
                                            class="flex-1 px-6 py-3 bg-red-500 text-white font-semibold rounded-xl shadow-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all"
                                        >
                                            Ya, Hapus
                                        </button>
                                    </div>
                                @endif
                                <button
                                    wire:click="showDeleteGroupConfirmation"
                                    class="flex-1 sm:flex-none px-6 py-3 border border-orange-300 text-orange-700 font-semibold rounded-xl hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0118.138 21H7.862a2 2 0 01-1.995-1.858L5 9m0 0l5.867-12.142A2 2 0 012.138-21H7.862a2 2 0 01-1.995 1.858L5 9m0 0l5.867 12.142A2 2 0 002.138 21H17.862a2 2 0 001.995-1.858L19 7zM14 7l-4 4m0 0l4-4m4 4V7" />
                                    </svg>
                                    Hapus Grup
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Share Modal -->
    @if($showShareModal)
        <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="pointer-events: auto;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" style="pointer-events: auto;">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeShareModal" style="pointer-events: auto;"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100" style="z-index: 101; pointer-events: auto;">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900">Share Grup</h3>
                            <button wire:click="closeShareModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Share URL -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">URL Share</label>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    value="{{ $shareUrl }}"
                                    readonly
                                    class="flex-1 px-4 py-2.5 border-2 border-gray-200 rounded-xl font-medium text-sm text-gray-900 bg-gray-50"
                                >
                                <button
                                    wire:click="copyShareUrl"
                                    class="px-4 py-2.5 bg-blue-500 text-white rounded-xl font-semibold hover:bg-blue-600 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Share Text -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Teks Share</label>
                            <textarea
                                readonly
                                rows="2"
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl font-medium text-sm text-gray-900 bg-gray-50"
                            >{{ $shareText }}</textarea>
                        </div>

                        <!-- Social Media Buttons -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-3">Share ke Social Media</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button
                                    wire:click="shareToWhatsApp"
                                    class="flex items-center justify-center gap-2 px-4 py-3 bg-green-500 text-white rounded-xl font-semibold hover:bg-green-600 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-.767.916-1.94 2.247a11.815 11.815 0 01-5.421 7.403A11.815 11.815 0 010-16.813-7.403 11.821 11.821 0 01-5.48-8.413z"/>
                                    </svg>
                                    WhatsApp
                                </button>
                                <button
                                    wire:click="shareToFacebook"
                                    class="flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v8.385C7.69 8.095 4.418 6.13 0 12.073z"/>
                                    </svg>
                                    Facebook
                                </button>
                                <button
                                    wire:click="shareToTwitter"
                                    class="flex items-center justify-center gap-2 px-4 py-3 bg-sky-500 text-white rounded-xl font-semibold hover:bg-sky-600 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958a4.958 4.923a4.958 4.923a4.958 4.923a4.923 0 01-.321.325c-.016.093-.036.306.02.472a4.923 4.923 0 01-.616.14c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 01-2.163-2.723c-.106-.008-.211-.023-.316-.025-.093.008-.184.02-.272.053a4.904 4.904 0 01-4.604 3.417a9.867 9.867 0 01-4.142 2.475c-.39 0-.779-.023-1.17-.067a4.936 4.936 0 01-4.604 3.417 4.923 4.923 0 01-3.48-8.413z"/>
                                    </svg>
                                    Twitter
                                </button>
                                <button
                                    wire:click="shareToTelegram"
                                    class="flex items-center justify-center gap-2 px-4 py-3 bg-blue-400 text-white rounded-xl font-semibold hover:bg-blue-500 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 002 12 12 12 0 0 0 2-12A12 12 0 0 0111.944 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225.46-1.9.902-1.9-.696.065-1.252.241-1.297.789-.696.065-1.252.241-1.297.789z"/>
                                    </svg>
                                    Telegram
                                </button>
                            </div>
                        </div>

                        <button wire:click="closeShareModal" class="w-full px-4 py-3 border-2 border-gray-200 text-gray-900 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Group Confirmation Modal -->
    @if($showDeleteGroupConfirm)
        <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="pointer-events: auto;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" style="pointer-events: auto;">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="hideDeleteGroupConfirmation" style="pointer-events: auto;"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100" style="z-index: 101; pointer-events: auto;">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900">Hapus Grup</h3>
                            <button wire:click="hideDeleteGroupConfirmation" class="text-gray-400 hover:text-gray-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mb-6">
                            <p class="text-gray-800 mb-4">
                                Apakah Anda yakin ingin menghapus grup ini beserta semua gambar di dalamnya?
                            </p>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-sm text-gray-700">
                                    <strong>Caption:</strong> {{ $group->caption ?? '-' }}
                                </p>
                                <p class="text-sm text-gray-700">
                                    <strong>Tanggal Kejadian:</strong> {{ $group->event_date->format('d M Y') }}
                                </p>
                                <p class="text-sm text-gray-700">
                                    <strong>Jumlah Gambar:</strong> {{ $group->image_count }}
                                </p>
                            </div>
                            <p class="text-sm text-red-600 mt-4">
                                ⚠️ Semua gambar dalam grup ini akan dihapus secara permanen.
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button wire:click="hideDeleteGroupConfirmation" class="flex-1 px-4 py-3 border-2 border-gray-200 text-gray-900 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button wire:click="confirmDeleteGroup" class="flex-1 px-4 py-3 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Thumbnail Confirmation Modal -->
    @if($showThumbnailDeleteConfirm && $imageToDelete !== null && isset($group->images[$imageToDelete]))
        <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="pointer-events: auto;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" style="pointer-events: auto;">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="hideThumbnailDeleteConfirmation" style="pointer-events: auto;"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100" style="z-index: 101; pointer-events: auto;">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900">Hapus Gambar</h3>
                            <button wire:click="hideThumbnailDeleteConfirmation" class="text-gray-400 hover:text-gray-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mb-6">
                            <p class="text-gray-800 mb-4">
                                Apakah Anda yakin ingin menghapus gambar ini?
                            </p>
                            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                                <img
                                    src="{{ asset('storage/' . $group->images[$imageToDelete]->thumbnail_path) }}"
                                    alt="{{ $group->images[$imageToDelete]->original_filename }}"
                                    class="w-full h-48 object-cover rounded-lg"
                                >
                            </div>
                            <p class="text-sm text-gray-700">
                                <strong>Nama File:</strong> {{ $group->images[$imageToDelete]->original_filename }}
                            </p>
                            <p class="text-sm text-red-600 mt-4">
                                ⚠️ Gambar ini akan dihapus secara permanen.
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button wire:click="hideThumbnailDeleteConfirmation" class="flex-1 px-4 py-3 border-2 border-gray-200 text-gray-900 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button wire:click="deleteThumbnailImage" class="flex-1 px-4 py-3 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Move Image Modal -->
    @if($showMoveModal)
        <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="pointer-events: auto;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" style="pointer-events: auto;">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" wire:click="closeMoveModal" style="pointer-events: auto;"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100" style="z-index: 101; pointer-events: auto;">
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900">Pindahkan Gambar</h3>
                            <button wire:click="closeMoveModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mb-6">
                            @if($imageToMove !== null && isset($group->images[$imageToMove]))
                                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                                    <img
                                        src="{{ asset('storage/' . $group->images[$imageToMove]->thumbnail_path) }}"
                                        alt="{{ $group->images[$imageToMove]->original_filename }}"
                                        class="w-full h-32 object-cover rounded-lg mb-2"
                                    >
                                    <p class="text-sm text-gray-700">
                                        <strong>Nama File:</strong> {{ $group->images[$imageToMove]->original_filename }}
                                    </p>
                                </div>
                            @endif
                            <label class="block text-sm font-semibold text-gray-900 mb-2">Pilih Grup Tujuan</label>
                            <select
                                wire:model="targetGroupId"
                                class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl font-medium text-sm text-gray-900 transition-all focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                            >
                                <option value="">Pilih grup...</option>
                                @foreach($availableGroups as $groupOption)
                                    <option value="{{ $groupOption->uuid }}">
                                        {{ $groupOption->caption ?? 'Tanpa Caption' }} ({{ $groupOption->event_date->format('d M Y') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @error('targetGroupId')
                            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="flex gap-3">
                            <button wire:click="closeMoveModal" class="flex-1 px-4 py-3 border-2 border-gray-200 text-gray-900 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button wire:click="moveImage" class="flex-1 px-4 py-3 bg-purple-500 text-white rounded-xl font-semibold hover:bg-purple-600 transition-colors">
                                Pindahkan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Gallery Modal Keyboard Handler -->
@if($showGalleryModal)
<script>
    /**
     * Handle keyboard navigation and closing
     */
    document.addEventListener('keydown', function(e) {
        // Escape key to close modal
        if (e.key === 'Escape') {
            const galleryComponent = document.querySelector('[wire\\:id]')?.closest('[wire\\:id]');
            if (galleryComponent) {
                // Find the actual component
                const allComponentIds = document.querySelectorAll('[wire\\:id]');
                for (let el of allComponentIds) {
                    const comp = Livewire.find(el.getAttribute('wire:id'));
                    if (comp && comp.showGalleryModal) {
                        comp.call('closeGallery');
                        break;
                    }
                }
            }
        }
        
        // Arrow keys for navigation
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            const allComponentIds = document.querySelectorAll('[wire\\:id]');
            for (let el of allComponentIds) {
                const comp = Livewire.find(el.getAttribute('wire:id'));
                if (comp && comp.showGalleryModal && typeof comp.nextImage === 'function') {
                    comp.call('nextImage');
                    break;
                }
            }
        }
        
        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            const allComponentIds = document.querySelectorAll('[wire\\:id]');
            for (let el of allComponentIds) {
                const comp = Livewire.find(el.getAttribute('wire:id'));
                if (comp && comp.showGalleryModal && typeof comp.previousImage === 'function') {
                    comp.call('previousImage');
                    break;
                }
            }
        }
    });
</script>
@endif
