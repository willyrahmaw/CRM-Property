<x-layouts.app title="Executive Dashboard">
    <x-page-header
        title="Executive Analytics Dashboard"
        subtitle="Pantauan real-time performa penjualan, pipeline leads, dan pergerakan unit properti.">
        @if (auth()->user()->isSales() || auth()->user()->isManagerial())
            <x-button variant="gold" icon="fa-solid fa-plus" :href="route('crm.leads.create')">
                Tambah Lead Baru
            </x-button>
        @endif
        <x-button variant="primary" icon="fa-solid fa-map-location-dot" :href="route('inventory.siteplan.index')">
            Interactive Siteplan
        </x-button>
    </x-page-header>

    <!-- Top Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <x-stat-card
            title="Total Nilai Penjualan (Closing)"
            value="Rp {{ number_format($metrics['sales_value'] ?? 0, 0, ',', '.') }}"
            icon="fa-solid fa-rupiah-sign"
            change="+18.5%"
            changeType="positive"
            helper="vs bulan lalu" />

        <x-stat-card
            title="Total Leads Masuk"
            value="{{ number_format($metrics['total_leads'] ?? 0) }}"
            icon="fa-solid fa-users"
            change="{{ $metrics['hot_leads'] ?? 0 }} Hot Leads"
            changeType="positive"
            helper="prioritas follow-up" />

        <x-stat-card
            title="Survei Lokasi (Site Visits)"
            value="{{ number_format($metrics['total_site_visits'] ?? 0) }}"
            icon="fa-solid fa-calendar-check"
            helper="kunjungan terjadwal & selesai" />

        <x-stat-card
            title="Tingkat Konversi (Conversion Rate)"
            value="{{ $metrics['conversion_rate'] ?? 0 }}%"
            icon="fa-solid fa-chart-pie"
            change="Lead to Booking"
            changeType="neutral"
            helper="industri avg: 3.2%" />
    </div>

    <!-- Second Row: Inventory Status & Pipeline Funnel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Inventory Breakdown -->
        <x-card title="Status Inventori Unit" subtitle="Distribusi ketersediaan unit di seluruh proyek">
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                    <div class="flex items-center space-x-3">
                        <span class="w-3 h-3 rounded-full bg-[#15803D]"></span>
                        <span class="text-xs font-semibold text-[#161616]">Available (Siap Jual)</span>
                    </div>
                    <span class="text-sm font-bold text-[#161616]">{{ $metrics['available_units'] ?? 0 }} Unit</span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                    <div class="flex items-center space-x-3">
                        <span class="w-3 h-3 rounded-full bg-[#B89B5E]"></span>
                        <span class="text-xs font-semibold text-[#161616]">Booked (Terpesan)</span>
                    </div>
                    <span class="text-sm font-bold text-[#161616]">{{ $metrics['booked_units'] ?? 0 }} Unit</span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                    <div class="flex items-center space-x-3">
                        <span class="w-3 h-3 rounded-full bg-[#991B1B]"></span>
                        <span class="text-xs font-semibold text-[#161616]">Sold (Terjual / Akad)</span>
                    </div>
                    <span class="text-sm font-bold text-[#161616]">{{ $metrics['sold_units'] ?? 0 }} Unit</span>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-[#E8E4DA] text-right">
                <a href="{{ route('inventory.units.index') }}" class="text-xs font-semibold text-[#B89B5E] hover:underline">
                    Kelola Seluruh Unit <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </x-card>

        <!-- Pipeline Stages Overview -->
        <div class="lg:col-span-2">
            <x-card title="Sales Pipeline Conversion Funnel" subtitle="Distribusi prospek berdasarkan tahapan penjualan aktif">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @php
                        $stageLabels = [
                            'new' => ['name' => 'New', 'color' => 'bg-[#262626]'],
                            'contacted' => ['name' => 'Contacted', 'color' => 'bg-[#79766F]'],
                            'qualified' => ['name' => 'Qualified', 'color' => 'bg-[#D7C49E] text-[#161616]'],
                            'site_visit' => ['name' => 'Site Visit', 'color' => 'bg-[#B89B5E] text-white'],
                            'negotiation' => ['name' => 'Negotiation', 'color' => 'bg-[#D97706] text-white'],
                            'booking' => ['name' => 'Booking', 'color' => 'bg-[#0284C7] text-white'],
                            'won' => ['name' => 'Won', 'color' => 'bg-[#15803D] text-white'],
                            'lost' => ['name' => 'Lost', 'color' => 'bg-[#991B1B] text-white'],
                        ];
                    @endphp

                    @foreach ($stageLabels as $key => $conf)
                        <div class="p-4 rounded-xl border border-[#E8E4DA] bg-white text-center flex flex-col justify-between">
                            <span class="text-[10px] uppercase font-bold tracking-wider text-[#79766F]">{{ $conf['name'] }}</span>
                            <div class="text-2xl font-bold text-[#161616] my-2">
                                {{ $metrics['pipeline_stages'][$key] ?? 0 }}
                            </div>
                            <div class="w-full h-1.5 rounded-full {{ $conf['color'] }}"></div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-4 border-t border-[#E8E4DA] flex items-center justify-between">
                    <span class="text-xs text-[#79766F]">Pergerakan tahapan dievaluasi setiap hari secara berkala.</span>
                    <a href="{{ route('crm.pipeline.index') }}" class="text-xs font-semibold text-[#B89B5E] hover:underline">
                        Buka Kanban Pipeline <i class="fa-solid fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Third Row: Top Performing Sales -->
    @if (isset($metrics['top_sales']) && $metrics['top_sales']->isNotEmpty())
        <x-card title="Leaderboard Sales Agent" subtitle="Sales dengan kontribusi closing terbanyak bulan ini" :padding="false">
            <x-table>
                <thead>
                    <tr>
                        <th>Nama Agent</th>
                        <th>Role</th>
                        <th>Email / Telepon</th>
                        <th class="action-col text-center">Closing Terverifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($metrics['top_sales'] as $agent)
                        <tr>
                            <td>
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-7 h-7 rounded-full bg-[#161616] text-[#B89B5E] flex items-center justify-center text-xs font-bold">
                                        {{ substr($agent->name, 0, 1) }}
                                    </div>
                                    <span class="font-semibold text-[#161616]">{{ $agent->name }}</span>
                                </div>
                            </td>
                            <td class="text-[#79766F]">{{ $agent->role->label() }}</td>
                            <td class="text-[#79766F]">{{ $agent->email }} / {{ $agent->phone ?? '-' }}</td>
                            <td class="action-col text-center">
                                <span class="px-2.5 py-1 rounded-full bg-[#15803D] text-white font-bold text-xs">
                                    {{ $agent->assigned_leads_count }} Closing
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
        </x-card>
    @endif
</x-layouts.app>
