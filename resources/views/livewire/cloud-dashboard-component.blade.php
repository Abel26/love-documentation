<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-love-900 font-hacker tracking-tight">
                <span class="text-love-500">></span> CLOUD DASHBOARD
            </h1>
            <p class="text-love-600 mt-1 font-mono text-sm">
                Storage Monitoring & Management System v1.0
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="refreshData" class="px-4 py-2 bg-love-500 hover:bg-love-600 text-white rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
            </button>
        </div>
    </div>

    <!-- Storage Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Image Storage Card -->
        <div class="hacker-card rounded-2xl p-6 border border-love-200/30">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-love-500 to-love-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-love-900 font-hacker">IMAGE STORAGE</h3>
                        <p class="text-xs text-love-600 font-mono">/uploads/images</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-love-100 text-love-900 rounded-full text-xs font-semibold font-mono">
                    {{ $imageStats['count'] }} files
                </span>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-love-700 font-mono">{{ $imageStats['total_size_formatted'] }}</span>
                    <span class="text-love-900 font-bold font-mono">{{ number_format($imageStats['percentage'], 1) }}%</span>
                </div>
                <div class="h-3 bg-love-900/10 rounded-full overflow-hidden relative">
                    <div class="h-full bg-gradient-to-r from-love-400 to-love-500 rounded-full transition-all duration-500 ease-out" style="width: {{ $imageStats['percentage'] }}%"></div>
                </div>
                <p class="text-xs text-love-600 mt-2 font-mono">
                    Available: {{ $imageStats['available_formatted'] }} / {{ $imageStats['max_storage_formatted'] }}
                </p>
            </div>

            <!-- Upload Button -->
            <button wire:click="openImageUploadModal" class="w-full py-3 bg-gradient-to-r from-love-500 to-love-600 hover:from-love-600 hover:to-love-700 text-white rounded-xl font-semibold transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Upload Images
            </button>
        </div>

        <!-- Video Storage Card -->
        <div class="hacker-card rounded-2xl p-6 border border-love-200/30">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-love-700 to-love-800 rounded-xl flex items-center justify-center shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-love-900 font-hacker">VIDEO STORAGE</h3>
                        <p class="text-xs text-love-600 font-mono">/uploads/videos</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-love-100 text-love-900 rounded-full text-xs font-semibold font-mono">
                    {{ $videoStats['count'] }} files
                </span>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-love-700 font-mono">{{ $videoStats['total_size_formatted'] }}</span>
                    <span class="text-love-900 font-bold font-mono">{{ number_format($videoStats['percentage'], 1) }}%</span>
                </div>
                <div class="h-3 bg-love-900/10 rounded-full overflow-hidden relative">
                    <div class="h-full bg-gradient-to-r from-love-600 to-love-700 rounded-full transition-all duration-500 ease-out" style="width: {{ $videoStats['percentage'] }}%"></div>
                </div>
                <p class="text-xs text-love-600 mt-2 font-mono">
                    Available: {{ $videoStats['available_formatted'] }} / {{ $videoStats['max_storage_formatted'] }}
                </p>
            </div>

            <!-- Upload Button -->
            <button wire:click="openVideoUploadModal" class="w-full py-3 bg-gradient-to-r from-love-700 to-love-800 hover:from-love-800 hover:to-love-900 text-white rounded-xl font-semibold transition-all duration-200 flex items-center justify-center gap-2 shadow-lg hover:shadow-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Upload Videos
            </button>
        </div>

        <!-- System Info Card -->
        <div class="hacker-card rounded-2xl p-6 border border-love-200/30">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-love-400 to-love-500 rounded-xl flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-love-900 font-hacker">SYSTEM INFO</h3>
                    <p class="text-xs text-love-600 font-mono">Server Status</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-love-700 font-mono">PHP Version</span>
                    <span class="text-sm font-bold text-love-900 font-mono">{{ $systemInfo['php_version'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-love-700 font-mono">Laravel</span>
                    <span class="text-sm font-bold text-love-900 font-mono">{{ $systemInfo['laravel_version'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-love-700 font-mono">Server Time</span>
                    <span class="text-sm font-bold text-love-900 font-mono">{{ $systemInfo['server_time'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-love-700 font-mono">Disk Usage</span>
                    <span class="text-sm font-bold text-love-900 font-mono">{{ number_format($systemInfo['disk_usage_percentage'], 1) }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Uploads Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Images -->
        <div class="hacker-card rounded-2xl p-6 border border-love-200/30">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-love-900 font-hacker flex items-center gap-2">
                    <span class="text-love-500">></span> RECENT IMAGES
                </h3>
                <a href="{{ route('images.index') }}" class="text-sm text-love-600 hover:text-love-900 font-mono transition-colors">
                    View All &rarr;
                </a>
            </div>
            @if($recentImages->count() > 0)
                <div class="grid grid-cols-5 gap-2">
                    @foreach($recentImages as $image)
                        <div class="aspect-square rounded-lg overflow-hidden bg-love-100 border border-love-200 group cursor-pointer">
                            <img src="{{ Storage::url($image->thumbnail_path) }}" alt="{{ $image->original_filename }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-love-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 text-love-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="font-mono text-sm">No images uploaded yet</p>
                </div>
            @endif
        </div>

        <!-- Recent Videos -->
        <div class="hacker-card rounded-2xl p-6 border border-love-200/30">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-love-900 font-hacker flex items-center gap-2">
                    <span class="text-love-500">></span> RECENT VIDEOS
                </h3>
                <a href="{{ route('videos.index') }}" class="text-sm text-love-600 hover:text-love-900 font-mono transition-colors">
                    View All &rarr;
                </a>
            </div>
            @if($recentVideos->count() > 0)
                <div class="grid grid-cols-5 gap-2">
                    @foreach($recentVideos as $video)
                        <div class="aspect-square rounded-lg overflow-hidden bg-love-100 border border-love-200 group cursor-pointer relative">
                            <img src="{{ Storage::url($video->thumbnail_path) }}" alt="{{ $video->original_filename }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-love-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 text-love-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <p class="font-mono text-sm">No videos uploaded yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Storage Trends -->
    <div class="hacker-card rounded-2xl p-6 border border-love-200/30">
        <h3 class="text-lg font-bold text-love-900 font-hacker flex items-center gap-2 mb-4">
            <span class="text-love-500">></span> STORAGE TRENDS (Last 7 Days)
        </h3>
        <div class="h-48 flex items-end justify-between gap-2">
            @foreach($storageTrends as $trend)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="w-full flex gap-1 h-32">
                        <div class="flex-1 bg-gradient-to-t from-love-400 to-love-500 rounded-t transition-all duration-300 hover:from-love-500 hover:to-love-600" style="height: {{ $trend['images']['count'] > 0 ? min(100, ($trend['images']['count'] * 20)) : 5 }}%"></div>
                        <div class="flex-1 bg-gradient-to-t from-love-700 to-love-800 rounded-t transition-all duration-300 hover:from-love-800 hover:to-love-900" style="height: {{ $trend['videos']['count'] > 0 ? min(100, ($trend['videos']['count'] * 20)) : 5 }}%"></div>
                    </div>
                    <span class="text-xs text-love-600 font-mono">{{ \Carbon\Carbon::parse($trend['date'])->format('d M') }}</span>
                </div>
            @endforeach
        </div>
        <div class="flex justify-center gap-6 mt-4">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-love-500 rounded"></div>
                <span class="text-sm text-love-700 font-mono">Images</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-love-800 rounded"></div>
                <span class="text-sm text-love-700 font-mono">Videos</span>
            </div>
        </div>
    </div>

    <!-- Image Upload Modal -->
    @if($showImageUploadModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-love-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-love-900 font-hacker">UPLOAD IMAGES</h2>
                        <button wire:click="closeImageUploadModal" class="p-2 hover:bg-love-100 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-love-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <livewire:cloud-image-upload-component :listenFor="'imageUploaded'" />
                </div>
            </div>
        </div>
    @endif

    <!-- Video Upload Modal -->
    @if($showVideoUploadModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-love-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-love-900 font-hacker">UPLOAD VIDEOS</h2>
                        <button wire:click="closeVideoUploadModal" class="p-2 hover:bg-love-100 rounded-lg transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-love-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <livewire:cloud-video-upload-component :listenFor="'videoUploaded'" />
                </div>
            </div>
        </div>
    @endif
</div>
