@component('layouts.user')
    <div class="space-y-8 pb-24 font-poppins pt-8">
        
        <div class="max-w-6xl mx-auto px-4">
            {{-- Back Button & Header --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <a href="{{ route('user.gallery') }}" class="inline-flex items-center gap-2 bg-white text-brown-medium hover:text-brown-dark font-bold px-6 py-3 rounded-full shadow-sm hover:shadow-brown-light border border-brown-light transition-all duration-300 w-max group">
                    <i class="ph-bold ph-arrow-left text-xl group-hover:-translate-x-1 transition-transform"></i>
                    Kembali ke Galeri
                </a>
            </div>

            {{-- Album Header Card --}}
            <div class="bg-gradient-to-br from-love-50 to-brown-light/20 rounded-[2.5rem] shadow-brown-soft/40 p-8 md:p-12 mb-12 border-2 border-brown-light relative overflow-hidden">
                <div class="absolute top-0 right-0 bg-brown-medium w-32 h-32 rounded-bl-full opacity-20 transform translate-x-10 -translate-y-10"></div>
                <div class="absolute bottom-0 left-0 bg-love-200 w-24 h-24 rounded-tr-full opacity-20 transform -translate-x-10 translate-y-10"></div>
                
                <div class="relative z-10 text-center max-w-2xl mx-auto">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-brown-dark tracking-tight font-playfair mb-4">
                        {{ $group->caption ?? 'Galeri Foto' }}
                    </h1>
                    <div class="flex flex-wrap items-center justify-center gap-4 text-brown-medium font-medium">
                        <span class="flex items-center gap-1.5 bg-white/60 px-4 py-1.5 rounded-full backdrop-blur-sm border border-white/40 shadow-sm">
                            <i class="ph-fill ph-calendar-heart text-lg text-brown-soft"></i>
                            {{ $group->event_date->translatedFormat('d F Y') }}
                        </span>
                        <span class="flex items-center gap-1.5 bg-white/60 px-4 py-1.5 rounded-full backdrop-blur-sm border border-white/40 shadow-sm">
                            <i class="ph-fill ph-camera text-lg text-brown-soft"></i>
                            {{ $group->images->count() }} Foto
                        </span>
                    </div>
                </div>
            </div>
            
            {{-- Images Grid --}}
            @if($group->images->isEmpty())
                <div class="flex flex-col items-center justify-center p-16 mt-4 bg-love-50 rounded-[3rem] border-4 border-dashed border-brown-light">
                    <i class="ph-fill ph-ghost text-7xl text-brown-medium mb-6 animate-bounce"></i>
                    <h3 class="text-3xl font-extrabold text-brown-dark mb-3 tracking-tight">Belum Ada Foto</h3>
                    <p class="text-brown-medium text-center font-medium text-lg max-w-md">
                        Album ini hangat sekali, namun sayangnya masih belum ada foto yang diabadikan di dalamnya! 🤎
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($group->images as $index => $image)
                        <div class="bg-white rounded-3xl overflow-hidden shadow-lg border border-brown-light hover:scale-[1.02] transition-transform duration-300 relative group cursor-pointer flex flex-col" onclick="window.open('{{ asset('storage/' . $image->path) }}', '_blank')">
                            
                            {{-- Image Container --}}
                            <div class="relative w-full aspect-square bg-love-50">
                                <img 
                                    src="{{ asset('storage/' . $image->path) }}"
                                    alt="Foto {{ $index + 1 }}"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:opacity-90 transition-opacity"
                                    loading="lazy"
                                >
                                
                                {{-- Hover Overlay --}}
                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none z-10">
                                    <div class="bg-white/90 p-3 rounded-full text-brown-medium shadow-xl transform scale-0 group-hover:scale-100 transition-transform">
                                        <i class="ph-bold ph-arrows-out text-2xl"></i>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Image Footer (Index and Download) --}}
                            <div class="absolute bottom-4 right-4 bg-white/90 backdrop-blur-md text-brown-dark px-4 py-2 rounded-full font-bold text-xs shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 z-20 flex items-center gap-3 border border-brown-light">
                                <span class="flex items-center">
                                    <i class="ph-fill ph-heart mr-1.5 text-brown-soft"></i>
                                    #{{ $index + 1 }}
                                </span>
                                <div class="w-px h-4 bg-brown-light"></div>
                                <a href="{{ asset('storage/' . $image->path) }}" download class="text-brown-medium hover:text-brown-dark hover:scale-110 transition-all" onclick="event.stopPropagation()" title="Download Foto ini">
                                    <i class="ph-bold ph-download-simple text-lg"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endcomponent
