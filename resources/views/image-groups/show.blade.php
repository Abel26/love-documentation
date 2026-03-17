<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $group->caption ?? 'Galeri Foto' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto py-8 px-4">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-love-500 to-love-600 px-6 py-8">
                <h1 class="text-3xl font-bold text-white mb-2">
                    {{ $group->caption ?? 'Galeri Foto' }}
                </h1>
                <p class="text-white/80 text-lg">
                    {{ $group->event_date->format('d M Y') }} • {{ $group->image_count }} foto
                </p>
            </div>
            
            <!-- Images Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($group->images as $image)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                            <img 
                                src="{{ asset('storage/' . $image->path) }}"
                                alt="{{ $image->original_filename }}"
                                class="w-full h-48 object-cover"
                            >
                        </div>
                    @endforeach
                </div>
                
                @if($group->images->isEmpty())
                    <div class="text-center py-16">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-gray-500 text-lg">Tidak ada foto dalam grup ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
