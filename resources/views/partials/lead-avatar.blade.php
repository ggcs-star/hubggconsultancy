@php
    $leadAvatarPalette = [
        ['bg-violet-100', 'text-violet-700'],
        ['bg-orange-100', 'text-orange-700'],
        ['bg-blue-100', 'text-blue-700'],
        ['bg-pink-100', 'text-pink-700'],
        ['bg-emerald-100', 'text-emerald-700'],
        ['bg-amber-100', 'text-amber-700'],
        ['bg-cyan-100', 'text-cyan-700'],
        ['bg-rose-100', 'text-rose-700'],
        ['bg-indigo-100', 'text-indigo-700'],
        ['bg-teal-100', 'text-teal-700'],
    ];
    [$leadAvatarBg, $leadAvatarText] = $leadAvatarPalette[$lead->id % count($leadAvatarPalette)];
    $leadAvatarInitials = strtoupper(substr($lead->company ?: $lead->name, 0, 2));
@endphp

<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $leadAvatarBg }} {{ $leadAvatarText }} text-sm font-bold">
    {{ $leadAvatarInitials }}
</span>
