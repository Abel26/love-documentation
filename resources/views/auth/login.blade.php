<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Love Documentation</title>
    @vite('resources/css/app.css')
    <style>
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.1); }
            50% { transform: scale(1); }
            75% { transform: scale(1.1); }
        }
        .heart-beat {
            animation: heartbeat 2s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-love-100 via-love-200 to-brown-light flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Card Login -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden transform transition-all hover:scale-[1.02] duration-300">
            <!-- Header dengan background gradient -->
            <div class="bg-gradient-to-r from-love-500 to-love-600 px-8 py-10 text-center relative overflow-hidden">
                <!-- Dekorasi hati kecil di background -->
                <div class="absolute top-4 left-4 text-love-400 text-2xl opacity-30">♥</div>
                <div class="absolute top-8 right-8 text-love-400 text-xl opacity-30">♥</div>
                <div class="absolute bottom-4 left-8 text-love-400 text-lg opacity-30">♥</div>
                <div class="absolute bottom-8 right-4 text-love-400 text-2xl opacity-30">♥</div>

                <!-- Ikon Hati Besar -->
                <div class="heart-beat inline-block mb-4">
                    <svg class="w-20 h-20 mx-auto text-white drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold text-white mb-2">Love Documentation</h1>
                <p class="text-love-100 text-sm">Masuk untuk melihat kisah kita</p>
            </div>

            <!-- Form Content -->
            <div class="px-8 py-8">
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-3 bg-love-50 border border-love-200 rounded-lg text-love-700 text-sm text-center">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Username Field -->
                    <div class="mb-6">
                        <label for="username" class="block text-love-900 font-semibold mb-2 text-sm">
                            Username
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-love-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input
                                id="username"
                                type="text"
                                name="username"
                                value="{{ old('username') }}"
                                class="w-full pl-10 pr-4 py-3 border-2 border-love-200 rounded-xl focus:outline-none focus:border-love-500 focus:ring-2 focus:ring-love-200 transition-all duration-200 text-love-900 placeholder-love-300"
                                placeholder="Masukkan username"
                                required
                                autofocus
                                autocomplete="username"
                            >
                        </div>
                        @error('username')
                            <p class="mt-2 text-love-600 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="mb-6">
                        <label for="password" class="block text-love-900 font-semibold mb-2 text-sm">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-love-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                class="w-full pl-10 pr-4 py-3 border-2 border-love-200 rounded-xl focus:outline-none focus:border-love-500 focus:ring-2 focus:ring-love-200 transition-all duration-200 text-love-900 placeholder-love-300"
                                placeholder="Masukkan password"
                                required
                                autocomplete="current-password"
                            >
                        </div>
                        @error('password')
                            <p class="mt-2 text-love-600 text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-6">
                        <label for="remember" class="flex items-center cursor-pointer group">
                            <input
                                id="remember"
                                type="checkbox"
                                name="remember"
                                class="w-4 h-4 text-love-600 border-2 border-love-300 rounded focus:ring-love-500 focus:ring-offset-0 transition-all duration-200"
                            >
                            <span class="ml-2 text-sm text-love-700 group-hover:text-love-900 transition-colors duration-200">
                                Ingat saya
                            </span>
                        </label>
                    </div>

                    <!-- Login Button -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-love-500 to-love-600 hover:from-love-600 hover:to-love-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        <span>Masuk</span>
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="px-8 py-4 bg-love-50 border-t border-love-100 text-center">
                <p class="text-love-600 text-sm flex items-center justify-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                    </svg>
                    Dibuat dengan cinta untuk kita berdua
                </p>
            </div>
        </div>

        <!-- Dekorasi hati mengambang -->
        <div class="absolute top-20 left-10 text-love-300 text-4xl float-animation opacity-50">♥</div>
        <div class="absolute bottom-20 right-10 text-brown-soft text-3xl float-animation opacity-50" style="animation-delay: 1s;">♥</div>
        <div class="absolute top-40 right-20 text-love-200 text-2xl float-animation opacity-50" style="animation-delay: 2s;">♥</div>
        <div class="absolute bottom-40 left-20 text-brown-light text-3xl float-animation opacity-50" style="animation-delay: 1.5s;">♥</div>
    </div>
</body>
</html>
