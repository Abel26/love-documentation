<nav x-data="{ open: false }" class="genz-navbar">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo - Gen-Z Style -->
            <div class="flex items-center">
                <a href="{{ route('user.gallery') }}" class="flex items-center space-x-2 group">
                    <div class="heart-beat">
                        <i class="ph-fill ph-heart text-coral-500 text-2xl group-hover:text-coral-600 transition-colors"></i>
                    </div>
                    <span class="text-xl font-extrabold text-love-900 group-hover:text-teal-600 transition-colors">
                        Love Docs
                    </span>
                </a>
            </div>

            <!-- Desktop Navigation Links - Gen-Z Color-Coded -->
            <div class="hidden sm:flex sm:items-center sm:space-x-6">
                <a href="{{ route('user.gallery') }}"
                   class="genz-nav-link {{ request()->routeIs('user.gallery') ? 'active-teal' : '' }}">
                    <i class="ph ph-image text-lg"></i>
                    Gallery
                </a>
                <a href="{{ route('user.map') }}"
                   class="genz-nav-link {{ request()->routeIs('user.map') ? 'active-teal' : '' }}">
                    <i class="ph ph-map-pin text-lg"></i>
                    Peta
                </a>
                <a href="{{ route('user.videos') }}"
                   class="genz-nav-link {{ request()->routeIs('user.videos') ? 'active-lavender' : '' }}">
                    <i class="ph ph-video text-lg"></i>
                    Videos
                </a>
                <a href="{{ route('user.favorites') }}"
                   class="genz-nav-link {{ request()->routeIs('user.favorites') ? 'active-yellow' : '' }}">
                    <i class="ph ph-heart text-lg"></i>
                    Favorites
                </a>
            </div>

            <!-- Settings Dropdown - Gen-Z Pill Style -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="relative">
                    <div @click.away="open = false" class="relative inline-block text-left">
                        <button @click="open = ! open"
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm leading-5 font-medium text-love-700 bg-white hover:bg-love-50 focus:outline-none focus:ring-2 focus:ring-teal-200 focus:border-teal-500 transition-all duration-300 shadow-md border border-love-200">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-coral-400 to-teal-500 flex items-center justify-center text-white font-bold shadow-sm">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="font-medium">{{ auth()->user()->name }}</span>
                            </div>
                            <i class="ph ph-caret-down ml-2 text-love-600"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
                             class="origin-top-right absolute right-0 mt-3 w-56 rounded-3xl shadow-2xl bg-white ring-1 ring-teal-100 focus:outline-none"
                             style="display: none;"
                             x-cloak>
                            <div class="py-3">
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center px-4 py-3 text-sm text-love-700 hover:bg-teal-50 hover:text-teal-700 transition-all duration-300 rounded-2xl mx-2">
                                    <i class="ph ph-user text-xl mr-3 text-teal-500"></i>
                                    Profile
                                </a>

                                <div class="border-t border-love-100 my-2 mx-2"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-all duration-300 rounded-2xl mx-2">
                                        <i class="ph ph-sign-out text-xl mr-3"></i>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-full text-love-700 bg-white hover:bg-love-50 focus:outline-none focus:ring-2 focus:ring-teal-200 transition-all duration-300 shadow-md border border-love-200">
                    <i class="ph ph-list text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform opacity-0 -translate-x-full"
             x-transition:enter-end="transform opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="transform opacity-100 translate-x-0"
             x-transition:leave-end="transform opacity-0 -translate-x-full"
             class="sm:hidden fixed inset-y-0 right-0 z-50 w-64 bg-white shadow-2xl border-l border-teal-200"
             style="display: none;"
             x-cloak>
            <div class="flex flex-col h-full py-6 px-4 space-y-4">
                <!-- Mobile Logo -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-2">
                        <i class="ph-fill ph-heart text-coral-500 text-2xl"></i>
                        <span class="text-xl font-extrabold text-love-900">Love Docs</span>
                    </div>
                    <button @click="open = false" class="p-2 rounded-full hover:bg-love-50 transition-colors">
                        <i class="ph ph-x text-2xl text-love-600"></i>
                    </button>
                </div>

                <!-- Mobile Navigation Links -->
                <a href="{{ route('user.gallery') }}"
                   class="flex items-center px-4 py-3 rounded-2xl text-love-700 hover:bg-teal-50 hover:text-teal-700 transition-all duration-300 {{ request()->routeIs('user.gallery') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="ph ph-image text-xl mr-3"></i>
                    Gallery
                </a>
                <a href="{{ route('user.map') }}"
                   class="flex items-center px-4 py-3 rounded-2xl text-love-700 hover:bg-teal-50 hover:text-teal-700 transition-all duration-300 {{ request()->routeIs('user.map') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="ph ph-map-pin text-xl mr-3"></i>
                    Peta
                </a>
                <a href="{{ route('user.videos') }}"
                   class="flex items-center px-4 py-3 rounded-2xl text-love-700 hover:bg-lavender-50 hover:text-lavender-700 transition-all duration-300 {{ request()->routeIs('user.videos') ? 'bg-lavender-50 text-lavender-700' : '' }}">
                    <i class="ph ph-video text-xl mr-3"></i>
                    Videos
                </a>
                <a href="{{ route('user.favorites') }}"
                   class="flex items-center px-4 py-3 rounded-2xl text-love-700 hover:bg-yellow-50 hover:text-yellow-700 transition-all duration-300 {{ request()->routeIs('user.favorites') ? 'bg-yellow-50 text-yellow-700' : '' }}">
                    <i class="ph ph-heart text-xl mr-3"></i>
                    Favorites
                </a>

                <!-- Mobile Divider -->
                <div class="border-t border-love-200 my-4"></div>

                <!-- Mobile Profile -->
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center px-4 py-3 rounded-2xl text-love-700 hover:bg-love-50 transition-all duration-300">
                    <i class="ph ph-user text-xl mr-3"></i>
                    Profile
                </a>

                <!-- Mobile Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 transition-all duration-300 rounded-2xl">
                        <i class="ph ph-sign-out text-xl mr-3"></i>
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Mobile Overlay -->
        <div x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false"
             class="sm:hidden fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
             style="display: none;"
             x-cloak></div>
    </div>
</nav>
