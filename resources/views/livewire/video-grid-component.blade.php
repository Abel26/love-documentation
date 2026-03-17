<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($videos as $video)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl cursor-pointer group">
                <!-- Thumbnail -->
                <div class="relative aspect-video bg-gray-100" wire:click="$emit('openVideoModal', '{{ $video->uuid }}')">
                    <img
                        src="{{ $video->thumbnail_url }}"
                        alt="{{ $video->display_name }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    />
                    <!-- Play Icon Overlay -->
                    <div class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="w-16 h-16 bg-white/90 rounded-full flex items-center justify-center shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-love-600 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 5v14l11-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Video Info -->
                <div class="p-4">
                    <h3 class="text-sm font-semibold text-love-900 truncate mb-2">
                        {{ $video->display_name }}
                    </h3>
                    <div class="flex items-center justify-between text-xs text-love-600">
                        <span>{{ $video->formatted_size }}</span>
                        <span>{{ $video->upload_date->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(count($videos) === 0)
        <div class="text-center py-12">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-love-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            <p class="text-lg font-semibold text-love-900">Tidak ada video</p>
            <p class="text-love-600">Mulai dengan mengupload video pertama Anda</p>
        </div>
    @endif
</div>
