<div class="mt-auto pt-6 border-t border-love-800">
    <div class="px-4 pb-4">
        <!-- User Info -->
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-love-700 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-love-200 truncate">
                    {{ auth()->user()->email }}
                </p>
            </div>
        </div>

        <!-- Logout Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-love-700 hover:bg-love-600 text-white text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-love-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>Log Out</span>
            </button>
        </form>
    </div>
</div>
