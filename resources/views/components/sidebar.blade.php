<div x-data="{ open: false }"
     x-init="window.addEventListener('toggle-sidebar', () => open = !open)"
     class="relative">
    
    <!-- Sidebar Overlay (Mobile Only) -->
    <div x-show="open"
         x-transition:enter="transition-opacity duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 sm:hidden"
         @click="open = false"
         style="display: none;">
    </div>

    <!-- Sidebar -->
    <aside :class="[
        'fixed inset-y-0 left-0 z-50 w-64 bg-love-900 flex flex-col transform transition-transform duration-300 ease-in-out sm:translate-x-0',
        open ? 'translate-x-0' : '-translate-x-full'
    ]">
        <!-- Logo Section -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-love-800 flex-shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="w-8 h-8 bg-love-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-white">
                    {{ config('app.name', 'Laravel') }}
                </span>
            </a>

            <!-- Close Button (Mobile Only) -->
            <button @click="open = false"
                    class="sm:hidden p-1 rounded-lg text-love-200 hover:text-white hover:bg-love-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'bg-love-800 text-white border-l-4 border-love-400' : 'text-love-100 hover:bg-love-800 hover:text-white border-l-4 border-transparent' }} flex items-center gap-3 px-4 py-3 transition-all duration-200 group">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="font-medium text-sm">Dashboard</span>
                @if(request()->routeIs('dashboard'))
                    <span class="ml-auto w-1.5 h-1.5 bg-love-400 rounded-full"></span>
                @endif
            </a>

            <!-- Cloud Dashboard (Super Admin Only) -->
            @if(auth()->check() && auth()->user()->isSuperAdmin())
                <a href="{{ route('cloud-dashboard.index') }}"
                   class="{{ request()->routeIs('cloud-dashboard.*') ? 'bg-love-800 text-white border-l-4 border-love-400' : 'text-love-100 hover:bg-love-800 hover:text-white border-l-4 border-transparent' }} flex items-center gap-3 px-4 py-3 transition-all duration-200 group">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1 0H7a4 4 0 00-4-4V3a3 3 0 013-3h1m-1 8a2 2 0 012 2h6a2 2 0 012-2v-6a2 2 0 00-2-2H7a2 2 0 00-2 2v6z" />
                    </svg>
                    <span class="font-medium text-sm">Cloud Dashboard</span>
                    @if(request()->routeIs('cloud-dashboard.*'))
                        <span class="ml-auto w-1.5 h-1.5 bg-love-400 rounded-full"></span>
                    @endif
                </a>

                <!-- Galeri Foto (Super Admin Only) -->
                <a href="{{ route('images.index') }}"
                   class="{{ request()->routeIs('images.index') ? 'bg-love-800 text-white border-l-4 border-love-400' : 'text-love-100 hover:bg-love-800 hover:text-white border-l-4 border-transparent' }} flex items-center gap-3 px-4 py-3 transition-all duration-200 group">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium text-sm">Galeri Foto</span>
                    @if(request()->routeIs('images.index'))
                        <span class="ml-auto w-1.5 h-1.5 bg-love-400 rounded-full"></span>
                    @endif
                </a>

                <!-- Galeri Video (Super Admin Only) -->
                <a href="{{ route('videos.index') }}"
                   class="{{ request()->routeIs('videos.index') ? 'bg-love-800 text-white border-l-4 border-love-400' : 'text-love-100 hover:bg-love-800 hover:text-white border-l-4 border-transparent' }} flex items-center gap-3 px-4 py-3 transition-all duration-200 group">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium text-sm">Galeri Video</span>
                    @if(request()->routeIs('videos.index'))
                        <span class="ml-auto w-1.5 h-1.5 bg-love-400 rounded-full"></span>
                    @endif
                </a>
            @endif

            <!-- Profile -->
            <a href="{{ route('profile.edit') }}"
               class="{{ request()->routeIs('profile.*') ? 'bg-love-800 text-white border-l-4 border-love-400' : 'text-love-100 hover:bg-love-800 hover:text-white border-l-4 border-transparent' }} flex items-center gap-3 px-4 py-3 transition-all duration-200 group">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="font-medium text-sm">Profile</span>
                @if(request()->routeIs('profile.*'))
                    <span class="ml-auto w-1.5 h-1.5 bg-love-400 rounded-full"></span>
                @endif
            </a>
        </nav>

        <!-- User Profile Section -->
        <x-sidebar-user-profile />
    </aside>
</div>
