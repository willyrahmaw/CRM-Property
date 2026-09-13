<x-layouts.app title="Laporan Eksekutif & Kinerja Penjualan">
    <x-page-header
        title="Laporan Eksekutif & Analitik"
        subtitle="Analisis realisasi penjualan, serapan unit per kawasan, efektivitas funneling leads, dan peringkat sales.">
        <div class="flex items-center gap-2">
            <x-button variant="outline" icon="fa-solid fa-file-excel" href="#">
                Ekspor Excel
            </x-button>
            <x-button variant="gold" icon="fa-solid fa-print" href="#">
                Cetak Laporan
            </x-button>
        </div>
    </x-page-header>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            title="Total Nilai Penjualan"
            :value="'Rp ' . number_format($financials['total_revenue'] / 1000000000, 2, ',', '.') . ' M'"
            icon="fa-solid fa-vault"
            helper="Dari seluruh unit berstatus BOOKED / SOLD" />

        <x-stat-card
            title="Total SPP Closing"
            :value="$financials['total_bookings'] . ' Unit'"
            icon="fa-solid fa-file-signature"
            helper="Pesanan unit yang telah disetujui" />

        <x-stat-card
            title="Uang Kas Masuk (Inflow)"
            :value="'Rp ' . number_format($financials['cash_collected'] / 1000000, 0, ',', '.') . ' Jt'"
            icon="fa-solid fa-receipt"
            helper="Total pembayaran terverifikasi finance" />

        <x-stat-card
            title="Total Prospek Masuk"
            :value="$funnel['total']"
            icon="fa-solid fa-users"
            helper="Akumulasi lead di seluruh saluran promosi" />
    </div>

    <!-- Funnel Pipeline Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-1">
            <x-card title="Funnel Konversi Prospek" subtitle="Tahapan konversi dari lead mentah hingga closing">
                <div class="space-y-4 text-xs">
                    <!-- Step 1: All Leads -->
                    <div class="p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-[#161616]">1. Total Lead Masuk</span>
                            <span class="font-bold text-[#161616]">{{ $funnel['total'] }}</span>
                        </div>
                        <div class="w-full bg-[#E8E4DA] rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#161616] h-1.5 rounded-full" style="width: 100%"></div>
                        </div>
                    </div>

                    <!-- Step 2: Contacted / Qualified -->
                    <div class="p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-[#161616]">2. Berhasil Dihubungi</span>
                            <span class="font-bold text-[#161616]">{{ $funnel['contacted'] }}</span>
                        </div>
                        <div class="w-full bg-[#E8E4DA] rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#79766F] h-1.5 rounded-full" style="width: {{ $funnel['total'] > 0 ? round(($funnel['contacted'] / $funnel['total']) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-[10px] text-[#79766F] mt-1 block">Konversi: {{ $funnel['total'] > 0 ? round(($funnel['contacted'] / $funnel['total']) * 100, 1) : 0 }}%</span>
                    </div>

                    <!-- Step 3: Site Visit -->
                    <div class="p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-[#161616]">3. Hadir Survei Lokasi</span>
                            <span class="font-bold text-[#161616]">{{ $funnel['site_visit'] }}</span>
                        </div>
                        <div class="w-full bg-[#E8E4DA] rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#B89B5E] h-1.5 rounded-full" style="width: {{ $funnel['total'] > 0 ? round(($funnel['site_visit'] / $funnel['total']) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-[10px] text-[#79766F] mt-1 block">Konversi: {{ $funnel['total'] > 0 ? round(($funnel['site_visit'] / $funnel['total']) * 100, 1) : 0 }}%</span>
                    </div>

                    <!-- Step 4: Booking & Won -->
                    <div class="p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-bold text-[#161616]">4. Closing SPP Booking</span>
                            <span class="font-bold text-[#15803D]">{{ $funnel['booking'] }}</span>
                        </div>
                        <div class="w-full bg-[#E8E4DA] rounded-full h-1.5 overflow-hidden">
                            <div class="bg-[#15803D] h-1.5 rounded-full" style="width: {{ $funnel['total'] > 0 ? round(($funnel['booking'] / $funnel['total']) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-[10px] text-[#15803D] font-semibold mt-1 block">Final Conversion: {{ $funnel['total'] > 0 ? round(($funnel['booking'] / $funnel['total']) * 100, 1) : 0 }}%</span>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Project Absorption Table -->
        <div class="lg:col-span-2">
            <x-card title="Laju Serapan Unit per Kawasan Proyek" subtitle="Tingkat keterjualan unit properti di setiap proyek kawasan">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="text-[#79766F] uppercase border-b border-[#E8E4DA] bg-[#F7F6F2]">
                            <tr>
                                <th class="py-2.5 px-3">Nama Proyek</th>
                                <th class="py-2.5 px-3 text-center">Total Unit</th>
                                <th class="py-2.5 px-3 text-center">Terjual</th>
                                <th class="py-2.5 px-3 text-center">Sisa Stok</th>
                                <th class="py-2.5 px-3">Laju Serapan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#E8E4DA]">
                            @forelse ($projectSummaries as $proj)
                                <tr class="hover:bg-[#F7F6F2]/50">
                                    <td class="py-3 px-3">
                                        <span class="font-bold text-[#161616] block">{{ $proj['name'] }}</span>
                                        <span class="text-[10px] text-[#79766F]">{{ $proj['location'] }}</span>
                                    </td>
                                    <td class="py-3 px-3 text-center font-semibold text-[#161616]">{{ $proj['total_units'] }}</td>
                                    <td class="py-3 px-3 text-center font-bold text-[#15803D]">{{ $proj['sold_units'] }}</td>
                                    <td class="py-3 px-3 text-center font-semibold text-[#79766F]">{{ $proj['available_units'] }}</td>
                                    <td class="py-3 px-3">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-24 bg-[#E8E4DA] rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-[#B89B5E] h-1.5 rounded-full" style="width: {{ $proj['absorption_rate'] }}%"></div>
                                            </div>
                                            <span class="font-bold text-[#161616]">{{ $proj['absorption_rate'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-[#79766F]">Belum ada data proyek.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Sales Leaderboard Ranking -->
    <x-card title="Peringkat Kinerja Tim Sales (Leaderboard)" subtitle="Volume pencapaian omzet penjualan dan jumlah closing unit per agen">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="text-[#79766F] uppercase border-b border-[#E8E4DA] bg-[#F7F6F2]">
                    <tr>
                        <th class="py-2.5 px-3 text-center w-12">No</th>
                        <th class="py-2.5 px-3">Nama Sales Agent</th>
                        <th class="py-2.5 px-3 text-center">Lead Ditangani</th>
                        <th class="py-2.5 px-3 text-center">Unit Closing</th>
                        <th class="py-2.5 px-3 text-right">Volume Penjualan</th>
                        <th class="py-2.5 px-3 text-center">Tingkat Closing</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8E4DA]">
                    @forelse ($salesLeaderboard as $index => $item)
                        <tr class="hover:bg-[#F7F6F2]/50">
                            <td class="py-3 px-3 text-center font-bold {{ $index === 0 ? 'text-[#B89B5E]' : 'text-[#79766F]' }}">
                                @if ($index === 0)
                                    <i class="fa-solid fa-crown text-[#B89B5E]"></i>
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-[#161616] text-[#B89B5E] text-xs font-bold flex items-center justify-center">
                                        {{ substr($item['agent']->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-[#161616] block">{{ $item['agent']->name }}</span>
                                        <span class="text-[10px] text-[#79766F]">{{ $item['agent']->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-center font-semibold text-[#161616]">{{ $item['leads_count'] }}</td>
                            <td class="py-3 px-3 text-center font-bold text-[#15803D]">{{ $item['closing_count'] }}</td>
                            <td class="py-3 px-3 text-right font-extrabold text-[#161616]">
                                Rp {{ number_format($item['sales_volume'], 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $item['closing_count'] > 0 ? 'bg-[#DCFCE7] text-[#15803D]' : 'bg-[#F7F6F2] text-[#79766F]' }}">
                                    {{ $item['leads_count'] > 0 ? round(($item['closing_count'] / $item['leads_count']) * 100, 1) : 0 }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-[#79766F]">Belum ada data penjualan tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
