@props(['type' => 'button'])

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 rounded-lg border border-app-border bg-white px-4 py-2.5 text-sm font-medium text-secondary-dark transition hover:bg-surface-alt']) }}>
    {{ $slot }}
</button>
