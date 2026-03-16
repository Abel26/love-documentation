<div>
    @if($images && count($images) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($images as $image)
                <div class="group relative bg-white rounded-2xl shadow-md overflow-hidden transform transition-all duration-300 hover:scale-105 hover:shadow-xl">
                    <!-- Image -->
                    <div class="relative aspect-square overflow-hidden bg-love-100">
                        <img
                            src="{{ $image->thumbnail_url }}"
                            alt="{{ $image->display_name }}"
                            loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                        />
                        
                        <!-- Overlay on hover -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="absolute bottom-0 left-0 right-0 p-4">
                                <p class="text-white text-sm font-medium truncate">
                                    {{ $image->display_name }}
                                </p>
                                @if($image->caption)
                                    <p class="text-white/80 text-xs mt-1 line-clamp-2">
                                        {{ $image->caption }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
 
                    <!-- Image Info -->
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-love-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs text-love-700">
                                    {{ $image->formatted_size }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-love-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs text-love-700">
                                    {{ $image->upload_date->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center justify-center space-x-3">
                            <!-- View Button -->
                            <button
                                wire:click="openImageModal('{{ $image->uuid }}')"
                                class="p-2 bg-love-100 text-love-600 rounded-lg hover:bg-love-200 transition-colors"
                                title="Lihat"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            
                            <!-- Edit Button -->
                            <button
                                wire:click="$emit('openEditModal', '{{ $image->uuid }}')"
                                class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors"
                                title="Edit"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            
                            <!-- Delete Button -->
                            <button
                                wire:click="$emit('openDeleteModal', '{{ $image->uuid }}')"
                                class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors"
                                title="Hapus"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
