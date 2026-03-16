<x-super-admin-layout>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex items-center gap-3 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-love-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <h2 class="text-xl font-semibold text-love-900">
                    Selamat Datang, {{ auth()->user()->name }}!
                </h2>
            </div>
            <p class="text-love-700 mb-6">
                Anda telah berhasil login ke sistem. Berikut adalah informasi akun Anda:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- User Info Card -->
                <div class="bg-love-50 rounded-xl p-6 border border-love-100">
                    <div class="flex items-center gap-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-love-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h3 class="font-semibold text-love-900">Informasi Akun</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-love-600">Nama:</span>
                            <span class="font-medium text-love-900">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-love-600">Email:</span>
                            <span class="font-medium text-love-900">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-love-600">Username:</span>
                            <span class="font-medium text-love-900">{{ auth()->user()->username }}</span>
                        </div>
                    </div>
                </div>

                <!-- Role Info Card -->
                <div class="bg-love-50 rounded-xl p-6 border border-love-100">
                    <div class="flex items-center gap-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-love-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <h3 class="font-semibold text-love-900">Role Akses</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-love-600">Role:</span>
                            <span class="font-medium text-love-900">
                                @if(auth()->user()->isSuperAdmin())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-love-500 text-white">
                                        Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-love-200 text-love-900">
                                        User
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-love-600">Status:</span>
                            <span class="font-medium text-green-600">Aktif</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-love-50 rounded-xl p-6 border border-love-100">
                    <div class="flex items-center gap-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-love-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <h3 class="font-semibold text-love-900">Aksi Cepat</h3>
                    </div>
                    <div class="space-y-2">
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('images.index') }}" class="block w-full text-center px-4 py-2 bg-love-500 hover:bg-love-600 text-white rounded-lg text-sm font-medium transition-colors">
                                Kelola Galeri Foto
                            </a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="block w-full text-center px-4 py-2 bg-white hover:bg-love-100 text-love-900 rounded-lg text-sm font-medium border border-love-200 transition-colors">
                            Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
