<x-layouts.app title="Negosiasi & Persetujuan Diskon">
    <x-page-header
        title="Negosiasi & Persetujuan Diskon"
        subtitle="Kelola penawaran harga khusus konsumen, persetujuan diskon manajerial, dan program promo unit.">
        @if (auth()->user()->isSales() || auth()->user()->isManagerial())
            <x-button variant="gold" icon="fa-solid fa-plus" :href="route('sales.negotiations.create')">
                Ajukan Negosiasi Baru
            </x-button>
        @endif
    </x-page-header>

    <!-- Metrics Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            title="Total Negosiasi"
            :value="$metrics['total']"
            icon="fa-solid fa-handshake"
            helper="Seluruh riwayat negosiasi" />

        <x-stat-card
            title="Menunggu Approval"
            :value="$metrics['pending']"
            icon="fa-solid fa-clock"
            helper="Perlu konfirmasi manajer" />

        <x-stat-card
            title="Diskon Disetujui"
            :value="$metrics['approved']"
            icon="fa-solid fa-circle-check"
            helper="Siap diproses ke booking" />

        <x-stat-card
            title="Total Diskon Disetujui"
            :value="'Rp ' . number_format($metrics['total_discount'] / 1000000, 0, ',', '.') . ' Jt'"
            icon="fa-solid fa-tags"
            helper="Akumulasi potongan harga" />
    </div>

    <!-- Filter & Search Bar -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('sales.negotiations.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('sales.negotiations.index') }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ empty($currentStatus) ? 'bg-[#161616] text-white' : 'bg-[#F7F6F2] text-[#79766F] hover:bg-[#E8E4DA]' }}">
                    Semua ({{ $metrics['total'] }})
                </a>
                @foreach ($statuses as $st)
                    <a href="{{ route('sales.negotiations.index', array_merge($filters, ['status' => $st->value])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $currentStatus === $st->value ? 'bg-[#161616] text-white' : 'bg-[#F7F6F2] text-[#79766F] hover:bg-[#E8E4DA]' }}">
                        {{ $st->label() }}
                    </a>
                @endforeach
            </div>

            <div class="w-full sm:w-72 flex gap-2">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari prospek atau no. unit..."
                       class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                <button type="submit" class="px-3 py-2 bg-[#161616] text-white rounded-lg text-xs hover:bg-[#262626]">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    </x-card>

    <!-- Negotiations Table -->
    <x-card :padding="false">
        @if ($negotiations->isEmpty())
            <x-empty-state
                icon="fa-solid fa-handshake-slash"
                title="Belum ada data negosiasi"
                message="Pengajuan harga khusus dan permohonan diskon konsumen akan tampil di sini." />
        @else
            <x-table>
                <thead>
                    <tr>
                        <th class="cell-wrap">Prospek Konsumen</th>
                        <th class="cell-wrap">Unit & Kawasan</th>
                        <th class="cell-fit">Pricelist Unit</th>
                        <th class="cell-fit">Penawaran Konsumen</th>
                        <th class="cell-fit">Nilai Diskon</th>
                        <th class="cell-fit">Status Approval</th>
                        <th class="cell-fit">Sales Agent</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($negotiations as $neg)
                        <tr>
                            <td class="cell-wrap">
                                @if ($neg->lead)
                                    <a href="{{ route('crm.leads.show', $neg->lead) }}" class="font-bold text-[#161616] hover:text-[#B89B5E] block transition-colors">
                                        {{ $neg->lead->name }}
                                    </a>
                                    <span class="text-[11px] text-[#79766F] block">{{ $neg->lead->phone ?? '-' }}</span>
                                @else
                                    <span class="font-bold text-[#161616] block">Konsumen</span>
                                @endif
                            </td>
                            <td class="cell-wrap">
                                <span class="font-semibold text-[#161616] block">
                                    Unit {{ $neg->propertyUnit->unit_number }} (Blok {{ $neg->propertyUnit->block ?? '-' }})
                                </span>
                                <span class="text-[11px] text-[#79766F] block">
                                    {{ $neg->propertyUnit->cluster->name }} • {{ $neg->propertyUnit->cluster->project->name }}
                                </span>
                            </td>
                            <td class="cell-fit">
                                <span class="font-medium text-[#79766F]">
                                    Rp {{ number_format($neg->initial_price, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="cell-fit">
                                <span class="font-bold text-[#161616]">
                                    Rp {{ number_format($neg->customer_offer_price, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="cell-fit">
                                @if ($neg->discount_amount > 0)
                                    <span class="font-bold text-[#B89B5E] block">
                                        -Rp {{ number_format($neg->discount_amount, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-[#79766F] block">
                                        ({{ $neg->discount_percentage }}% diskon)
                                    </span>
                                @else
                                    <span class="text-xs text-[#79766F] italic">Tanpa Diskon</span>
                                @endif
                            </td>
                            <td class="cell-fit">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $neg->approval_status->badgeClass() }}">
                                    {{ $neg->approval_status->label() }}
                                </span>
                            </td>
                            <td class="cell-fit">
                                <span class="text-xs text-[#161616] font-medium block">{{ $neg->sales->name }}</span>
                                <span class="text-[10px] text-[#79766F] block">{{ $neg->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="action-col">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('sales.negotiations.show', $neg) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 rounded-lg border border-[#E8E4DA] bg-white text-xs font-medium text-[#161616] hover:bg-[#F7F6F2] transition-colors"
                                       title="Lihat Detail & Persetujuan">
                                        <i class="fa-solid fa-eye mr-1.5"></i> Detail
                                    </a>

                                    @if ($neg->isApproved() && $neg->propertyUnit->isAvailable())
                                        <a href="{{ route('sales.bookings.create', ['unit_id' => $neg->property_unit_id, 'discount_amount' => $neg->discount_amount]) }}"
                                           class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-[#B89B5E] hover:bg-[#A3884E] text-white text-xs font-semibold transition-colors"
                                           title="Buat Surat Pesanan Booking (SPP)">
                                            <i class="fa-solid fa-file-signature mr-1.5"></i> Booking
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>

            @if ($negotiations->hasPages())
                <div class="prop-table-pagination">
                    {{ $negotiations->links() }}
                </div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
