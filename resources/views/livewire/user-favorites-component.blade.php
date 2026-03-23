@component('layouts.user')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-pastel-yellow/30 overflow-hidden shadow-xl sm:rounded-3xl p-8 border-4 border-dashed border-yellow-200 text-center">
                <i class="ph-fill ph-star text-7xl text-yellow-400 mb-6 animate-bounce"></i>
                <h2 class="text-3xl font-poppins font-extrabold text-yellow-800 mb-4">Favorites</h2>
                <p class="text-yellow-700 text-lg font-medium">
                    Halaman favorit sedang dipersiapkan. Untuk sementara, lihat semua memori seru kita di halaman utama <a href="{{ route('user.gallery') }}" class="text-teal-500 font-bold underline hover:text-teal-600">Gallery</a>! ⭐
                </p>
            </div>
        </div>
    </div>
@endcomponent
