<div>
    <!-- Memory of the Day Card -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden mb-8 transform hover:scale-[1.01] transition-all duration-500 border border-love-100">
        <div class="md:flex">
            <!-- Image Section -->
            <div class="md:w-1/2 relative bg-love-50 min-h-[300px] flex items-center justify-center overflow-hidden group">
                @if($memoryImage)
                    <img src="{{ asset('storage/' . $memoryImage->path) }}" 
                         alt="{{ $memoryCaption }}"
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60"></div>
                    
                    <!-- Floating Hearts Decoration -->
                    <div class="absolute top-4 left-4 text-white/30 text-3xl animate-bounce" style="animation-duration: 3s;">💕</div>
                    <div class="absolute bottom-4 right-4 text-white/30 text-3xl animate-pulse">💖</div>
                @else
                    <div class="text-love-200 flex flex-col items-center p-8">
                        <svg class="w-20 h-20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-love-400 font-medium italic text-center">Belum ada foto kenangan untuk hari ini</p>
                    </div>
                @endif
            </div>

            <!-- Content Section -->
            <div class="md:w-1/2 p-8 flex flex-col justify-between bg-gradient-to-br from-white to-love-50/30">
                <div>
                    <!-- Date Badge -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="inline-flex items-center px-4 py-1.5 bg-love-100 text-love-700 rounded-full text-sm font-semibold tracking-wide">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            {{ $memoryDate->format('d F Y') }}
                        </div>
                        <div class="text-love-300 font-serif italic text-sm">#MemoryOfTheDay</div>
                    </div>

                    <!-- Quote -->
                    <div class="relative mb-8 pt-4">
                        <svg class="absolute top-0 left-0 w-8 h-8 text-love-100 -translate-x-2 -translate-y-2" fill="currentColor" viewBox="0 0 32 32">
                            <path d="M10 8v8h6v-8h-6zM22 8v8h6v-8h-6z"/>
                        </svg>
                        <blockquote class="relative z-10">
                            <p class="text-xl sm:text-2xl font-serif text-love-900 leading-relaxed italic mb-3">
                                "{{ $memoryQuote->quote }}"
                            </p>
                            <cite class="text-love-500 font-medium block not-italic">— {{ $memoryQuote->author ?? 'Love Notes' }}</cite>
                        </blockquote>
                    </div>

                    <!-- Caption -->
                    @if($memoryCaption)
                        <div class="mb-8 p-4 bg-white/60 backdrop-blur-sm rounded-2xl border border-love-50 shadow-sm">
                            <p class="text-gray-700 leading-relaxed flex items-start">
                                <span class="text-love-400 mr-2 text-xl">✨</span>
                                {{ $memoryCaption }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Footer Actions -->
                <div class="space-y-4">
                    <!-- Navigation Buttons -->
                    <div class="flex items-center gap-2">
                        <button wire:click="loadPreviousMemory" 
                                class="flex-1 flex items-center justify-center px-4 py-2.5 bg-white border border-love-200 text-love-600 rounded-xl hover:bg-love-50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed group shadow-sm"
                                {{ !$showPrevious ? 'disabled' : '' }} title="Kenangan Sebelumnya">
                            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        
                        <button wire:click="loadRandomMemory" 
                                class="flex-[2] flex items-center justify-center px-4 py-2.5 bg-love-500 text-white rounded-xl hover:bg-love-600 transition-all duration-300 shadow-md shadow-love-200 font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Acak Momen
                        </button>

                        <button wire:click="loadNextMemory" 
                                class="flex-1 flex items-center justify-center px-4 py-2.5 bg-white border border-love-200 text-love-600 rounded-xl hover:bg-love-50 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed group shadow-sm"
                                {{ !$showNext ? 'disabled' : '' }} title="Kenangan Berikutnya">
                            <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Share & Download -->
                    <div class="flex items-center gap-2 pt-2">
                        <button wire:click="shareToWhatsApp" 
                                class="flex-1 flex items-center justify-center px-4 py-2.5 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-colors shadow-sm font-medium">
                            <svg class="w-5 h-5 mr-2 fill-current" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.835-.738-1.835h-3.535c-.617-.509-1.235-.296-.723-.296-1.623 0-2.695 0-3.582 0-3.582a3.176 3.176 0 01-1.801-3.601 1.125 1.125 0 00-1.801-1.801 1.125 1.125 0 001.801 1.801 1.125 1.125 0 001.801-1.801 1.125 1.125 0 00-1.801-1.801 1.125 1.125 0 013.601 1.801z"/>
                                <path d="M12.035 2c-5.523 0-10 4.477-10 10 0 1.763.456 3.419 1.254 4.864l-1.289 4.707 4.82-.1.264.445c1.464.887 3.167 1.396 4.981 1.396 5.522 0 10-4.477 10-10s-4.478-10-10-10zm.035 18.25c-1.628 0-3.21-.433-4.58-1.25l-.328-.198-2.84.593.603-2.76-.23-.366c-.887-1.414-1.354-3.056-1.354-4.735 0-4.832 3.931-8.763 8.764-8.763 4.832 0 8.763 3.931 8.763 8.763s-3.931 8.763-8.798 8.763z" />
                                <path d="M15.462 12.505c-.24-.12-1.424-.704-1.647-.785-.223-.08-.386-.12-.547.12-.162.24-.627.785-.768.946-.14.161-.282.181-.522.061-.24-.12-1.015-.374-1.933-1.19-.715-.638-1.197-1.425-1.338-1.666-.14-.24-.015-.37.106-.49.11-.108.24-.281.36-.422.12-.14.161-.24.241-.401.08-.162.04-.301-.02-.422-.06-.12-.547-1.32-.75-1.804-.197-.475-.398-.41-.547-.417-.14-.007-.301-.009-.462-.009-.161 0-.422.06-.643.301-.22.24-.844.824-.844 2.009 0 1.185.864 2.33 1.05 2.503.186.173 1.637 2.5 3.966 3.51.554.24 1.01.397 1.356.505.56.177 1.07.153 1.472.093.45-.067 1.424-.582 1.627-1.145.203-.563.203-1.045.142-1.145-.06-.101-.22-.161-.462-.281z" />
                            </svg>
                            WhatsApp
                        </button>
                        
                        <button wire:click="downloadMemory" 
                                class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors shadow-sm font-medium"
                                title="Download Foto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>