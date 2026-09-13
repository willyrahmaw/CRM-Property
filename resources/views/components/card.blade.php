@props([
    'title' => null,
    'subtitle' => null,
    'action' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-[#E8E4DA] shadow-xs relative']) }}>
    @if ($title || $action)
        <div class="px-4 sm:px-6 py-3.5 sm:py-4 border-b border-[#E8E4DA] flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 rounded-t-xl">
            <div class="min-w-0">
                @if ($title)
                    <h3 class="text-sm sm:text-base font-semibold text-[#161616] tracking-tight">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-[#79766F] mt-0.5 line-clamp-2 sm:line-clamp-none">{{ $subtitle }}</p>
                @endif
            </div>
            @if ($action)
                <div class="flex items-center flex-wrap gap-2 flex-shrink-0">
                    {{ $action }}
                </div>
            @endif
        </div>
    @endif
    @if ($padding)
        <div class="p-4 sm:p-6">
            {{ $slot }}
        </div>
    @else
        <div>
            {{ $slot }}
        </div>
    @endif
</div>
