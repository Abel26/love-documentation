<div>
    <!-- Validation Errors -->
    @error('videos.*')
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            {{ $message }}
        </div>
    @enderror

    @error('videos')
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
            wire:model="videos"
            multiple
            accept="video/mp4,video/webm,video/quicktime,video/x-msvideo,video/x-matroska"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
        />

        @if(count($videos) === 0)
            <div class="space-y-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-love-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                <div>
                    <p class="text-lg font-semibold text-love-900">
                        Drag & Drop video di sini
                    </p>
                    <p class="text-sm text-love-700">
                        atau klik untuk memilih file
                    </p>
                </div>
                <p class="text-xs text-love-600">
                    Maksimal 1GB per file (MP4, WebM, MOV, AVI, MKV)
                </p>
            </div>
        @else
            <p class="text-lg font-semibold text-love-900">
                {{ count($videos) }} video dipilih
            </p>
        @endif
    </div>

    <!-- Video Previews -->
    @if(count($videos) > 0)
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($videos as $index => $video)
                <div class="relative bg-white rounded-xl shadow-md overflow-hidden">
                    <video
                        src="{{ $video->temporaryUrl() }}"
                        class="w-full h-48 object-cover"
                        controls
                    ></video>
                    
                    <!-- Remove Button -->
                    <button
                        wire:click="removeVideo({{ $index }})"
                        class="absolute top-2 right-2 p-2 bg-red-500 text-white rounded-full hover:bg-red-600 focus:outline-none transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Caption Input -->
                    <div class="p-4">
                        <input
                            type="text"
                            wire:model="captions.{{ $index }}"
                            placeholder="Tambahkan caption..."
                            class="w-full px-3 py-2 border border-love-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-love-500 focus:border-transparent"
                        />
                        <p class="mt-1 text-xs text-love-600">
                            {{ $video->getClientOriginalName() }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Progress Bar -->
        @if($isUploading)
            <div class="mt-6">
                <div class="flex justify-between text-sm text-love-700 mb-2">
                    <span>Mengupload video...</span>
                    <span>{{ $uploadProgress }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div 
                        class="bg-love-500 h-2.5 rounded-full transition-all duration-300" 
                        style="width: {{ $uploadProgress }}%"
                    ></div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="mt-6 flex gap-4">
            <button
                wire:click="confirmUpload"
                wire:loading.attr="disabled"
                class="flex-1 px-6 py-3 bg-gradient-to-r from-love-500 to-love-600 text-white font-semibold rounded-xl shadow-lg hover:from-love-600 hover:to-love-700 focus:outline-none focus:ring-2 focus:ring-love-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span wire:loading wire:target="upload" class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Mengupload...
                </span>
                <span wire:loading.remove wire:target="upload">
                    Upload Video
                </span>
            </button>
            <button
                wire:click="resetForm"
                class="px-6 py-3 border border-love-300 text-love-700 font-semibold rounded-xl hover:bg-love-50 focus:outline-none focus:ring-2 focus:ring-love-500 transition-all"
            >
                Reset
            </button>
        </div>
    @endif
</div>
