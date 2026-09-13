@props([
    'variant' => 'primary', // primary, gold, secondary, danger, outline
    'size' => 'md',        // sm, md, lg
    'icon' => null,
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-[#B89B5E] focus:ring-offset-1 disabled:opacity-50 disabled:cursor-not-allowed';

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'lg' => 'px-6 py-3 text-base',
        default => 'px-4 py-2 text-sm',
    };

    $variantClasses = match ($variant) {
        'gold' => 'bg-[#B89B5E] hover:bg-[#A3884E] text-white shadow-xs',
        'secondary' => 'bg-[#F7F6F2] hover:bg-[#E8E4DA] text-[#161616] border border-[#E8E4DA]',
        'danger' => 'bg-[#991B1B] hover:bg-[#7F1D1D] text-white shadow-xs',
        'outline' => 'bg-transparent hover:bg-[#F7F6F2] text-[#161616] border border-[#E8E4DA]',
        default => 'bg-[#161616] hover:bg-[#262626] text-white shadow-xs',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i class="{{ $icon }} mr-2"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i class="{{ $icon }} mr-2"></i>
        @endif
        {{ $slot }}
    </button>
@endif
