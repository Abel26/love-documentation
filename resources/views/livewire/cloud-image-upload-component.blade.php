<div>
    <!-- Upload Errors -->
    @if(count($uploadErrors) > 0)
        <div class="mb-4 p-4 bg-red-50 border border-red-300 text-red-700 rounded-xl">
            <div class="flex items-center gap-2 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-semibold">Upload Errors</span>
            </div>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($uploadErrors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Storage Warning -->
    @if($storageCheck && !$storageCheck['can_upload'])
        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-300 text-yellow-700 rounded-xl">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="font-semibold">Storage Limit Reached</span>
            </div>
            <p class="text-sm mt-1">Kekurangan storage: {{ $storageCheck['shortage_formatted'] }}</p>
        </div>
    @endif

    <!-- Drop Zone -->
    <div
        x-data="{ dragOver: false }"
        @dragover.prevent="dragOver = true"
        @dragleave.prevent="dragOver = false"
        @drop.prevent="dragOver = false"
        :class="{ 'border-love-500 bg-love-50': dragOver, 'border-love-300 bg-white': !dragOver }"
        class="relative border-2 border-dashed rounded-2xl p-8 text-center transition-all duration-200"
    >
        <input
            type="file"
            wire:model="images"
            multiple
            accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
            :disabled="$isUploading"
        />

        @if(count($images) === 0)
            <div class="space-y-4">
                <div class="w-16 h-16 mx-auto bg-love-100 rounded-2xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-love-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-semibold text-love-900">
                        Drag & Drop images here
                    </p>
                    <p class="text-sm text-love-600">
                        or click to select files
                    </p>
                </div>
                <p class="text-xs text-love-500 font-mono">
                    Max 50MB per file • JPEG, PNG, GIF, WebP
                </p>
            </div>
        @else
            <div class="space-y-2">
                <p class="text-lg font-semibold text-love-900">
                    {{ count($images) }} image(s) selected
                </p>
                @if($storageCheck && $storageCheck['can_upload'])
                    <p class="text-sm text-love-600">
                        Total size: {{ $storageCheck['required_formatted'] }}
                    </p>
                @endif
            </div>
        @endif
    </div>

    <!-- Image Previews -->
    @if(count($images) > 0)
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
            @foreach($images as $index => $image)
                <div class="relative bg-love-50 rounded-xl overflow-hidden border border-love-200 group">
                    <img
                        src="{{ $image->temporaryUrl() }}"
                        alt="Preview {{ $index + 1 }}"
                        class="w-full h-32 object-cover"
                    />
                    <button
                        wire:click="removeImage({{ $index }})"
                        class="absolute top-2 right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="p-2 bg-white/80 backdrop-blur">
                        <p class="text-xs text-love-900 truncate font-mono">{{ $image->getClientOriginalName() }}</p>
                        <p class="text-xs text-love-600 font-mono">{{ formatBytes($image->getSize()) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex gap-3">
            <button
                wire:click="cancel"
                type="button"
                class="flex-1 py-3 bg-love-100 hover:bg-love-200 text-love-900 rounded-xl font-semibold transition-all duration-200"
                :disabled="$isUploading"
            >
                Cancel
            </button>
            <button
                wire:click="upload"
                type="button"
                class="flex-1 py-3 bg-gradient-to-r from-love-500 to-love-600 hover:from-love-600 hover:to-love-700 text-white rounded-xl font-semibold transition-all duration-200 flex items-center justify-center gap-2"
                :disabled="$isUploading"
            >
                @if($isUploading)
                    <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Uploading... {{ number_format($uploadProgress, 0) }}%
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload {{ count($images) }} Image(s)
                @endif
            </button>
        </div>

        <!-- Progress Bar -->
        @if($isUploading)
            <div class="mt-4">
                <div class="h-2 bg-love-100 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-love-500 to-love-600 transition-all duration-300" style="width: {{ $uploadProgress }}%"></div>
                </div>
                <p class="text-xs text-love-600 mt-2 text-center font-mono">
                    Uploading... {{ number_format($uploadProgress, 1) }}%
                </p>
            </div>
        @endif
    @endif
</div>
