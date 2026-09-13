@props([
    'title' => null,
    'subtitle' => null,
    'action' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-[#E8E4DA] shadow-xs relative']) }}>
    @if ($title || $action)
        <div class="px-6 py-4 border-b border-[#E8E4DA] flex items-center justify-between rounded-t-xl">
            <div>
                @if ($title)
                    <h3 class="text-base font-semibold text-[#161616] tracking-tight">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-[#79766F] mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if ($action)
                <div class="flex items-center space-x-2">
                    {{ $action }}
                </div>
            @endif
        </div>
    @endif
    @if ($padding)
        <div class="p-6">
            {{ $slot }}
        </div>
    @else
        <div>
            {{ $slot }}
        </div>
    @endif
</div>
