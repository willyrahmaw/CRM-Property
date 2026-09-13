@props([
    'icon' => 'fa-solid fa-folder-open',
    'title' => 'Belum ada data',
    'message' => 'Data baru akan muncul di sini setelah ditambahkan ke dalam sistem.',
    'actionText' => null,
    'actionUrl' => null,
])

<div class="py-12 px-4 text-center">
    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-[#F7F6F2] border border-[#E8E4DA] flex items-center justify-center text-[#B89B5E]">
        <i class="{{ $icon }} text-2xl"></i>
    </div>
    <h3 class="text-base font-semibold text-[#161616] tracking-tight">{{ $title }}</h3>
    <p class="text-xs text-[#79766F] max-w-sm mx-auto mt-1 mb-6 leading-relaxed">{{ $message }}</p>

    @if ($actionText && $actionUrl)
        <x-button variant="gold" :href="$actionUrl">
            {{ $actionText }}
        </x-button>
    @endif
</div>
