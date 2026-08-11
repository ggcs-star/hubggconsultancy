@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-secondary-dark']) }}>
    {{ $value ?? $slot }}
</label>
