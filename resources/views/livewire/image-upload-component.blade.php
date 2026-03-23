<div>
    <!-- Validation Errors -->
    @error('images.*')
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ $message }}
        </div>
    @enderror

    @error('groupCaption')
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ $message }}
        </div>
    @enderror

    @error('eventDate')
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ $message }}
        </div>
    @enderror
 
    <!-- Upload Errors -->
    @if(count($uploadErrors) > 0)
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($uploadErrors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Group Caption Input -->
    <div class="mb-6">
        <label class="block text-sm font-semibold text-love-900 mb-2">Caption Grup</label>
        <textarea
            wire:model="groupCaption"
            rows="3"
            placeholder="Tambahkan caption untuk semua foto..."
            class="w-full px-4 py-2.5 border-2 border-love-200 rounded-xl font-medium text-sm text-love-900 transition-all focus:border-love-500 focus:ring-4 focus:ring-love-100"
        ></textarea>
    </div>

    <!-- Event Date Input -->
    <div class="mb-6">
        <label class="block text-sm font-semibold text-love-900 mb-2">Tanggal Kejadian</label>
        <input
            type="date"
            wire:model="eventDate"
            class="flatpickr-input w-full px-4 py-2.5 border-2 border-love-200 rounded-xl font-medium text-sm text-love-900 transition-all focus:border-love-500 focus:ring-4 focus:ring-love-100"
        >
    </div>

    <!-- Drop Zone -->
    <div
        x-data="{ dragOver: false }"
        @dragover.prevent="dragOver = true"
        @dragleave.prevent="dragOver = false"
        @drop.prevent="dragOver = false"
        :class="{ 'border-love-500 bg-love-50': dragOver, 'border-gray-300 bg-white': !dragOver }"
        class="relative border-2 border-dashed rounded-2xl p-8 text-center transition-colors duration-200"
    >
        <input
            type="file"
            wire:model="images"
            multiple
            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
        />

        @if(count($images) === 0)
            <div class="space-y-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-love-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <div>
                    <p class="text-lg font-semibold text-love-900">
                        Drag & Drop foto di sini
                    </p>
                    <p class="text-sm text-love-700">
                        atau klik untuk memilih file
                    </p>
                </div>
                <p class="text-xs text-love-600">
                    Maksimal 10MB per file (JPEG, PNG, GIF, WebP)
                </p>
            </div>
        @else
            <p class="text-lg font-semibold text-love-900">
                {{ count($images) }} foto dipilih
            </p>
        @endif
    </div>

    <!-- Image Previews -->
    @if(count($images) > 0)
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($images as $index => $image)
                <div class="relative bg-white rounded-xl shadow-md overflow-hidden">
                    <img
                        src="{{ $image->temporaryUrl() }}"
                        alt="Preview"
                        class="w-full h-48 object-cover"
                    />
                    
                    <!-- Remove Button -->
                    <button
                        wire:click="removeImage({{ $index }})"
                        class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 focus:outline-none transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <p class="p-4 text-xs text-love-600 truncate">
                        {{ $image->getClientOriginalName() }}
                    </p>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Progress Bar -->
    @if($isUploading)
        <div class="mt-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-love-900">
                    Mengupload foto...
                </span>
                <span class="text-sm font-medium text-love-900">
                    {{ $uploadProgress }}%
                </span>
            </div>
            <div class="w-full bg-love-200 rounded-full h-3">
                <div
                    :style="{ width: $uploadProgress + '%' }"
                    class="bg-gradient-to-r from-love-500 to-love-600 h-3 rounded-full transition-all duration-300"
                ></div>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    @if(count($images) > 0)
        <div class="mt-6 flex justify-end gap-4">
            <button
                wire:click="resetForm"
                class="px-6 py-3 border border-love-300 text-love-700 font-semibold rounded-2xl hover:bg-love-50 focus:outline-none focus:ring-2 focus:ring-love-500 transition-all"
            >
                Batal
            </button>
            <button
                wire:click="confirmUpload"
                wire:loading.attr="disabled"
                class="px-6 py-3 bg-gradient-to-r from-love-500 to-love-600 text-white font-semibold rounded-2xl shadow-lg hover:from-love-600 hover:to-love-700 focus:outline-none focus:ring-2 focus:ring-love-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span wire:loading>
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12c0 4.411-3.589 8-8 8V0H5v5.291z"></path>
                    </svg>
                    Mengupload...
                </span>
                <span wire:loading.remove>
                    Upload Foto
                </span>
            </button>
        </div>
    @endif
</div>
