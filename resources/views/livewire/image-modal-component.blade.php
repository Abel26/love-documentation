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
                                Tanggal Upload
                            </p>
                            <p class="text-lg font-semibold text-love-900">
                                {{ $image->upload_date->format('d M Y H:i') }}
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
                                    Caption
                                </label>
                                <textarea
                                    wire:model="caption"
                                    rows="3"
                                    placeholder="Tambahkan caption..."
                                    class="w-full px-4 py-3 border border-love-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-love-500 focus:border-transparent"
                                ></textarea>
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
                                        Caption
                                    </p>
                                    <p class="text-love-900">
                                        {{ $image->caption ?? 'Tidak ada caption' }}
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
                        <button
                            wire:click="downloadImage"
                            class="flex-1 sm:flex-none px-6 py-3 border border-love-300 text-love-700 font-semibold rounded-xl hover:bg-love-50 focus:outline-none focus:ring-2 focus:ring-love-500 transition-all"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4 4h4M4 16l4-4m0 0l-4 4m4 4v-4" />
                            </svg>
                            Download
                        </button>
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
</div>
