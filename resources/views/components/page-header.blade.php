@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
])

<div {{ $attributes->merge(['class' => 'mb-6 sm:mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4']) }}>
    <div class="min-w-0">
        @if (count($breadcrumbs) > 0)
            <nav class="flex items-center space-x-2 text-xs text-[#79766F] mb-1.5 overflow-x-auto whitespace-nowrap pb-1 select-none">
                @foreach ($breadcrumbs as $crumb)
                    @if (!$loop->last)
                        <a href="{{ $crumb['url'] ?? '#' }}" class="hover:text-[#161616] transition-colors flex-shrink-0">{{ $crumb['label'] }}</a>
                        <span class="text-[#E8E4DA] flex-shrink-0"><i class="fa-solid fa-chevron-right text-[10px]"></i></span>
                    @else
                        <span class="text-[#161616] font-medium flex-shrink-0">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-[#161616] tracking-tight leading-tight">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-xs sm:text-sm text-[#79766F] mt-1 line-clamp-2 sm:line-clamp-none">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="flex items-center flex-wrap gap-2 sm:gap-3 w-full md:w-auto">
            {{ $slot }}
        </div>
    @endif
</div>
