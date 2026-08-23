@props([
    'text' => '',
    'type' => 'default',
])

@php
    $typeClasses = match(strtoupper((string)($type ?: $text))) {
        'BEST SELLER' => 'bg-red-600 text-white',
        'TIẾT KIỆM' => 'bg-emerald-600 text-white',
        default => 'bg-amber-500 text-white',
    };
@endphp

<span {{ $attributes->merge(['class' => "px-2.5 py-1 rounded-md text-[11px] font-extrabold uppercase shadow-xs {$typeClasses}"]) }}>
    {{ $text ?: $slot }}
</span>
