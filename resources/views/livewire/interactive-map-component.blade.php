<div>
    <!-- Header Section -->
    <section class="mb-6 fade-in">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="heart-beat">
                        <svg class="w-10 h-10 text-love-500" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Peta Kenangan Kita</h1>
                        <p class="text-gray-600 text-sm">Jejak perjalanan cinta kita di berbagai tempat 🗺️</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-love-600">{{ $imageGroups->count() }}</div>
                    <div class="text-gray-600 text-sm">Lokasi Kenangan</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="flex flex-wrap gap-3">
                <!-- Month Filter -->
                <select wire:model="selectedMonth"
                        class="px-4 py-2 border border-love-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-love-500 bg-white text-gray-700">
                    <option value="">Semua Bulan</option>
                    @foreach($availableMonths as $month)
                        <option value="{{ $month }}">{{ $month }}</option>
                    @endforeach
                </select>

                <!-- Year Filter -->
                <select wire:model="selectedYear"
                        class="px-4 py-2 border border-love-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-love-500 bg-white text-gray-700">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>

                <!-- Reset Filter -->
                @if($selectedMonth || $selectedYear)
                    <button wire:click="resetFilters"
                            class="px-4 py-2 bg-love-100 text-love-700 rounded-xl hover:bg-love-200 transition-colors flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Reset Filter</span>
                    </button>
                @endif
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="mb-6 fade-in" style="animation-delay: 0.2s;">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div id="interactive-map" class="h-96 md:h-[500px] w-full"></div>
        </div>
    </section>

    <!-- Location List Section -->
    <section class="mb-6 fade-in" style="animation-delay: 0.4s;">
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-love-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Daftar Lokasi
            </h2>

            @if($imageGroups->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-gray-500">Belum ada lokasi kenangan yang ditambahkan</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($imageGroups as $imageGroup)
                        <div class="border border-love-100 rounded-xl p-4 hover:shadow-lg transition-shadow cursor-pointer"
                             wire:click="showDetail('{{ $imageGroup->uuid }}')">
                            <!-- Thumbnail -->
                            @if($imageGroup->images->isNotEmpty())
                                <div class="relative mb-3">
                                    <img src="{{ asset('storage/' . $imageGroup->images->first()->path) }}"
                                         alt="{{ $imageGroup->name }}"
                                         class="w-full h-32 object-cover rounded-lg">
                                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-full text-xs font-semibold text-love-600">
                                        {{ $imageGroup->images->count() }} foto
                                    </div>
                                </div>
                            @endif

                            <!-- Location Info -->
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-love-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $imageGroup->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $this->getFormattedLocation($imageGroup->location_name, $imageGroup->location_address) }}</p>
                                    <p class="text-xs text-love-600 mt-1">{{ $imageGroup->event_date ? $imageGroup->event_date->format('d F Y') : 'Tanggal tidak tersedia' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedImageGroup)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             x-data="{ open: true }"
             x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="open = false; $wire.closeDetail()">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                 x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <!-- Header -->
                <div class="bg-gradient-to-r from-love-400 to-love-600 p-6 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-white">{{ $selectedImageGroup->name }}</h2>
                        <button wire:click="closeDetail"
                                class="text-white/80 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    @if($selectedImageGroup->caption)
                        <p class="text-white/90 mt-2">{{ $selectedImageGroup->caption }}</p>
                    @endif
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Location Info -->
                    <div class="flex items-center space-x-3 mb-4 p-4 bg-love-50 rounded-xl">
                        <svg class="w-6 h-6 text-love-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $this->getFormattedLocation($selectedImageGroup->location_name, $selectedImageGroup->location_address) }}</p>
                            <p class="text-sm text-gray-600">{{ $selectedImageGroup->latitude }}, {{ $selectedImageGroup->longitude }}</p>
                        </div>
                    </div>

                    <!-- Date Info -->
                    <div class="flex items-center space-x-3 mb-4 p-4 bg-love-50 rounded-xl">
                        <svg class="w-6 h-6 text-love-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $selectedImageGroup->event_date ? $selectedImageGroup->event_date->format('d F Y') : 'Tanggal tidak tersedia' }}</p>
                            <p class="text-sm text-gray-600">{{ $selectedImageGroup->event_month ?? '' }}</p>
                        </div>
                    </div>

                    <!-- Image Grid -->
                    <div class="mb-4">
                        <h3 class="font-semibold text-gray-800 mb-3">Foto Kenangan ({{ $selectedImageGroup->images->count() }})</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($selectedImageGroup->images as $image)
                                <img src="{{ asset('storage/' . $image->path) }}"
                                     alt="{{ $image->original_filename }}"
                                     class="w-full h-24 object-cover rounded-lg hover:scale-105 transition-transform cursor-pointer">
                            @endforeach
                        </div>
                    </div>

                    <!-- Action Button -->
                    <button wire:click="goToImageGroup('{{ $selectedImageGroup->uuid }}')"
                            class="w-full py-3 bg-love-500 text-white font-semibold rounded-xl hover:bg-love-600 transition-colors flex items-center justify-center space-x-2">
                        <span>Lihat Semua Foto</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Leaflet Map Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map
            const map = L.map('interactive-map').setView([{{ $mapCenterLat }}, {{ $mapCenterLng }}], {{ $mapZoom }});

            // Add tile layer (OpenStreetMap)
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Custom heart icon
            const heartIcon = L.divIcon({
                className: 'custom-heart-marker',
                html: `<svg class="w-8 h-8 text-love-500 heart-marker-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>`,
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            // Add markers for each image group
            const imageGroups = @js($imageGroups);

            imageGroups.forEach(function(group) {
                if (group.latitude && group.longitude) {
                    const marker = L.marker([group.latitude, group.longitude], {
                        icon: heartIcon
                    }).addTo(map);

                    // Create popup content
                    let popupContent = `
                        <div class="p-2 min-w-[200px]">
                            <h3 class="font-bold text-gray-800 mb-1">${group.name}</h3>
                            <p class="text-sm text-gray-600 mb-2">${group.location_name || group.location_address || 'Lokasi tidak diketahui'}</p>
                            <p class="text-xs text-love-600 mb-2">${group.event_date ? new Date(group.event_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : 'Tanggal tidak tersedia'}</p>
                            <button onclick="Livewire.dispatch('showDetail', { uuid: '${group.uuid}' })"
                                    class="w-full py-2 bg-love-500 text-white text-sm font-semibold rounded-lg hover:bg-love-600 transition-colors">
                                Lihat Detail
                            </button>
                        </div>
                    `;

                    marker.bindPopup(popupContent);
                }
            });

            // Fit bounds to show all markers
            if (imageGroups.length > 0) {
                const bounds = L.latLngBounds(
                    imageGroups
                        .filter(g => g.latitude && g.longitude)
                        .map(g => [g.latitude, g.longitude])
                );
                map.fitBounds(bounds, { padding: [50, 50] });
            }

            // Add heartbeat animation to markers
            const style = document.createElement('style');
            style.textContent = `
                .heart-marker-icon {
                    animation: marker-heartbeat 2s ease-in-out infinite;
                    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
                }
                @keyframes marker-heartbeat {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.2); }
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</div>
