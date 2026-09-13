<x-layouts.app :title="'Surat Pesanan ' . $booking->booking_number">
    <x-page-header
        :title="'Pemesanan ' . $booking->booking_number"
        :subtitle="'Tanggal Pemesanan: ' . $booking->booking_date->translatedFormat('d F Y') . ' • Sales PIC: ' . $booking->sales->name"
        :breadcrumbs="[
            ['label' => 'Penjualan', 'url' => route('sales.bookings.index')],
            ['label' => 'Bookings', 'url' => route('sales.bookings.index')],
            ['label' => $booking->booking_number, 'url' => '#'],
        ]">
        <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('sales.bookings.index')">
            Kembali
        </x-button>

        @if ($booking->status === \App\Enums\BookingStatus::PENDING && auth()->user()->isManagerial())
            <form action="{{ route('sales.bookings.approve', $booking) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        data-confirm-action
                        data-title="Setujui Booking?"
                        data-text="Unit akan resmi dialihkan ke status BOOKED dan terkunci dari penjualan lain."
                        data-confirm-text="Ya, Setujui Booking"
                        class="px-4 py-2 rounded-lg bg-[#15803D] hover:bg-[#166534] text-white text-xs font-bold transition-colors">
                    <i class="fa-solid fa-check mr-1.5"></i> Setujui Booking
                </button>
            </form>
        @endif

        @if (in_array($booking->status, [\App\Enums\BookingStatus::PENDING, \App\Enums\BookingStatus::APPROVED]) && auth()->user()->isManagerial())
            <form action="{{ route('sales.bookings.cancel', $booking) }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="reason" value="Dibatalkan oleh manajemen / konsumen membatalkan pesanan.">
                <button type="submit"
                        data-confirm-action
                        data-is-danger="true"
                        data-title="Batalkan Booking?"
                        data-text="Unit akan dikembalikan menjadi AVAILABLE dan dapat dipesan oleh sales lain."
                        data-confirm-text="Ya, Batalkan"
                        class="px-4 py-2 rounded-lg bg-[#991B1B] hover:bg-[#7F1D1D] text-white text-xs font-bold transition-colors">
                    <i class="fa-solid fa-ban mr-1.5"></i> Batalkan Booking
                </button>
            </form>
        @endif
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Summary Info -->
        <div class="space-y-6">
            <!-- Booking Status Card -->
            <x-card title="Status Pemesanan">
                <div class="space-y-3">
                    <div class="flex items-center justify-between pb-3 border-b border-[#E8E4DA]">
                        <span class="text-xs text-[#79766F]">Status Unit</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $booking->status->badgeClass() }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $booking->status->dotClass() }}"></span>
                            {{ $booking->status->label() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pb-3 border-b border-[#E8E4DA]">
                        <span class="text-xs text-[#79766F]">Skema Pembayaran</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium {{ $booking->payment_scheme->badgeClass() }}">
                            <i class="{{ $booking->payment_scheme->icon() }} text-[10px]"></i>
                            <span>{{ $booking->payment_scheme->label() }}</span>
                        </span>
                    </div>

                    @if ($booking->approver)
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-[#79766F]">Disetujui Oleh</span>
                            <span class="text-xs font-semibold text-[#161616]">{{ $booking->approver->name }}</span>
                        </div>
                    @endif

                    @if ($booking->cancellation_reason)
                        <div class="p-3 rounded-lg bg-[#FEF2F2] border border-[#FCA5A5] text-xs text-[#991B1B]">
                            <span class="font-bold block mb-0.5">Alasan Pembatalan:</span>
                            {{ $booking->cancellation_reason }}
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Customer Card -->
            <x-card title="Identitas Konsumen">
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[#79766F] block text-[10px] uppercase font-bold">Nama Pemesan</span>
                        <span class="text-sm font-bold text-[#161616]">{{ $booking->customer->name }}</span>
                    </div>
                    <div>
                        <span class="text-[#79766F] block text-[10px] uppercase font-bold mb-1">WhatsApp / Telepon</span>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-[#161616]">{{ $booking->customer->phone }}</span>
                            @if ($booking->customer->phone)
                                <a href="{{ $booking->customer->getWhatsAppUrl("Halo {$booking->customer->salutation} {$booking->customer->name}, menginformasikan mengenai perkembangan pemesanan unit {$booking->propertyUnit->unit_number} di {$booking->propertyUnit->cluster->project->name}...") }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#DCFCE7] hover:bg-[#BBF7D0] text-[#15803D] text-[11px] font-bold transition-colors">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    <span>Chat WA</span>
                                </a>
                            @endif
                        </div>
                    </div>
                    @if ($booking->customer->gender)
                        <div>
                            <span class="text-[#79766F] block text-[10px] uppercase font-bold">Jenis Kelamin</span>
                            <span class="text-[#161616] font-medium">
                                {{ $booking->customer->gender->label() }} (Sapaan: {{ $booking->customer->salutation }})
                            </span>
                        </div>
                    @endif
                    @if ($booking->customer->nik)
                        <div>
                            <span class="text-[#79766F] block text-[10px] uppercase font-bold">NIK KTP</span>
                            <span class="font-mono text-[#161616]">{{ $booking->customer->nik }}</span>
                        </div>
                    @endif
                    @if ($booking->customer->email)
                        <div>
                            <span class="text-[#79766F] block text-[10px] uppercase font-bold">Email</span>
                            <span class="text-[#161616]">{{ $booking->customer->email }}</span>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Unit Specs -->
            <x-card title="Spesifikasi Unit Dipesan">
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[#79766F] block text-[10px] uppercase font-bold">Nomor Unit</span>
                        <span class="text-base font-bold text-[#161616]">
                            Unit {{ $booking->propertyUnit->unit_number }} (Blok {{ $booking->propertyUnit->block ?? '-' }})
                        </span>
                    </div>
                    <div>
                        <span class="text-[#79766F] block text-[10px] uppercase font-bold">Cluster & Proyek</span>
                        <span class="font-semibold text-[#161616]">
                            {{ $booking->propertyUnit->cluster->name }} — {{ $booking->propertyUnit->cluster->project->name }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-[#E8E4DA]">
                        <div>
                            <span class="text-[#79766F] block">Luas Bangunan:</span>
                            <span class="font-semibold text-[#161616]">{{ $booking->propertyUnit->building_area }} m²</span>
                        </div>
                        <div>
                            <span class="text-[#79766F] block">Luas Tanah:</span>
                            <span class="font-semibold text-[#161616]">{{ $booking->propertyUnit->land_area }} m²</span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right: Financial Breakdown & Payments -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Financial Breakdown Table -->
            <x-card title="Rincian Nilai Transaksi & Pembayaran">
                <div class="space-y-3">
                    <div class="flex justify-between text-xs py-1 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Harga Unit (Pricelist)</span>
                        <span class="font-semibold text-[#161616]">Rp {{ number_format($booking->unit_price, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between text-xs py-1 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Diskon yang Diberikan</span>
                        <span class="font-semibold text-[#991B1B]">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between text-sm py-2 border-b-2 border-[#161616] font-bold">
                        <span class="text-[#161616]">Harga Final Pengikatan</span>
                        <span class="text-[#161616]">Rp {{ number_format($booking->final_price, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between text-xs py-1 text-[#15803D] font-bold">
                        <span>Tanda Jadi / Booking Fee</span>
                        <span>Rp {{ number_format($booking->booking_fee, 0, ',', '.') }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Payments History & Record Form -->
            <x-card title="Histori Pembayaran Masuk" subtitle="Daftar pembayaran yang telah diterima atau menunggu verifikasi Finance">
                <div class="mb-4">
                    <form action="{{ route('finance.payments.store') }}" method="POST" enctype="multipart/form-data" class="p-4 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA] space-y-3">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        <span class="text-xs font-bold text-[#161616] uppercase block">Input Pembayaran Baru</span>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-[#161616] uppercase mb-1">Jenis Pembayaran</label>
                                <select name="payment_type" required class="w-full px-2.5 py-1.5 text-xs rounded border border-[#E8E4DA] bg-white text-[#161616]">
                                    @foreach (\App\Enums\PaymentType::cases() as $type)
                                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-semibold text-[#161616] uppercase mb-1">Nominal (Rp)</label>
                                <input type="number" name="amount" value="{{ $booking->booking_fee }}" required class="w-full px-2.5 py-1.5 text-xs rounded border border-[#E8E4DA] bg-white text-[#161616]">
                            </div>

                            <div>
                                <label class="block text-[10px] font-semibold text-[#161616] uppercase mb-1">Tanggal Bayar</label>
                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full px-2.5 py-1.5 text-xs rounded border border-[#E8E4DA] bg-white text-[#161616]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-semibold text-[#161616] uppercase mb-1">Metode & Referensi Transfer</label>
                                <input type="text" name="payment_method" value="Transfer Bank BCA" placeholder="Metode transfer..." class="w-full px-2.5 py-1.5 text-xs rounded border border-[#E8E4DA] bg-white text-[#161616]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-[#161616] uppercase mb-1">Upload Bukti Bayar (Opsional)</label>
                                <input type="file" name="proof_file" class="w-full text-xs text-[#79766F] file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:bg-[#161616] file:text-white">
                            </div>
                        </div>

                        <div class="flex justify-end pt-1">
                            <x-button type="submit" variant="primary" size="sm" icon="fa-solid fa-receipt">
                                Simpan Bukti Pembayaran
                            </x-button>
                        </div>
                    </form>
                </div>

                <!-- Payments Table -->
                @if ($booking->payments->isEmpty())
                    <p class="text-xs text-[#79766F] py-4 text-center">Belum ada pembayaran yang tercatat.</p>
                @else
                    <x-table class="border border-[#E8E4DA] rounded-lg">
                        <thead>
                            <tr>
                                <th>No. Kwitansi</th>
                                <th>Jenis</th>
                                <th>Nominal</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th class="action-col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($booking->payments as $pay)
                                <tr>
                                    <td class="font-bold text-[#161616]">{{ $pay->payment_number }}</td>
                                    <td class="font-medium text-[#161616]">{{ $pay->payment_type->label() }}</td>
                                    <td class="font-bold text-[#15803D]">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                                    <td class="text-[#79766F]">{{ $pay->payment_date->translatedFormat('d M Y') }}</td>
                                    <td>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $pay->status->badgeClass() }}">
                                            {{ $pay->status->label() }}
                                        </span>
                                    </td>
                                    <td class="action-col">
                                        @if ($pay->status === \App\Enums\PaymentStatus::PENDING && (auth()->user()->isFinance() || auth()->user()->isManagerial()))
                                            <form action="{{ route('finance.payments.verify', $pay) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        data-confirm-action
                                                        data-title="Verifikasi Pembayaran?"
                                                        data-text="Pembayaran ini akan ditandai lunas dan kwitansi resmi diterbitkan."
                                                        data-confirm-text="Ya, Verifikasi"
                                                        class="px-2.5 py-1 rounded-lg bg-[#15803D] hover:bg-[#166534] text-white text-xs font-semibold transition-colors">
                                                    Verifikasi
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-table>
                @endif
            </x-card>

            <!-- Commission Distribution Card -->
            <x-card title="Distribusi Komisi Penjualan" subtitle="Perhitungan komisi berjenjang dari nilai final transaksi unit properti">
                @if ($booking->commissions->isEmpty())
                    <div class="p-4 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA] text-center space-y-3">
                        <div class="w-10 h-10 rounded-full bg-white border border-[#E8E4DA] flex items-center justify-center mx-auto text-[#B89B5E]">
                            <i class="fa-solid fa-coins text-base"></i>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-[#161616] block">Komisi Belum Diterbitkan</span>
                            <span class="text-[11px] text-[#79766F] block mt-0.5">
                                Komisi penjualan belum dihitung untuk transaksi ini. Standar komisi adalah 2.5% dari nilai final pengikatan (60% Sales Closing, 20% Team Leader).
                            </span>
                        </div>

                        @if (auth()->user()->isFinance() || auth()->user()->isManagerial())
                            <div class="pt-1">
                                <form action="{{ route('finance.commissions.generate', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            data-confirm-action
                                            data-title="Terbitkan Komisi Sales?"
                                            data-text="Komisi akan dihitung otomatis sebesar 2.5% dari harga final transaksi (Rp {{ number_format($booking->final_price, 0, ',', '.') }})."
                                            data-confirm-text="Ya, Terbitkan Komisi"
                                            class="px-4 py-2 rounded-lg bg-[#B89B5E] hover:bg-[#A3884E] text-white text-xs font-bold transition-colors shadow-sm">
                                        <i class="fa-solid fa-calculator mr-1.5"></i> Hitung & Terbitkan Komisi Sales
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="text-[11px] text-[#79766F] italic">Menunggu penerbitan dan kalkulasi oleh tim Finance / Manajemen.</p>
                        @endif
                    </div>
                @else
                    <div class="space-y-4">
                        <x-table class="border border-[#E8E4DA] rounded-lg">
                            <thead>
                                <tr>
                                    <th>Penerima Komisi</th>
                                    <th>Kategori Fee</th>
                                    <th>Rate (%)</th>
                                    <th>Nominal Komisi</th>
                                    <th>Status</th>
                                    <th class="action-col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($booking->commissions as $comm)
                                    <tr>
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-[#161616] text-[#B89B5E] text-[10px] font-bold flex items-center justify-center uppercase">
                                                    {{ substr($comm->user->name, 0, 2) }}
                                                </div>
                                                <div>
                                                    <span class="font-bold text-[#161616] block text-xs">{{ $comm->user->name }}</span>
                                                    <span class="text-[10px] text-[#79766F]">{{ $comm->user->role->label() }}</span>
                                                    @if ($comm->user->bank_account_number)
                                                        <span class="text-[9px] text-[#79766F] font-mono block mt-0.5">
                                                            <i class="fa-solid fa-credit-card text-[8px] mr-1 text-[#B89B5E]"></i>
                                                            {{ $comm->user->bank_name }} {{ $comm->user->bank_account_number }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="px-2 py-0.5 rounded bg-[#F7F6F2] border border-[#E8E4DA] text-[#161616] text-[10px] font-semibold uppercase">
                                                {{ str_replace('_', ' ', $comm->beneficiary_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="font-bold text-[#161616] text-xs">{{ $comm->percentage }}%</span>
                                        </td>
                                        <td>
                                            <span class="font-bold text-[#15803D] text-xs">Rp {{ number_format($comm->amount, 0, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $comm->status->badgeClass() }}">
                                                {{ $comm->status->label() }}
                                            </span>
                                            @if ($comm->status === \App\Enums\CommissionStatus::APPROVED && $comm->approver)
                                                <span class="text-[9px] text-[#79766F] block mt-0.5">Oleh: {{ $comm->approver->name }}</span>
                                            @elseif ($comm->status === \App\Enums\CommissionStatus::PAID && $comm->paid_at)
                                                <span class="text-[9px] text-[#15803D] block mt-0.5">Cair: {{ $comm->paid_at->translatedFormat('d M Y') }}</span>
                                            @endif
                                        </td>
                                        <td class="action-col space-x-1">
                                            @if ($comm->status === \App\Enums\CommissionStatus::PENDING && (auth()->user()->isFinance() || auth()->user()->isManagerial()))
                                                <form action="{{ route('finance.commissions.approve', $comm) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            data-confirm-action
                                                            data-title="Setujui Komisi?"
                                                            data-text="Komisi ini akan disetujui (Approved) dan siap dicairkan oleh Finance."
                                                            data-confirm-text="Ya, Setujui"
                                                            class="px-2.5 py-1 rounded-lg bg-[#B89B5E] hover:bg-[#A3884E] text-white text-xs font-semibold transition-colors">
                                                        Approve
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($comm->status === \App\Enums\CommissionStatus::APPROVED && auth()->user()->isFinance())
                                                <form action="{{ route('finance.commissions.pay', $comm) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            data-confirm-action
                                                            data-title="Cairkan Komisi?"
                                                            data-text="Dana komisi telah berhasil ditransfer ke rekening penerima."
                                                            data-confirm-text="Ya, Tandai Cair"
                                                            class="px-2.5 py-1 rounded-lg bg-[#15803D] hover:bg-[#166534] text-white text-xs font-semibold transition-colors">
                                                        Cairkan
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </x-table>

                        <div class="flex items-center justify-between pt-2 border-t border-[#E8E4DA] text-xs">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('finance.commissions.index') }}" class="text-[#B89B5E] hover:underline font-semibold text-xs flex items-center gap-1">
                                    <span>Buka Dashboard Komisi Seluruh Penjualan</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>

                            @if ((auth()->user()->isFinance() || auth()->user()->isManagerial()) && !$booking->commissions->contains('status', \App\Enums\CommissionStatus::PAID))
                                <form action="{{ route('finance.commissions.generate', $booking) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            data-confirm-action
                                            data-title="Hitung Ulang Komisi?"
                                            data-text="Komisi berstatus Pending / Approved akan dihitung ulang berdasarkan harga final saat ini."
                                            data-confirm-text="Ya, Hitung Ulang"
                                            class="px-2.5 py-1 rounded-lg bg-[#F7F6F2] hover:bg-[#E8E4DA] border border-[#E8E4DA] text-[#161616] text-[11px] font-medium transition-colors">
                                        <i class="fa-solid fa-arrows-rotate mr-1 text-[10px]"></i> Hitung Ulang
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-layouts.app>
