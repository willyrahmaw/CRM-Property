@props([
    'title',
    'value',
    'icon' => null,
    'change' => null,
    'changeType' => 'positive', // positive, negative, neutral
    'helper' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-[#E8E4DA] p-4 sm:p-5 shadow-xs flex flex-col justify-between']) }}>
    <div class="flex items-center justify-between">
        <span class="text-[11px] sm:text-xs font-medium uppercase tracking-wider text-[#79766F]">{{ $title }}</span>
        @if ($icon)
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA] flex items-center justify-center text-[#B89B5E] flex-shrink-0 ml-2">
                <i class="{{ $icon }} text-xs sm:text-sm"></i>
            </div>
        @endif
    </div>

    <div class="mt-3 sm:mt-4">
        <div class="text-xl sm:text-2xl font-bold text-[#161616] tracking-tight break-words">
            {{ $value }}
        </div>

        @if ($change || $helper)
            <div class="mt-2 flex items-center flex-wrap gap-1 text-xs">
                @if ($change)
                    <span class="font-semibold {{ $changeType === 'positive' ? 'text-[#15803D]' : ($changeType === 'negative' ? 'text-[#991B1B]' : 'text-[#79766F]') }}">
                        @if ($changeType === 'positive')
                            <i class="fa-solid fa-arrow-trend-up mr-0.5"></i>
                        @elseif ($changeType === 'negative')
                            <i class="fa-solid fa-arrow-trend-down mr-0.5"></i>
                        @endif
                        {{ $change }}
                    </span>
                @endif

                @if ($helper)
                    <span class="text-[#79766F]">{{ $helper }}</span>
                @endif
            </div>
        @endif
    </div>
</div>
