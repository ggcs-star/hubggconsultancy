@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full rounded-lg border-app-border text-sm shadow-sm focus:border-primary focus:ring-primary']) !!}>
