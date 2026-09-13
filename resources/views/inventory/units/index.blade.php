<x-layouts.app title="Inventori Unit Properti">
    <x-page-header
        title="Inventori Unit Properti"
        subtitle="Daftar lengkap seluruh kaveling, spesifikasi bangunan, status penjualan, dan harga jual.">
        @if (auth()->user()->isAdminProperty() || auth()->user()->isCompanyOwner() || auth()->user()->isSuperAdmin())
            <x-button variant="gold" icon="fa-solid fa-plus" :href="route('inventory.units.create')">
                Tambah Unit Baru
            </x-button>
        @endif
        <x-button variant="primary" icon="fa-solid fa-map-location-dot" :href="route('inventory.siteplan.index')">
            Interactive Siteplan
        </x-button>
    </x-page-header>

    <!-- Units Table -->
    <x-card :padding="false">
        @if ($units->isEmpty())
            <x-empty-state
                icon="fa-solid fa-house-chimney"
                title="Belum ada unit properti"
                message="Data unit properti akan terdaftar di sini." />
        @else
            <x-table>
                <thead>
                    <tr>
                        <th class="cell-fit">No. Unit & Blok</th>
                        <th class="cell-wrap">Proyek & Cluster</th>
                        <th class="cell-wrap">Tipe & Ukuran</th>
                        <th class="cell-fit">Kamar</th>
                        <th class="cell-fit">Harga Jual</th>
                        <th class="cell-fit">Status</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($units as $unit)
                        <tr>
                            <td class="cell-fit">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('inventory.units.show', $unit) }}" class="w-12 h-12 rounded-lg overflow-hidden border border-[#E8E4DA] bg-[#F7F6F2] shrink-0 block hover:border-[#B89B5E] transition-colors">
                                        <img src="{{ $unit->image_url }}" alt="Unit {{ $unit->unit_number }}" class="w-full h-full object-cover">
                                    </a>
                                    <div>
                                        <a href="{{ route('inventory.units.show', $unit) }}" class="font-bold text-[#161616] hover:text-[#B89B5E] transition-colors block">
                                            Unit {{ $unit->unit_number }}
                                        </a>
                                        <span class="block text-[11px] text-[#79766F] font-normal">Blok {{ $unit->block ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="cell-wrap">
                                <span class="font-semibold text-[#161616] block">{{ $unit->cluster->name }}</span>
                                <span class="block text-[11px] text-[#79766F]">{{ $unit->cluster->project->name }}</span>
                            </td>
                            <td class="cell-wrap">
                                <span class="font-medium text-[#161616] block">{{ $unit->propertyType?->name ?? 'Standard' }}</span>
                                <span class="text-[11px] text-[#79766F] block">LB: {{ $unit->building_area }} m² / LT: {{ $unit->land_area }} m²</span>
                            </td>
                            <td class="cell-fit text-[#79766F]">
                                {{ $unit->bedrooms }} KT / {{ $unit->bathrooms }} KM
                            </td>
                            <td class="cell-fit">
                                <span class="font-bold text-[#15803D]">Rp {{ number_format($unit->selling_price, 0, ',', '.') }}</span>
                            </td>
                            <td class="cell-fit">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $unit->status->badgeClass() }}">
                                    {{ $unit->status->label() }}
                                </span>
                            </td>
                            <td class="action-col">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('inventory.units.show', $unit) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 rounded-lg border border-[#E8E4DA] bg-white text-xs font-medium text-[#161616] hover:bg-[#F7F6F2] transition-colors"
                                       title="Lihat Detail Unit">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if ($unit->isAvailable())
                                        @if (auth()->user()->isSales() || auth()->user()->isManagerial())
                                            <a href="{{ route('sales.bookings.create', ['unit_id' => $unit->id]) }}"
                                               class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#B89B5E] hover:bg-[#A3884E] text-white text-xs font-semibold transition-colors">
                                                Booking
                                            </a>
                                        @else
                                            <span class="text-xs text-[#15803D] font-semibold">Tersedia</span>
                                        @endif
                                    @else
                                        <span class="text-xs text-[#79766F] italic">Terkunci</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>

            @if ($units->hasPages())
                <div class="prop-table-pagination">
                    {{ $units->links() }}
                </div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
