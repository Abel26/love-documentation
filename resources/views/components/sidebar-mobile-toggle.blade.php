<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center justify-center p-2 rounded-lg text-love-100 hover:bg-love-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-love-400 transition-all duration-200',
    'aria-label' => 'Toggle sidebar',
    '@click' => '$dispatch(\'toggle-sidebar\')'
]) }}>
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>
