<x-layouts.app title="Manajemen KPR & Akad Kredit">
    <x-page-header
        title="Manajemen KPR & Akad Kredit"
        subtitle="Monitoring status pengajuan pembiayaan bank konsumen, proses SP3K, hingga realisasi akad kredit.">
        <x-button variant="primary" size="sm" icon="fa-solid fa-plus" :href="route('finance.mortgages.create')">
            Daftarkan KPR Baru
        </x-button>
    </x-page-header>

    <!-- Top Metric Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            title="Total Pengajuan KPR"
            :value="$counts['total']"
            icon="fa-solid fa-landmark"
            helper="Seluruh berkas konsumen" />

        <x-stat-card
            title="Sedang Diproses Bank"
            :value="$counts['in_process']"
            icon="fa-solid fa-hourglass-half"
            helper="Berkas / Appraisal Bank" />

        <x-stat-card
            title="SP3K Terbit / Disetujui"
            :value="$counts['sp3k_approved']"
            icon="fa-solid fa-file-circle-check"
            helper="Siap jadwalkan akad" />

        <x-stat-card
            title="Akad Kredit Selesai"
            :value="$counts['contract_signed']"
            icon="fa-solid fa-handshake"
            helper="Realisasi pembiayaan" />
    </div>

    <!-- Filter Bar -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('finance.mortgages.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-[#79766F] uppercase mb-1">Status KPR</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-[#79766F] uppercase mb-1">Bank Penyalur</label>
                <input type="text" name="bank" value="{{ $filters['bank'] ?? '' }}" placeholder="Cari nama bank..."
                       class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-[#79766F] uppercase mb-1">Pencarian Kata Kunci</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Nama konsumen, unit, no SPP..."
                       class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
            </div>

            <div class="flex items-end gap-2">
                <x-button type="submit" variant="primary" size="sm" class="flex-1" icon="fa-solid fa-filter">
                    Filter Data
                </x-button>
                @if (!empty($filters['status']) || !empty($filters['bank']) || !empty($filters['search']))
                    <x-button variant="outline" size="sm" :href="route('finance.mortgages.index')" icon="fa-solid fa-rotate-left">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>
    </x-card>

    <!-- Main Mortgages Table -->
    <x-card :padding="false">
        @if ($mortgages->isEmpty())
            <x-empty-state
                icon="fa-solid fa-landmark"
                title="Belum ada data pengajuan KPR"
                message="Data pengajuan KPR bank dan status akad kredit konsumen akan tampil di sini untuk dikelola oleh tim Finance.">
                <div class="mt-4">
                    <x-button variant="primary" size="sm" icon="fa-solid fa-plus" :href="route('finance.mortgages.create')">
                        Daftarkan Pengajuan KPR
                    </x-button>
                </div>
            </x-empty-state>
        @else
            <x-table>
                <thead>
                    <tr>
                        <th>Konsumen & Kontak</th>
                        <th>Unit & No. SPP</th>
                        <th>Bank Penyalur</th>
                        <th>Plafon Pengajuan / Disetujui</th>
                        <th>Tenor & Bunga</th>
                        <th>Status KPR</th>
                        <th>Tanggal SP3K & Akad</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mortgages as $mortgage)
                        <tr>
                            <td>
                                <span class="font-bold text-[#161616] block">{{ $mortgage->booking->customer->name }}</span>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-[11px] text-[#79766F] font-mono">{{ $mortgage->booking->customer->phone }}</span>
                                    @if ($mortgage->booking->customer->phone)
                                        <a href="{{ $mortgage->booking->customer->getWhatsAppUrl("Halo {$mortgage->booking->customer->salutation} {$mortgage->booking->customer->name}, menginformasikan mengenai progres pengajuan KPR Bank {$mortgage->bank_name} untuk unit {$mortgage->booking->propertyUnit->unit_number}...") }}"
                                           target="_blank"
                                           class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-[#DCFCE7] hover:bg-[#BBF7D0] text-[#15803D] text-[10px] font-bold transition-colors">
                                            <i class="fa-brands fa-whatsapp"></i> WA
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('sales.bookings.show', $mortgage->booking) }}" class="font-bold text-[#161616] hover:text-[#B89B5E] block">
                                    {{ $mortgage->booking->booking_number }}
                                </a>
                                <span class="text-[11px] text-[#79766F] block">
                                    Unit {{ $mortgage->booking->propertyUnit->unit_number }} ({{ $mortgage->booking->propertyUnit->cluster->name }})
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-6 h-6 rounded bg-[#F7F6F2] border border-[#E8E4DA] text-[#B89B5E] flex items-center justify-center text-xs">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </div>
                                    <span class="font-bold text-[#161616] text-xs">{{ $mortgage->bank_name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="text-xs">
                                    <span class="text-[#79766F] block text-[10px] uppercase font-semibold">Pengajuan:</span>
                                    <span class="font-semibold text-[#161616]">Rp {{ number_format($mortgage->submission_amount, 0, ',', '.') }}</span>
                                    @if ($mortgage->approved_amount)
                                        <span class="text-[#15803D] block text-[10px] uppercase font-bold mt-1">Disetujui (SP3K):</span>
                                        <span class="font-black text-[#15803D]">Rp {{ number_format($mortgage->approved_amount, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="text-xs text-[#161616]">
                                    <span class="font-medium block">{{ $mortgage->tenor_years }} Tahun</span>
                                    @if ($mortgage->interest_rate)
                                        <span class="text-[11px] text-[#79766F] block">{{ $mortgage->interest_rate }}% p.a</span>
                                    @endif
                                    @if ($mortgage->estimated_installment)
                                        <span class="text-[10px] text-[#79766F] block mt-0.5">Est. Rp {{ number_format($mortgage->estimated_installment, 0, ',', '.') }}/bln</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold inline-block {{ $mortgage->status->badgeClass() }}">
                                    {{ $mortgage->status->label() }}
                                </span>
                            </td>
                            <td>
                                <div class="text-xs space-y-0.5">
                                    @if ($mortgage->sp3k_date)
                                        <div>
                                            <span class="text-[10px] font-bold text-[#B89B5E] uppercase block">SP3K Terbit:</span>
                                            <span class="text-[#161616]">{{ $mortgage->sp3k_date->translatedFormat('d M Y') }}</span>
                                        </div>
                                    @endif
                                    @if ($mortgage->contract_date)
                                        <div>
                                            <span class="text-[10px] font-bold text-[#15803D] uppercase block">Akad Kredit:</span>
                                            <span class="font-bold text-[#15803D]">{{ $mortgage->contract_date->translatedFormat('d M Y') }}</span>
                                        </div>
                                    @endif
                                    @if (!$mortgage->sp3k_date && !$mortgage->contract_date)
                                        <span class="text-[#79766F] text-[11px] italic">Belum terbit</span>
                                    @endif
                                </div>
                            </td>
                            <td class="action-col">
                                <a href="{{ route('finance.mortgages.edit', $mortgage) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#161616] hover:bg-[#262626] text-white text-xs font-semibold transition-colors whitespace-nowrap">
                                    <i class="fa-solid fa-pen-to-square text-[#B89B5E]"></i>
                                    <span>Update KPR & Akad</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>

            @if ($mortgages->hasPages())
                <div class="prop-table-pagination">
                    {{ $mortgages->links() }}
                </div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
