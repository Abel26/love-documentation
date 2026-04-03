<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Love Documentation') }} - Love Gallery</title>

        <!-- Phosphor Icons CDN -->
        <script src="https://unpkg.com/@phosphor-icons/web"></script>

        <!-- Leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
              integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
              crossorigin=""/>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-genz-layered text-text-primary selection:bg-lavender-300 selection:text-text-primary min-h-screen">
        <!-- Floating Hearts Background - Gen-Z Style -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
            <div class="absolute top-10 left-10 text-teal-300/20 text-4xl float-animation" style="animation-delay: 0s;">💕</div>
            <div class="absolute top-20 right-20 text-lavender-300/20 text-3xl float-animation" style="animation-delay: 1s;">💖</div>
            <div class="absolute bottom-20 left-20 text-yellow-300/20 text-5xl float-animation" style="animation-delay: 2s;">💗</div>
            <div class="absolute bottom-40 right-10 text-coral-300/20 text-2xl float-animation" style="animation-delay: 1.5s;">💝</div>
            <div class="absolute top-1/2 left-5 text-teal-300/20 text-3xl float-animation" style="animation-delay: 0.5s;">💞</div>
            <div class="absolute top-1/3 right-5 text-lavender-300/20 text-4xl float-animation" style="animation-delay: 2.5s;">💖</div>
        </div>

        <!-- User Navbar -->
        @include('components.user-navbar')

        <!-- Page Content -->
        <main class="relative z-10 pt-4 pb-12 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        <!-- User Footer -->
        @include('components.user-footer')

        <!-- Livewire Scripts -->
        @livewireScripts

        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                crossorigin=""></script>

        <!-- Cursor Love Effect -->
        <script src="{{ asset('js/cursor-love.js') }}"></script>
    </body>
</html>
