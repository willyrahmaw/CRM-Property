@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => [],
])

<div {{ $attributes->merge(['class' => 'mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4']) }}>
    <div>
        @if (count($breadcrumbs) > 0)
            <nav class="flex items-center space-x-2 text-xs text-[#79766F] mb-1.5">
                @foreach ($breadcrumbs as $crumb)
                    @if (!$loop->last)
                        <a href="{{ $crumb['url'] ?? '#' }}" class="hover:text-[#161616] transition-colors">{{ $crumb['label'] }}</a>
                        <span class="text-[#E8E4DA]"><i class="fa-solid fa-chevron-right text-[10px]"></i></span>
                    @else
                        <span class="text-[#161616] font-medium">{{ $crumb['label'] }}</span>
                    @endif
                @endforeach
            </nav>
        @endif
        <h1 class="text-2xl md:text-3xl font-bold text-[#161616] tracking-tight">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-[#79766F] mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="flex items-center flex-wrap gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
