<x-layouts.app title="Daftar Booking Unit">
    <x-page-header
        title="Daftar Pemesanan (Booking Unit)"
        subtitle="Kelola transaksi booking fee, persetujuan unit, dan alur pembayaran konsumen.">
        @if (auth()->user()->isSales() || auth()->user()->isManagerial())
            <x-button variant="gold" icon="fa-solid fa-plus" :href="route('sales.bookings.create')">
                Buat Booking Baru
            </x-button>
        @endif
    </x-page-header>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            title="Total Pemesanan"
            :value="($metrics['total'] ?? 0) . ' Unit'"
            icon="fa-solid fa-file-signature"
            helper="semua status transaksi" />

        <x-stat-card
            title="Menunggu Persetujuan"
            :value="($metrics['pending'] ?? 0) . ' Unit'"
            icon="fa-solid fa-hourglass-half"
            helper="menunggu review managerial" />

        <x-stat-card
            title="Booking Disetujui"
            :value="($metrics['approved'] ?? 0) . ' Unit'"
            icon="fa-solid fa-circle-check"
            changeType="positive"
            helper="unit resmi terkunci (booked)" />

        <x-stat-card
            title="Total Nilai Booking"
            :value="'Rp ' . number_format($metrics['total_value'] ?? 0, 0, ',', '.')"
            icon="fa-solid fa-receipt"
            changeType="positive"
            helper="akumulasi final price aktif" />
    </div>

    <!-- Filters Bar -->
    <x-card class="mb-6">
        <form action="{{ route('sales.bookings.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-[#79766F] uppercase mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="No. booking, konsumen, unit..."
                       class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] text-[#161616] focus:bg-white focus:outline-none focus:border-[#B89B5E]">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-[#79766F] uppercase mb-1">Status Pemesanan</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] text-[#161616] focus:bg-white focus:outline-none focus:border-[#B89B5E]">
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" {{ ($filters['status'] ?? '') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <x-button type="submit" variant="primary" size="md" class="w-full">
                    Filter
                </x-button>
                <a href="{{ route('sales.bookings.index') }}" class="px-3 py-2 text-xs font-medium rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] hover:bg-[#E8E4DA] text-[#161616] transition-colors" title="Reset Filter">
                    <i class="fa-solid fa-arrow-rotate-right"></i>
                </a>
            </div>
        </form>
    </x-card>

    <!-- Bookings Table -->
    <x-card :padding="false">
        @if ($bookings->isEmpty())
            <x-empty-state
                icon="fa-solid fa-file-signature"
                title="Belum ada pemesanan unit"
                message="Transaksi booking unit akan terdaftar di sini setelah sales memproses surat pesanan."
                actionText="Buat Booking Baru"
                :actionUrl="route('sales.bookings.create')" />
        @else
            <x-table>
                <thead>
                    <tr>
                        <th class="cell-fit">No. Booking & Tgl</th>
                        <th class="cell-wrap">Konsumen</th>
                        <th class="cell-wrap">Unit & Cluster</th>
                        <th class="cell-fit">Skema Bayar</th>
                        <th class="cell-fit">Harga Final</th>
                        <th class="cell-fit">Booking Fee</th>
                        <th class="cell-fit">Sales PIC</th>
                        <th class="cell-fit">Status</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                        <tr>
                            <td class="cell-fit">
                                <span class="font-bold text-[#161616] block">{{ $booking->booking_number }}</span>
                                <span class="text-[11px] text-[#79766F] block">{{ $booking->booking_date->translatedFormat('d M Y') }}</span>
                            </td>
                            <td class="cell-wrap">
                                <span class="font-semibold text-[#161616] block">{{ $booking->customer->name }}</span>
                                <span class="text-[11px] text-[#79766F] block">{{ $booking->customer->phone }}</span>
                            </td>
                            <td class="cell-wrap">
                                <span class="font-semibold text-[#161616] block">Unit {{ $booking->propertyUnit->unit_number }}</span>
                                <span class="text-[11px] text-[#79766F] block">{{ $booking->propertyUnit->cluster->name }}</span>
                            </td>
                            <td class="cell-fit">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium {{ $booking->payment_scheme->badgeClass() }}">
                                    <i class="{{ $booking->payment_scheme->icon() }} text-[10px]"></i>
                                    <span>{{ $booking->payment_scheme->shortLabel() }}</span>
                                </span>
                            </td>
                            <td class="cell-fit">
                                <span class="font-bold text-[#161616] text-sm block">
                                    Rp {{ number_format($booking->final_price, 0, ',', '.') }}
                                </span>
                                @if ($booking->discount_amount > 0)
                                    <span class="text-[10px] text-[#991B1B] font-medium flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-tag text-[8px]"></i> Diskon Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-[10px] text-[#79766F] font-normal block">Pricelist Net</span>
                                @endif
                            </td>
                            <td class="cell-fit">
                                <span class="font-bold text-[#15803D] text-xs block">
                                    Rp {{ number_format($booking->booking_fee, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-[#79766F] font-normal block">Tanda Jadi</span>
                            </td>
                            <td class="cell-fit text-[#79766F]">
                                {{ $booking->sales->name }}
                            </td>
                            <td class="cell-fit">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $booking->status->badgeClass() }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $booking->status->dotClass() }}"></span>
                                    {{ $booking->status->label() }}
                                </span>
                            </td>
                            <td class="action-col space-x-1">
                                <a href="{{ route('sales.bookings.show', $booking) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-[#E8E4DA] bg-white hover:bg-[#F7F6F2] text-[#161616] font-medium text-xs transition-colors">
                                    Detail
                                </a>

                                @if ($booking->status === \App\Enums\BookingStatus::PENDING && auth()->user()->isManagerial())
                                    <form action="{{ route('sales.bookings.approve', $booking) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                data-confirm-action
                                                data-title="Setujui Booking?"
                                                data-text="Unit akan resmi dialihkan ke status BOOKED."
                                                data-confirm-text="Ya, Setujui"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#15803D] hover:bg-[#166534] text-white font-semibold text-xs transition-colors">
                                            Setujui
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>

            @if ($bookings->hasPages())
                <div class="prop-table-pagination">
                    {{ $bookings->links() }}
                </div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
