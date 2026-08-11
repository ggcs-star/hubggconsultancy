@props(['href', 'active' => false, 'icon' => null])

<a href="{{ $href }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium ' . ($active ? 'border-primary text-primary' : 'border-transparent text-secondary hover:border-app-border hover:text-secondary-dark')]) }}>
    @if ($icon)
        <x-icon :name="$icon" class="h-4 w-4" />
    @endif
    {{ $slot }}
</a>
