@props(['href', 'active' => false, 'label', 'icon'])

@php
    $classes = $active
        ? 'bg-love-800 text-white border-l-4 border-love-400'
        : 'text-love-100 hover:bg-love-800 hover:text-white border-l-4 border-transparent';
@endphp

<a href="{{ $href }}"
   class="{{ $classes }} flex items-center gap-3 px-4 py-3 transition-all duration-200 group">
    @if(isset($icon))
        <span class="w-5 h-5 flex-shrink-0">
            {!! $icon !!}
        </span>
    @endif
    
    <span class="font-medium text-sm">
        {{ $label }}
    </span>
    
    @if($active)
        <span class="ml-auto w-1.5 h-1.5 bg-love-400 rounded-full"></span>
    @endif
</a>
