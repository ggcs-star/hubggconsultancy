@props(['classes' => 'bg-secondary-light text-secondary-dark', 'dot' => false])

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {$classes}"]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
    @endif
    {{ $slot }}
</span>
