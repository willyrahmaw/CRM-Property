<x-layouts.app title="Interactive Siteplan">
    <x-page-header
        :title="'Interactive Siteplan — ' . $project->name"
        subtitle="Denah visual interaktif untuk pemantauan ketersediaan unit, status penjualan, dan inspeksi detail.">
        <div class="flex items-center space-x-2">
            <select onchange="window.location.href = '/inventory/siteplan/' + this.value"
                    class="px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] font-medium focus:outline-none focus:border-[#B89B5E]">
                @foreach ($allProjects as $proj)
                    <option value="{{ $proj->id }}" {{ $proj->id === $project->id ? 'selected' : '' }}>
                        {{ $proj->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </x-page-header>

    <!-- Legend Bar -->
    <div class="bg-white p-4 rounded-xl border border-[#E8E4DA] mb-6 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center space-x-6 flex-wrap gap-y-2 text-xs font-semibold">
            <span class="text-[#79766F] uppercase text-[10px] tracking-wider">Indikator Unit:</span>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-sm bg-[#15803D]"></span>
                <span class="text-[#161616]">Available (Tersedia)</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-sm bg-[#D97706]"></span>
                <span class="text-[#161616]">Reserved</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-sm bg-[#B89B5E]"></span>
                <span class="text-[#161616]">Booked</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-sm bg-[#991B1B]"></span>
                <span class="text-[#161616]">Sold Out</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-sm bg-[#262626]"></span>
                <span class="text-[#161616]">Blocked</span>
            </div>
        </div>

        <span class="text-xs text-[#79766F]">
            <i class="fa-solid fa-circle-info mr-1 text-[#B89B5E]"></i> Klik pada salah satu kotak unit untuk memeriksa detail spesifikasi & pemesanan.
        </span>
    </div>

    <!-- Main Siteplan Stage & Inspection Drawer Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Siteplan Visualizer Canvas / Grid -->
        <div class="lg:col-span-3 bg-white p-6 rounded-xl border border-[#E8E4DA] shadow-xs min-h-[500px]" id="siteplan-interactive-wrapper">
            @forelse ($project->clusters as $cluster)
                <div class="mb-8 last:mb-0">
                    <div class="flex items-center justify-between pb-2 mb-4 border-b border-[#E8E4DA]">
                        <h3 class="text-sm font-bold text-[#161616] tracking-tight">
                            <i class="fa-solid fa-sitemap mr-1.5 text-[#B89B5E]"></i> {{ $cluster->name }}
                        </h3>
                        <span class="text-xs text-[#79766F] font-medium">{{ $cluster->propertyUnits->count() }} Total Unit</span>
                    </div>

                    <!-- Unit Blocks Grid -->
                    <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2.5">
                        @foreach ($cluster->propertyUnits as $unit)
                            <button type="button"
                                    data-unit-id="{{ $unit->id }}"
                                    data-detail-url="{{ route('inventory.siteplan.unit', $unit) }}"
                                    class="p-2.5 rounded-lg border border-[#E8E4DA] bg-white hover:border-[#161616] hover:scale-105 transition-all text-center flex flex-col justify-between items-center group cursor-pointer">
                                <span class="w-full h-1.5 rounded-full mb-1.5 {{ $unit->status->badgeClass() }}"></span>
                                <span class="text-xs font-bold text-[#161616] group-hover:text-[#B89B5E] transition-colors">
                                    {{ $unit->unit_number }}
                                </span>
                                <span class="text-[9px] text-[#79766F] mt-0.5">
                                    Blok {{ $unit->block ?? '-' }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @empty
                <x-empty-state
                    icon="fa-solid fa-map-location-dot"
                    title="Belum ada unit yang dipetakan"
                    message="Tambahkan cluster dan unit properti pada project ini untuk melihat visualisasi siteplan." />
            @endforelse
        </div>

        <!-- Unit Inspection Drawer -->
        <div class="lg:col-span-1">
            <div id="unit-detail-drawer" class="bg-white p-6 rounded-xl border border-[#E8E4DA] shadow-xs sticky top-8 hidden">
                <div class="flex items-center justify-between pb-3 border-b border-[#E8E4DA] mb-4">
                    <h3 class="text-sm font-bold text-[#161616]">Detail Unit Properti</h3>
                    <button type="button" id="close-drawer-btn" class="text-[#79766F] hover:text-[#161616]">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[10px] text-[#79766F] uppercase font-bold block">Nomor & Blok Unit</span>
                        <span id="drawer-unit-number" class="text-base font-bold text-[#161616]">Unit A-01</span>
                    </div>

                    <div>
                        <span class="text-[10px] text-[#79766F] uppercase font-bold block">Cluster</span>
                        <span id="drawer-cluster" class="font-semibold text-[#161616]">Cluster Magnolia</span>
                    </div>

                    <div>
                        <span class="text-[10px] text-[#79766F] uppercase font-bold block">Tipe Properti</span>
                        <span id="drawer-type" class="font-semibold text-[#161616]">Type 45/90</span>
                    </div>

                    <div class="pt-2 border-t border-[#E8E4DA]">
                        <span class="text-[10px] text-[#79766F] uppercase font-bold block">Harga Jual</span>
                        <span id="drawer-price" class="text-lg font-bold text-[#15803D]">Rp 650.000.000</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#E8E4DA]">
                        <div>
                            <span class="text-[10px] text-[#79766F] uppercase block">Luas Bangunan</span>
                            <span id="drawer-building" class="font-semibold text-[#161616]">45 m²</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-[#79766F] uppercase block">Luas Tanah</span>
                            <span id="drawer-land" class="font-semibold text-[#161616]">90 m²</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-[10px] text-[#79766F] uppercase block">Kamar Tidur / Mandi</span>
                        <span id="drawer-bedrooms" class="font-semibold text-[#161616]">2 KT / 1 KM</span>
                    </div>

                    <div class="pt-2 border-t border-[#E8E4DA]">
                        <span class="text-[10px] text-[#79766F] uppercase font-bold block mb-1">Status Unit</span>
                        <span id="drawer-status" class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-[#15803D] text-white">
                            Available
                        </span>
                    </div>

                    <div id="drawer-customer-info" class="hidden pt-2 border-t border-[#E8E4DA]">
                        <span class="text-[10px] text-[#79766F] uppercase font-bold block">Data Pemesan</span>
                        <span id="drawer-customer-name" class="font-semibold text-[#161616] block mt-0.5">-</span>
                    </div>

                    @if(auth()->user()->isSales() || auth()->user()->isManagerial())
                        <div class="pt-4">
                            <a id="drawer-booking-btn" href="#"
                               class="w-full flex items-center justify-center py-2.5 px-4 rounded-lg bg-[#B89B5E] hover:bg-[#A3884E] text-white text-xs font-bold transition-colors">
                                <i class="fa-solid fa-file-signature mr-1.5"></i> Proses Booking Unit Ini
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
