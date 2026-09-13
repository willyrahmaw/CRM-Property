@props([
    'title',
    'value',
    'icon' => null,
    'change' => null,
    'changeType' => 'positive', // positive, negative, neutral
    'helper' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border border-[#E8E4DA] p-5 shadow-xs flex flex-col justify-between']) }}>
    <div class="flex items-center justify-between">
        <span class="text-xs font-medium uppercase tracking-wider text-[#79766F]">{{ $title }}</span>
        @if ($icon)
            <div class="w-9 h-9 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA] flex items-center justify-center text-[#B89B5E]">
                <i class="{{ $icon }} text-sm"></i>
            </div>
        @endif
    </div>

    <div class="mt-4">
        <div class="text-2xl font-bold text-[#161616] tracking-tight">
            {{ $value }}
        </div>

        @if ($change || $helper)
            <div class="mt-2 flex items-center text-xs">
                @if ($change)
                    <span class="font-semibold {{ $changeType === 'positive' ? 'text-[#15803D]' : ($changeType === 'negative' ? 'text-[#991B1B]' : 'text-[#79766F]') }}">
                        @if ($changeType === 'positive')
                            <i class="fa-solid fa-arrow-trend-up mr-1"></i>
                        @elseif ($changeType === 'negative')
                            <i class="fa-solid fa-arrow-trend-down mr-1"></i>
                        @endif
                        {{ $change }}
                    </span>
                @endif

                @if ($helper)
                    <span class="text-[#79766F] {{ $change ? 'ml-1.5' : '' }}">{{ $helper }}</span>
                @endif
            </div>
        @endif
    </div>
</div>
