<div>
    @if($showModal && $image)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="relative inline-block w-full max-w-4xl p-6 text-left align-bottom transition-all transform bg-white rounded-3xl shadow-2xl sm:my-8 sm:align-middle">
                    
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-love-900" id="modal-title">
                            Detail Foto
                        </h2>
                        <button
                            wire:click="closeModal"
                            class="text-love-400 hover:text-love-600 focus:outline-none"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Image Display -->
                    <div class="mb-6">
                        <img
                            src="{{ $image->url }}"
                            alt="{{ $image->display_name }}"
                            class="w-full rounded-2xl"
                        />
                    </div>

                    <!-- Image Info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-love-700">
                                Nama File
                            </p>
                            <p class="text-lg font-semibold text-love-900">
                                {{ $image->display_name }}
                            </p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-love-700">
                                Ukuran
                            </p>
                            <p class="text-lg font-semibold text-love-900">
                                {{ $image->formatted_size }}
                            </p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-love-700">
                                Tanggal Kejadian
                            </p>
                            <p class="text-lg font-semibold text-love-900">
                                {{ $image->imageGroup->event_date->format('d M Y') }}
                            </p>
                        </div>
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-love-700">
                                Diupload oleh
                            </p>
                            <p class="text-lg font-semibold text-love-900">
                                {{ $image->user->username ?? 'Unknown' }}
                            </p>
                        </div>
                    </div>

                    <!-- Caption Section -->
                    <div class="mb-6">
                        @if($isEditing)
                            <div>
                                <label class="block text-sm font-medium text-love-900 mb-2">
                                    Caption Grup
                                </label>
                                <textarea
                                    wire:model="caption"
                                    rows="3"
                                    placeholder="Tambahkan caption..."
                                    class="w-full px-4 py-3 border border-love-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-love-500 focus:border-transparent"
                                ></textarea>
                                <label class="block text-sm font-medium text-love-900 mb-2 mt-4">
                                    Tanggal Kejadian
                                </label>
                                <input
                                    type="date"
                                    wire:model="editEventDate"
                                    class="flatpickr-input w-full px-4 py-3 border border-love-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-love-500 focus:border-transparent"
                                >
                                <div class="flex justify-end gap-4 mt-4">
                                    <button
                                        wire:click="disableEditMode"
                                        class="px-6 py-3 border border-love-300 text-love-700 font-semibold rounded-xl hover:bg-love-50 focus:outline-none focus:ring-2 focus:ring-love-500 transition-all"
                                    >
                                        Batal
                                    </button>
                                    <button
                                        wire:click="confirmUpdateCaption"
                                        class="px-6 py-3 bg-gradient-to-r from-love-500 to-love-600 text-white font-semibold rounded-xl shadow-lg hover:from-love-600 hover:to-love-700 focus:outline-none focus:ring-2 focus:ring-love-500 focus:ring-offset-2 transition-all"
                                    >
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-love-700 mb-2">
                                        Caption Grup
                                    </p>
                                    <p class="text-love-900">
                                        {{ $image->imageGroup->caption ?? 'Tidak ada caption' }}
                                    </p>
                                </div>
                                <button
                                    wire:click="enableEditMode"
                                    class="px-4 py-2 border border-love-300 text-love-700 font-medium rounded-xl hover:bg-love-50 focus:outline-none focus:ring-2 focus:ring-love-500 transition-all"
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
                    <div class="flex flex-col sm:flex-row justify-end gap-4">
                        <a
                            href="{{ route('image-groups.download', $image->imageGroup->uuid) }}"
                            class="flex-1 sm:flex-none px-6 py-3 border border-love-300 text-love-700 font-semibold rounded-xl hover:bg-love-50 focus:outline-none focus:ring-2 focus:ring-love-500 transition-all"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4 4h4M4 16l4-4m0 0l-4 4m4 4v-4" />
                            </svg>
                            Download ZIP
                        </a>
                        @if(!$isEditing)
                            <button
                                wire:click="openMoveModal"
                                class="flex-1 sm:flex-none px-6 py-3 border border-purple-300 text-purple-700 font-semibold rounded-xl hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4-4m-4 4l4 4" />
                                </svg>
                                Pindahkan
                            </button>
                            <button
                                wire:click="openShareModal"
                                class="flex-1 sm:flex-none px-6 py-3 border border-pink-300 text-pink-700 font-semibold rounded-xl hover:bg-pink-50 focus:outline-none focus:ring-2 focus:ring-pink-500 transition-all"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 9.316a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                Share
                            </button>
                        @endif
                        @if(!$showDeleteConfirm)
                            <button
                                wire:click="confirmDeleteImage"
                                class="flex-1 sm:flex-none px-6 py-3 border border-red-300 text-red-700 font-semibold rounded-xl hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0118.138 21H7.862a2 2 0 01-1.995-1.858L5 9m0 0l5.867-12.142A2 2 0 012.138-21H7.862a2 2 0 01-1.995 1.858L5 9m0 0l5.867 12.142A2 2 0 002.138 21H17.862a2 2 0 001.995-1.858L19 7zM14 7l-4 4m0 0l4-4m4 4V7" />
                                </svg>
                                Hapus
                            </button>
                        @else
                            <div class="flex-1 sm:flex-none flex gap-4">
                                <button
                                    wire:click="hideDeleteConfirmation"
                                    class="flex-1 px-6 py-3 border border-love-300 text-love-700 font-semibold rounded-xl hover:bg-love-50 focus:outline-none focus:ring-2 focus:ring-love-500 transition-all"
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
                    </div>
                </div>
            </div>
        </div>
        @endif
    
        <!-- Move Modal -->
        @if($showMoveModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-love-900/40 backdrop-blur-sm transition-opacity" wire:click="closeMoveModal"></div>
    
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    
                    <div class="relative inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-love-100">
                        <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-2xl font-bold text-love-900">Pindahkan Gambar</h3>
                                <button wire:click="closeMoveModal" class="text-love-400 hover:text-love-600">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
    
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-love-900 mb-2">Pilih Grup Tujuan</label>
                                <select
                                    wire:model="targetGroupId"
                                    class="w-full px-4 py-2.5 border-2 border-love-200 rounded-xl font-medium text-sm text-love-900 transition-all focus:border-love-500 focus:ring-4 focus:ring-love-100"
                                >
                                    <option value="">Pilih grup...</option>
                                    @foreach($availableGroups as $group)
                                        <option value="{{ $group->uuid }}">
                                            {{ $group->caption ?? 'Tanpa Caption' }} ({{ $group->event_date->format('d M Y') }})
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
                                <button wire:click="closeMoveModal" class="flex-1 px-4 py-3 border-2 border-love-200 text-love-900 rounded-xl font-semibold hover:bg-love-50 transition-colors">
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
    
        <!-- Share Modal -->
        @if($showShareModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-love-900/40 backdrop-blur-sm transition-opacity" wire:click="closeShareModal"></div>
    
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
    
                    <div class="relative inline-block align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-love-100">
                        <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-2xl font-bold text-love-900">Share Grup</h3>
                                <button wire:click="closeShareModal" class="text-love-400 hover:text-love-600">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
    
                            <!-- Share URL -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-love-900 mb-2">URL Share</label>
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        value="{{ $shareUrl }}"
                                        readonly
                                        class="flex-1 px-4 py-2.5 border-2 border-love-200 rounded-xl font-medium text-sm text-love-900 bg-love-50"
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
                                <label class="block text-sm font-semibold text-love-900 mb-2">Teks Share</label>
                                <textarea
                                    readonly
                                    rows="2"
                                    class="w-full px-4 py-2.5 border-2 border-love-200 rounded-xl font-medium text-sm text-love-900 bg-love-50"
                                >{{ $shareText }}</textarea>
                            </div>
    
                            <!-- Social Media Buttons -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-love-900 mb-3">Share ke Social Media</label>
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
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 4.923a4.958 4.958 0 01-.321.325c-.016.093-.036.306.02.472a4.923 4.923 0 01-.616.14c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 01-2.163-2.723c-.106-.008-.211-.023-.316-.025-.093.008-.184.02-.272.053a4.904 4.904 0 01-4.604 3.417a9.867 9.867 0 01-4.142 2.475c-.39 0-.779-.023-1.17-.067a4.936 4.936 0 01-4.604 3.417 4.923 4.923 0 01-3.48-8.413z"/>
                                        </svg>
                                        Twitter
                                    </button>
                                    <button
                                        wire:click="shareToTelegram"
                                        class="flex items-center justify-center gap-2 px-4 py-3 bg-blue-400 text-white rounded-xl font-semibold hover:bg-blue-500 transition-colors"
                                    >
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 012 12 12 12 0 0 012-12A12 12 0 0 0111.944 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225.46-1.9.902-1.9-.696.065-1.252.241-1.297.789-.696.065-1.252.241-1.297.789z"/>
                                        </svg>
                                        Telegram
                                    </button>
                                </div>
                            </div>
    
                            <button wire:click="closeShareModal" class="w-full px-4 py-3 border-2 border-love-200 text-love-900 rounded-xl font-semibold hover:bg-love-50 transition-colors">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
