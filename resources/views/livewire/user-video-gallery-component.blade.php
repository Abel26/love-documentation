@component('layouts.user')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-pastel-lavender/30 overflow-hidden shadow-genz-lavender sm:rounded-3xl p-8 border-4 border-dashed border-lavender-200 text-center">
                <i class="ph-fill ph-video-camera text-7xl text-lavender-400 mb-6 animate-bounce"></i>
                <h2 class="text-3xl font-poppins font-extrabold text-lavender-800 mb-4">Video Gallery</h2>
                <p class="text-lavender-600 text-lg font-medium">
                    Halaman video khusus sedang dipersiapkan. Untuk sementara, lihat video-video seru kita di halaman utama <a href="{{ route('user.gallery') }}" class="text-teal-500 font-bold underline hover:text-teal-600">Gallery</a>! 💖
                </p>
            </div>
        </div>
    </div>
@endcomponent
