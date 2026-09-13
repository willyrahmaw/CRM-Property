@props([
    'variant' => 'neutral', // neutral, gold, success, warning, danger, dark
])

@php
    $classes = match ($variant) {
        'gold' => 'bg-[#D7C49E] text-[#161616]',
        'gold-solid' => 'bg-[#B89B5E] text-white',
        'success' => 'bg-[#15803D] text-white',
        'warning' => 'bg-[#D97706] text-white',
        'danger' => 'bg-[#991B1B] text-white',
        'dark' => 'bg-[#262626] text-white',
        default => 'bg-[#E8E4DA] text-[#161616]',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium tracking-wide ' . $classes]) }}>
    {{ $slot }}
</span>
