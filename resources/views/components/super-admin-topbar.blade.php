<header class="bg-love-500 shadow-sm sm:hidden">
    <div class="px-4 py-4">
        <div class="flex items-center justify-between">
            <!-- Mobile Toggle -->
            <x-sidebar-mobile-toggle />

            <!-- Page Title -->
            <h1 class="text-lg font-semibold text-white">
                {{ $title ?? config('app.name', 'Laravel') }}
            </h1>

            <!-- Spacer -->
            <div class="w-10"></div>
        </div>
    </div>
</header>
