<div class="space-y-12 pb-24 font-poppins">
    {{-- Hero Section --}}
    <div class="text-center pt-8">
        <h1 class="text-5xl md:text-7xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-brown-medium to-brown-soft animate-fade-in tracking-tight font-playfair drop-shadow-sm">
            MEMORIES OF US
        </h1>
        <p class="mt-4 text-brown-medium font-medium text-lg md:text-xl animate-slide-in-up">
            Perjalanan cinta kita yang tak terlupakan ✨🤎
        </p>
    </div>

    {{-- Memory of the Day Card (Playful) --}}
    @if($featuredImage)
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-pastel-lavender rounded-3xl shadow-genz-lavender p-6 md:p-8 border-2 border-lavender-200 transform hover:-translate-y-2 hover:rotate-1 transition-all duration-300 cursor-love">
            <div class="flex flex-col md:flex-row items-center gap-6 md:gap-8">
                <div class="w-full md:w-1/3">
                    <img src="{{ Storage::url($featuredImage->path) }}" alt="Featured Memory" class="w-full h-48 object-cover rounded-2xl shadow-lg -rotate-3 hover:rotate-0 transition-all duration-300 border-4 border-white">
                </div>
                <div class="w-full md:w-2/3 text-center md:text-left">
                    <div class="inline-block px-4 py-1.5 bg-lavender-500 text-white rounded-full text-xs font-bold uppercase tracking-wider mb-4 shadow-sm">
                        #MemoryOfTheDay 💖
                    </div>
                    @if($featuredImage->caption)
                        <h2 class="text-2xl md:text-3xl font-playfair font-bold text-lavender-900 leading-tight">
                            "{{ $featuredImage->caption }}"
                        </h2>
                    @else
                        <h2 class="text-2xl md:text-3xl font-playfair font-bold text-lavender-900 leading-tight">
                            "Setiap detik bersamamu adalah anugerah terindah."
                        </h2>
                    @endif
                    <p class="mt-4 text-lavender-700 font-medium text-sm">
                        Diabadikan pada {{ \Carbon\Carbon::parse($featuredImage->event_date ?? $featuredImage->created_at)->translatedFormat('d F Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Stats Tokens --}}
    <div class="flex flex-wrap justify-center gap-6 px-4">
        <div class="stats-token stats-token-teal w-48 hover:-rotate-2">
            <div class="flex flex-col items-center">
                <span class="text-xs font-bold uppercase tracking-wider text-teal-100 mb-1">Total Foto</span>
                <span class="stats-number text-5xl">{{ $statistics['total_photos'] ?? 0 }}</span>
            </div>
            <i class="ph-fill ph-camera stats-icon opacity-80 text-5xl drop-shadow-md"></i>
        </div>

        <div class="stats-token stats-token-yellow w-48 hover:rotate-2">
            <div class="flex flex-col items-center">
                <span class="text-xs font-bold uppercase tracking-wider text-yellow-100 mb-1">Total Video</span>
                <span class="stats-number text-5xl">{{ $statistics['total_videos'] ?? 0 }}</span>
            </div>
            <i class="ph-fill ph-video-camera stats-icon opacity-80 text-5xl drop-shadow-md"></i>
        </div>
    </div>

    {{-- Unified Date Filter Dropdown --}}
    <div class="max-w-6xl mx-auto px-4 mt-4" x-data="{ openFilter: false }">
        <div class="flex justify-center md:justify-start relative">
            
            {{-- Filter Button --}}
            <button 
                @click="openFilter = !openFilter"
                @click.away="openFilter = false"
                class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-brown-medium to-brown-soft text-white font-bold rounded-full shadow-lg hover:shadow-brown-medium/50 hover:-translate-y-1 transition-all duration-300 ring-4 ring-brown-light/30">
                <i class="ph-bold ph-calendar-heart text-xl"></i>
                Filter Memori 🤎
                <i class="ph-bold ph-caret-down text-sm transition-transform duration-300" :class="openFilter ? 'rotate-180' : ''"></i>
            </button>
            
            {{-- Dropdown Container --}}
            <div 
                x-show="openFilter" 
                style="display: none;"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
                class="absolute top-full mt-4 left-0 md:left-auto w-[320px] bg-white/95 backdrop-blur-xl border-2 border-brown-light rounded-[2rem] shadow-2xl p-6 z-50">
                
                <h3 class="font-bold text-brown-dark border-b border-brown-light pb-2 mb-4">Cari Waktu Spesial Kita</h3>
                
                <div class="space-y-4">
                    {{-- Select Hari --}}
                    <div>
                        <label class="block text-xs font-bold text-brown-medium uppercase tracking-widest mb-1.5"><i class="ph-fill ph-sun"></i> Tanggal</label>
                        <select wire:model="selectedDay" class="w-full bg-love-50 border-brown-light text-brown-dark rounded-xl px-4 py-2.5 focus:ring-brown-soft focus:border-brown-soft font-medium cursor-pointer transition-all">
                            <option value="">Semua Tanggal</option>
                            @for($i = 1; $i <= 31; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Select Bulan --}}
                    <div>
                        <label class="block text-xs font-bold text-brown-medium uppercase tracking-widest mb-1.5"><i class="ph-fill ph-moon"></i> Bulan</label>
                        <select wire:model="selectedMonth" class="w-full bg-love-50 border-brown-light text-brown-dark rounded-xl px-4 py-2.5 focus:ring-brown-soft focus:border-brown-soft font-medium cursor-pointer transition-all">
                            <option value="">Semua Bulan</option>
                            @foreach([
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', 
                                '04' => 'April', '05' => 'Mei', '06' => 'Juni', 
                                '07' => 'Juli', '08' => 'Agustus', '09' => 'September', 
                                '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                            ] as $num => $name)
                                <option value="{{ $num }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Select Tahun --}}
                    <div>
                        <label class="block text-xs font-bold text-brown-medium uppercase tracking-widest mb-1.5"><i class="ph-fill ph-star"></i> Tahun</label>
                        <select wire:model="selectedYear" class="w-full bg-love-50 border-brown-light text-brown-dark rounded-xl px-4 py-2.5 focus:ring-brown-soft focus:border-brown-soft font-medium cursor-pointer transition-all">
                            <option value="">Semua Tahun</option>
                            @if(isset($availableYears) && count($availableYears) > 0)
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            @else
                                <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <button wire:click="resetFilters" class="w-full py-2.5 text-brown-medium hover:text-white hover:bg-brown-soft bg-love-50 font-bold rounded-xl transition-colors border border-brown-light">
                        Hapus Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- The Gallery Grids --}}
    <div class="max-w-6xl mx-auto px-4 pb-12" x-data="{ videoModalOpen: false, currentVideo: '', currentTitle: '', currentDate: '' }" @open-video-modal.window="videoModalOpen = true; currentVideo = $event.detail.url; currentTitle = $event.detail.title; currentDate = $event.detail.date">
        @if(($imageGroups && $imageGroups->isNotEmpty()) || ($videos && $videos->isNotEmpty()))
            
            {{-- PHOTOS SECTION --}}
            @if($imageGroups && $imageGroups->isNotEmpty())
                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 rounded-full bg-love-100 flex items-center justify-center text-brown-medium shadow-inner">
                            <i class="ph-fill ph-camera text-2xl"></i>
                        </div>
                        <h2 class="text-3xl font-extrabold text-brown-dark tracking-tight font-playfair">Koleksi Foto</h2>
                        <div class="h-px bg-gradient-to-r from-brown-light to-transparent flex-1 ml-4 shadow-sm"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($imageGroups as $group)
                            @if($group->images->isNotEmpty())
                                <div class="bg-white/95 backdrop-blur-md rounded-[2.5rem] overflow-hidden shadow-lg border-2 border-brown-light flex flex-col hover:-translate-y-2 hover:shadow-brown-soft/40 transition-all duration-300 cursor-pointer relative group" onclick="window.location.href='{{ route('image-groups.show', $group->uuid) }}'">
                                    
                                    {{-- Thumbnail/Cover --}}
                                    <div class="relative w-full aspect-square sm:aspect-[4/3] overflow-hidden bg-love-50 shrink-0">
                                        <img src="{{ Storage::url($group->images->first()->path) }}" alt="{{ $group->caption ?? 'Album Memori' }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                        
                                        {{-- Stack Badge for Multiple Photos --}}
                                        @if($group->images->count() > 1)
                                            <div class="absolute top-4 right-4 bg-gradient-to-r from-brown-medium to-brown-soft text-white px-4 py-1.5 rounded-full flex items-center gap-1.5 shadow-lg border border-white/30 z-10 font-bold tracking-wide">
                                                <i class="ph-fill ph-images text-lg"></i>
                                                <span class="text-xs">{{ $group->images->count() }} Foto</span>
                                            </div>
                                        @elseif($group->images->count() == 1)
                                            <div class="absolute top-4 right-4 bg-gradient-to-r from-brown-soft to-brown-light text-white px-4 py-1.5 rounded-full flex items-center gap-1.5 shadow-md border border-white/30 z-10">
                                                <i class="ph-fill ph-image text-lg"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Album Info --}}
                                    <div class="p-6 bg-white flex flex-col grow justify-between">
                                        <h3 class="font-bold text-xl text-brown-dark line-clamp-2 md:line-clamp-1 mb-2 group-hover:text-brown-medium transition-colors font-playfair">
                                            {{ $group->caption ?? 'Album Kenangan Indah' }}
                                        </h3>
                                        <p class="font-mono text-sm text-brown-medium flex items-center gap-1.5 font-bold">
                                            <i class="ph-fill ph-calendar-heart text-brown-soft"></i>
                                            {{ \Carbon\Carbon::parse($group->event_date)->translatedFormat('d F Y') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- VIDEOS SECTION --}}
            @if($videos && $videos->isNotEmpty())
                <div>
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 rounded-full bg-love-100 flex items-center justify-center text-brown-medium shadow-inner">
                            <i class="ph-fill ph-video-camera text-2xl"></i>
                        </div>
                        <h2 class="text-3xl font-extrabold text-brown-dark tracking-tight font-playfair">Koleksi Video</h2>
                        <div class="h-px bg-gradient-to-r from-brown-light to-transparent flex-1 ml-4 shadow-sm"></div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($videos as $video)
                            <div class="bg-white/95 backdrop-blur-md rounded-[2.5rem] overflow-hidden shadow-lg border-2 border-brown-light flex flex-col hover:-translate-y-2 hover:shadow-brown-soft/40 transition-all duration-300 cursor-pointer relative group" 
                                 @click="$dispatch('open-video-modal', { 
                                     url: '{{ Storage::url($video->path) }}', 
                                     title: '{{ addslashes($video->caption ?? 'Video Kenangan') }}', 
                                     date: '{{ \Carbon\Carbon::parse($video->upload_date)->translatedFormat('d F Y') }}' 
                                 })">
                                
                                {{-- Video Cover --}}
                                <div class="relative w-full aspect-square sm:aspect-[4/3] overflow-hidden bg-brown-dark flex items-center justify-center shrink-0">
                                    <video src="{{ Storage::url($video->path) }}" class="absolute inset-0 w-full h-full object-cover opacity-80 transition-transform duration-700 group-hover:scale-105 group-hover:opacity-100" muted loop onmouseover="this.play()" onmouseout="this.pause()"></video>
                                    
                                    {{-- Play Icon Center --}}
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none group-hover:bg-black/10 transition-colors z-10">
                                        <div class="bg-white/90 p-4 rounded-full text-brown-medium shadow-[0_0_30px_rgba(212,165,116,0.4)] group-hover:scale-110 transition-transform flex items-center justify-center">
                                            <i class="ph-fill ph-play text-3xl ml-1"></i>
                                        </div>
                                    </div>
                                    
                                    {{-- Video Badge --}}
                                    <div class="absolute top-4 right-4 bg-gradient-to-r from-brown-soft to-brown-light text-white px-4 py-1.5 rounded-full flex items-center gap-1.5 shadow-lg border border-white/30 z-20 font-bold tracking-wide">
                                        <i class="ph-fill ph-film-strip text-lg"></i>
                                        <span class="text-xs">Video</span>
                                    </div>
                                </div>
                                
                                {{-- Video Info --}}
                                <div class="p-6 bg-white flex flex-col grow justify-between">
                                    <h3 class="font-bold text-xl text-brown-dark line-clamp-2 md:line-clamp-1 mb-2 group-hover:text-brown-medium transition-colors font-playfair">
                                        {{ $video->caption ?? 'Video Kenangan' }}
                                    </h3>
                                    <p class="font-mono text-sm text-brown-medium flex items-center gap-1.5 font-bold">
                                        <i class="ph-fill ph-calendar-heart text-brown-soft"></i>
                                        {{ \Carbon\Carbon::parse($video->upload_date)->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- YOUTUBE-STYLE VIDEO MODAL (Alpine.js) --}}
            <div x-show="videoModalOpen" 
                 style="display: none;" 
                 class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-love-900/90 backdrop-blur-md" @click="videoModalOpen = false; $refs.mainVideoPlayer.pause();"></div>
                
                {{-- Modal Content --}}
                <div class="relative bg-white rounded-3xl sm:rounded-[2.5rem] shadow-2xl overflow-hidden w-full max-w-5xl flex flex-col transform transition-all"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="scale-95 translate-y-8"
                     x-transition:enter-end="scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="scale-100 translate-y-0"
                     x-transition:leave-end="scale-95 translate-y-8"
                     @click.stop>
                    
                    {{-- Close Button --}}
                    <button @click="videoModalOpen = false; $refs.mainVideoPlayer.pause();" class="absolute top-4 right-4 sm:top-6 sm:right-6 z-10 bg-black/50 hover:bg-brown-medium text-white p-2.5 rounded-full backdrop-blur-sm transition-colors group">
                        <i class="ph-bold ph-x text-xl group-hover:rotate-90 transition-transform"></i>
                    </button>
                    
                    {{-- Video Player Container --}}
                    <div class="relative w-full bg-black aspect-video flex-shrink-0">
                        <video x-ref="mainVideoPlayer" :src="currentVideo" class="w-full h-full object-contain" controls autoplay playsinline controlsList="nodownload"></video>
                    </div>
                    
                    {{-- Video Details (YouTube Style) --}}
                    <div class="p-6 sm:p-8 bg-white">
                        <h2 class="text-2xl sm:text-3xl font-bold text-brown-dark mb-2 font-playfair" x-text="currentTitle"></h2>
                        <div class="flex items-center justify-between flex-wrap gap-4 border-b border-brown-light pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-brown-medium to-brown-soft flex items-center justify-center text-white shadow-md">
                                    <i class="ph-fill ph-heart text-xl animate-pulse-soft"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-brown-dark">Love Docs Memori</p>
                                    <p class="text-sm text-brown-medium font-mono" x-text="currentDate"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a :href="currentVideo" download class="bg-gradient-to-r from-brown-medium to-brown-soft text-white px-4 py-2 rounded-full font-bold text-sm flex items-center gap-2 hover:shadow-lg hover:-translate-y-1 transition-all shadow-brown-light">
                                    <i class="ph-bold ph-download-simple text-lg"></i> Download
                                </a>
                                <div class="bg-love-50 text-brown-dark px-4 py-2 rounded-full font-bold text-sm flex items-center gap-2 border border-brown-light">
                                    <i class="ph-fill ph-sparkle text-brown-soft"></i> Moments
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-brown-dark bg-love-50 p-4 rounded-2xl border border-brown-light">
                            <p class="font-medium">Video dokumenter dari momen spesial kita. 🤎</p>
                        </div>
                    </div>
                </div>
            </div>
            
        @else
            {{-- Empty State (Playful & No Buttons) --}}
            <div class="flex flex-col items-center justify-center p-16 mt-4 bg-pastel-lavender/30 rounded-[3rem] border-4 border-dashed border-lavender-200 transition-all hover:border-lavender-400 hover:bg-pastel-lavender/50">
                <div class="relative">
                    <i class="ph-fill ph-ghost text-7xl text-lavender-400 mb-6 animate-bounce"></i>
                    <i class="ph-fill ph-sparkle text-3xl text-yellow-400 absolute -top-2 -right-6 animate-pulse"></i>
                </div>
                <h3 class="text-3xl font-extrabold text-lavender-800 mb-3 tracking-tight">Oops! Masih Kosong...</h3>
                <p class="text-lavender-600 text-center font-medium text-lg leading-relaxed max-w-md">
                    Belum ada memori seru di bulan ini! 💖<br>Coba klik bulan lain di atas ya.
                </p>
            </div>
        @endif
    </div>
</div>
