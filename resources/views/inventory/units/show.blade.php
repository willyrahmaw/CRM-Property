<x-layouts.app :title="'Unit ' . $unit->unit_number . ' — ' . $unit->cluster->name">
    <x-page-header
        :title="'Unit ' . $unit->unit_number . ($unit->block ? ' (Blok ' . $unit->block . ')' : '')"
        :subtitle="$unit->cluster->project->name . ' • Cluster ' . $unit->cluster->name"
        :breadcrumbs="[
            ['label' => 'Inventori', 'url' => route('inventory.units.index')],
            ['label' => 'Unit Properti', 'url' => route('inventory.units.index')],
            ['label' => 'Unit ' . $unit->unit_number, 'url' => '#'],
        ]">
        <div class="flex items-center gap-3">
            <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('inventory.units.index')">
                Kembali
            </x-button>
            <x-button variant="secondary" icon="fa-solid fa-map-location-dot" :href="route('inventory.siteplan.show', $unit->cluster->project)">
                Siteplan Kawasan
            </x-button>
            @if($unit->isAvailable() && (auth()->user()->isSales() || auth()->user()->isManagerial()))
                <x-button variant="gold" icon="fa-solid fa-file-signature" :href="route('sales.bookings.create', ['unit_id' => $unit->id])">
                    Booking Unit Ini
                </x-button>
            @endif
        </div>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Detailed Unit Profile, Specifications & Simulation -->
        <div class="lg:col-span-2 space-y-6">
            <!-- 1. Hero Card: Photo, Pricing & Commercial Status -->
            <div class="bg-white rounded-xl border border-[#E8E4DA] overflow-hidden shadow-sm">
                <div class="relative h-72 sm:h-96 w-full bg-[#161616] overflow-hidden">
                    <img src="{{ $unit->image_url }}" alt="Fasad Unit {{ $unit->unit_number }}" class="w-full h-full object-cover">
                    <div class="absolute top-4 left-4 flex items-center gap-2">
                        <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $unit->status->badgeClass() }} shadow-sm">
                            Status: {{ $unit->status->label() }}
                        </span>
                        @if($unit->certificate_type)
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-[#161616]/80 text-[#D7C49E] border border-white/20 backdrop-blur-sm">
                                <i class="fa-solid fa-certificate text-[10px] mr-1"></i>
                                {{ $unit->certificate_type }}
                            </span>
                        @endif
                    </div>
                    <div class="absolute bottom-4 left-4 right-4 bg-[#161616]/95 backdrop-blur-sm border border-white/10 p-4 rounded-xl text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <span class="text-[10px] text-[#B89B5E] uppercase font-bold tracking-wider block">Harga Jual Resmi (Pricelist)</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-black text-white">Rp {{ number_format($unit->selling_price, 0, ',', '.') }}</span>
                                @if($unit->base_price && $unit->base_price < $unit->selling_price)
                                    <span class="text-xs text-[#79766F] line-through">Rp {{ number_format($unit->base_price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                        @if($unit->promo)
                            <div class="sm:text-right">
                                <span class="px-2.5 py-1 rounded bg-[#B89B5E] text-white text-[10px] font-bold inline-block">
                                    <i class="fa-solid fa-tag mr-1"></i>PROMO KHUSUS UNIT
                                </span>
                                <p class="text-xs text-[#E8E4DA] mt-1">{{ $unit->promo }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. Spesifikasi Fisik Bangunan & Kaveling Lengkap -->
                <div class="p-6 space-y-6">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-[#E8E4DA]">
                            <div>
                                <h3 class="text-sm font-bold text-[#161616] uppercase tracking-wider flex items-center">
                                    <i class="fa-solid fa-house-chimney-window text-[#B89B5E] mr-2"></i>
                                    Spesifikasi Fisik & Bangunan Lengkap
                                </h3>
                                <p class="text-xs text-[#79766F] mt-0.5">Parameter teknis tata ruang, dimensi kaveling, dan utilitas unit</p>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded bg-[#F7F6F2] text-[#161616] border border-[#E8E4DA]">
                                Tipe: {{ $unit->propertyType?->name ?? 'Standard Residence' }}
                            </span>
                        </div>

                        <!-- 6-Grid Key Stats -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 mt-4">
                            <div class="p-3.5 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA] text-center">
                                <i class="fa-solid fa-ruler-combined text-base text-[#B89B5E] mb-1.5 block"></i>
                                <span class="text-[9px] text-[#79766F] uppercase font-bold block">Luas Bangunan</span>
                                <span class="text-sm font-bold text-[#161616]">{{ $unit->building_area }} m²</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA] text-center">
                                <i class="fa-solid fa-draw-polygon text-base text-[#B89B5E] mb-1.5 block"></i>
                                <span class="text-[9px] text-[#79766F] uppercase font-bold block">Luas Tanah</span>
                                <span class="text-sm font-bold text-[#161616]">{{ $unit->land_area }} m²</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA] text-center">
                                <i class="fa-solid fa-bed text-base text-[#B89B5E] mb-1.5 block"></i>
                                <span class="text-[9px] text-[#79766F] uppercase font-bold block">Kamar Tidur</span>
                                <span class="text-sm font-bold text-[#161616]">{{ $unit->bedrooms }} KT</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA] text-center">
                                <i class="fa-solid fa-bath text-base text-[#B89B5E] mb-1.5 block"></i>
                                <span class="text-[9px] text-[#79766F] uppercase font-bold block">Kamar Mandi</span>
                                <span class="text-sm font-bold text-[#161616]">{{ $unit->bathrooms }} KM</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA] text-center">
                                <i class="fa-solid fa-car text-base text-[#B89B5E] mb-1.5 block"></i>
                                <span class="text-[9px] text-[#79766F] uppercase font-bold block">Carport</span>
                                <span class="text-sm font-bold text-[#161616]">{{ $unit->carports ?? 1 }} Mobil</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA] text-center">
                                <i class="fa-solid fa-layer-group text-base text-[#B89B5E] mb-1.5 block"></i>
                                <span class="text-[9px] text-[#79766F] uppercase font-bold block">Lantai</span>
                                <span class="text-sm font-bold text-[#161616]">{{ $unit->floors }} Lt</span>
                            </div>
                        </div>

                        <!-- Technical Dimension & Utilities Detail -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 p-4 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA] text-xs">
                            <div>
                                <span class="text-[#79766F] block mb-0.5 font-medium">Dimensi Kaveling:</span>
                                <span class="font-bold text-[#161616]">{{ $unit->dimension ?? 'Standar Kaveling' }}</span>
                            </div>
                            <div>
                                <span class="text-[#79766F] block mb-0.5 font-medium">Arah Hadap Muka:</span>
                                <span class="font-bold text-[#161616]">{{ $unit->direction ?? 'Menghadap Jalan' }}</span>
                            </div>
                            <div>
                                <span class="text-[#79766F] block mb-0.5 font-medium">Daya Listrik (PLN):</span>
                                <span class="font-bold text-[#161616]">{{ $unit->electricity ?? '2.200 VA' }}</span>
                            </div>
                            <div>
                                <span class="text-[#79766F] block mb-0.5 font-medium">Sumber Air Bersih:</span>
                                <span class="font-bold text-[#161616]">{{ $unit->water_source ?? 'PDAM Kota' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Material Konstruksi & Finishing Arsitektural -->
                    @php
                        $specs = $unit->building_specs ?? [];
                        $hasSpecs = !empty(array_filter($specs));
                    @endphp

                    @if($hasSpecs)
                        <div class="pt-4 border-t border-[#E8E4DA]">
                            <div class="flex items-center space-x-2 pb-2 mb-3">
                                <span class="w-1.5 h-4 bg-[#B89B5E] rounded-full inline-block"></span>
                                <h4 class="text-xs font-bold text-[#161616] uppercase tracking-wider">
                                    Material Konstruksi & Finishing Arsitektural
                                </h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 text-xs">
                                @if(!empty($specs['foundation']))
                                    <div class="p-3.5 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                                        <span class="text-[10px] font-bold text-[#79766F] uppercase block mb-1 flex items-center">
                                            <i class="fa-solid fa-cubes text-[#B89B5E] text-xs mr-1.5"></i>
                                            Pondasi & Struktur
                                        </span>
                                        <p class="font-semibold text-[#161616] leading-relaxed">{{ $specs['foundation'] }}</p>
                                    </div>
                                @endif

                                @if(!empty($specs['wall']))
                                    <div class="p-3.5 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                                        <span class="text-[10px] font-bold text-[#79766F] uppercase block mb-1 flex items-center">
                                            <i class="fa-solid fa-trowel-bricks text-[#B89B5E] text-xs mr-1.5"></i>
                                            Dinding & Cat
                                        </span>
                                        <p class="font-semibold text-[#161616] leading-relaxed">{{ $specs['wall'] }}</p>
                                    </div>
                                @endif

                                @if(!empty($specs['roof']))
                                    <div class="p-3.5 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                                        <span class="text-[10px] font-bold text-[#79766F] uppercase block mb-1 flex items-center">
                                            <i class="fa-solid fa-house-chimney text-[#B89B5E] text-xs mr-1.5"></i>
                                            Rangka & Penutup Atap
                                        </span>
                                        <p class="font-semibold text-[#161616] leading-relaxed">{{ $specs['roof'] }}</p>
                                    </div>
                                @endif

                                @if(!empty($specs['floor']))
                                    <div class="p-3.5 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                                        <span class="text-[10px] font-bold text-[#79766F] uppercase block mb-1 flex items-center">
                                            <i class="fa-solid fa-border-all text-[#B89B5E] text-xs mr-1.5"></i>
                                            Lantai Utama & Kamar
                                        </span>
                                        <p class="font-semibold text-[#161616] leading-relaxed">{{ $specs['floor'] }}</p>
                                    </div>
                                @endif

                                @if(!empty($specs['doors_windows']))
                                    <div class="p-3.5 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                                        <span class="text-[10px] font-bold text-[#79766F] uppercase block mb-1 flex items-center">
                                            <i class="fa-solid fa-door-open text-[#B89B5E] text-xs mr-1.5"></i>
                                            Kusen, Pintu & Jendela
                                        </span>
                                        <p class="font-semibold text-[#161616] leading-relaxed">{{ $specs['doors_windows'] }}</p>
                                    </div>
                                @endif

                                @if(!empty($specs['sanitary']))
                                    <div class="p-3.5 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                                        <span class="text-[10px] font-bold text-[#79766F] uppercase block mb-1 flex items-center">
                                            <i class="fa-solid fa-toilet text-[#B89B5E] text-xs mr-1.5"></i>
                                            Sanitair & Perlengkapan Mandi
                                        </span>
                                        <p class="font-semibold text-[#161616] leading-relaxed">{{ $specs['sanitary'] }}</p>
                                    </div>
                                @endif
                            </div>

                            @if(!empty($specs['smart_features']))
                                <div class="mt-3.5 p-3.5 rounded-lg bg-[#F7F6F2] border border-[#B89B5E]/50">
                                    <span class="text-[10px] font-bold text-[#B89B5E] uppercase block mb-1 flex items-center">
                                        <i class="fa-solid fa-star mr-1.5"></i>
                                        Fasilitas Unit & Fitur Tambahan
                                    </span>
                                    <p class="text-xs font-semibold text-[#161616] leading-relaxed">{{ $specs['smart_features'] }}</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($unit->notes)
                        <div class="p-4 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA]">
                            <span class="text-[10px] font-bold text-[#79766F] uppercase tracking-wider block mb-1">Catatan Khas Kaveling</span>
                            <p class="text-xs text-[#161616] leading-relaxed">{{ $unit->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 3. Financial Breakdown & KPR Simulation -->
            @php
                $price = (float) $unit->selling_price;
                $dp10 = $price * 0.10;
                $dp20 = $price * 0.20;
                $kprPlafon90 = $price * 0.90;
                $monthlyRate = (0.065 / 12); // Asumsi Suku Bunga KPR 6.5% p.a.
                
                // PMT Function = P * r * (1+r)^n / ((1+r)^n - 1)
                $calcInstallment = function($principal, $months) use ($monthlyRate) {
                    $pow = pow(1 + $monthlyRate, $months);
                    return ($principal * $monthlyRate * $pow) / ($pow - 1);
                };

                $cicilan10Thn = $calcInstallment($kprPlafon90, 120);
                $cicilan15Thn = $calcInstallment($kprPlafon90, 180);
                $cicilan20Thn = $calcInstallment($kprPlafon90, 240);
            @endphp

            <x-card title="Simulasi Finansial & Skema Pembayaran" subtitle="Kalkulasi estimasi uang muka, plafon pembiayaan, dan angsuran KPR bank rekanan">
                <div class="space-y-4 text-xs">
                    <!-- Top Summary Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                            <span class="text-[10px] text-[#79766F] font-bold uppercase block">Pricelist Unit</span>
                            <span class="text-base font-black text-[#161616]">Rp {{ number_format($price, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-[#79766F] block mt-0.5">Sudah termasuk PPN</span>
                        </div>

                        <div class="p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                            <span class="text-[10px] text-[#79766F] font-bold uppercase block">Estimasi DP 10%</span>
                            <span class="text-base font-black text-[#161616]">Rp {{ number_format($dp10, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-[#79766F] block mt-0.5">DP 20%: Rp {{ number_format($dp20, 0, ',', '.') }}</span>
                        </div>

                        <div class="p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                            <span class="text-[10px] text-[#79766F] font-bold uppercase block">Plafon KPR (90%)</span>
                            <span class="text-base font-black text-[#B89B5E]">Rp {{ number_format($kprPlafon90, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-[#79766F] block mt-0.5">Maksimal pembiayaan bank</span>
                        </div>
                    </div>

                    <!-- KPR Tenor Table -->
                    <div class="overflow-x-auto rounded-lg border border-[#E8E4DA]">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#F7F6F2] text-[#161616] font-bold border-b border-[#E8E4DA]">
                                <tr>
                                    <th class="p-3">Jangka Waktu (Tenor)</th>
                                    <th class="p-3">Asumsi Bunga Fix</th>
                                    <th class="p-3">Estimasi Angsuran / Bulan</th>
                                    <th class="p-3">Ketentuan Penghasilan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#E8E4DA] text-[#161616]">
                                <tr>
                                    <td class="p-3 font-semibold">10 Tahun (120 Bulan)</td>
                                    <td class="p-3 text-[#79766F]">6.50% p.a. Fix</td>
                                    <td class="p-3 font-bold text-[#161616]">Rp {{ number_format($cicilan10Thn, 0, ',', '.') }} / bln</td>
                                    <td class="p-3 text-[#79766F]">Min. Income Gabungan ± Rp {{ number_format($cicilan10Thn * 2.5, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-semibold">15 Tahun (180 Bulan)</td>
                                    <td class="p-3 text-[#79766F]">6.50% p.a. Fix</td>
                                    <td class="p-3 font-bold text-[#B89B5E]">Rp {{ number_format($cicilan15Thn, 0, ',', '.') }} / bln</td>
                                    <td class="p-3 text-[#79766F]">Min. Income Gabungan ± Rp {{ number_format($cicilan15Thn * 2.5, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td class="p-3 font-semibold">20 Tahun (240 Bulan)</td>
                                    <td class="p-3 text-[#79766F]">6.50% p.a. Fix</td>
                                    <td class="p-3 font-bold text-[#161616]">Rp {{ number_format($cicilan20Thn, 0, ',', '.') }} / bln</td>
                                    <td class="p-3 text-[#79766F]">Min. Income Gabungan ± Rp {{ number_format($cicilan20Thn * 2.5, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Scheme Notes -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-[11px] text-[#79766F]">
                        <div class="p-2.5 rounded bg-[#F7F6F2] border border-[#E8E4DA]">
                            <strong class="text-[#161616] block mb-0.5"><i class="fa-solid fa-money-bill-wave text-[#B89B5E] mr-1"></i> Cash Keras</strong>
                            Pelunasan maks. 30 hari kalender dengan diskon tunai spesial.
                        </div>
                        <div class="p-2.5 rounded bg-[#F7F6F2] border border-[#E8E4DA]">
                            <strong class="text-[#161616] block mb-0.5"><i class="fa-solid fa-calendar-days text-[#B89B5E] mr-1"></i> Cash Bertahap (In-House)</strong>
                            Cicilan langsung ke developer tanpa bunga tenor 12x s.d. 24x.
                        </div>
                        <div class="p-2.5 rounded bg-[#F7F6F2] border border-[#E8E4DA]">
                            <strong class="text-[#161616] block mb-0.5"><i class="fa-solid fa-building-columns text-[#B89B5E] mr-1"></i> KPR Express</strong>
                            Didukung Bank Rekanan: BCA, Mandiri, BNI, BTN, CIMB Niaga, dan BRI.
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- 4. Riwayat Komersial & Aktivitas Unit -->
            <x-card title="Riwayat Komersial & Aktivitas Unit" subtitle="Catatan surat pesanan, transaksi booking, serta kunjungan lapangan">
                <div class="space-y-6">
                    <!-- Bookings / Transaction Section -->
                    <div>
                        <h4 class="text-xs font-bold text-[#161616] uppercase tracking-wider mb-3 flex items-center">
                            <i class="fa-solid fa-file-invoice text-[#B89B5E] mr-2"></i>
                            Surat Pesanan & Riwayat Booking
                        </h4>

                        @if($unit->bookings->isNotEmpty())
                            <div class="overflow-x-auto rounded-lg border border-[#E8E4DA]">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-[#F7F6F2] text-[#161616] font-bold border-b border-[#E8E4DA]">
                                        <tr>
                                            <th class="p-3">No. Booking</th>
                                            <th class="p-3">Konsumen</th>
                                            <th class="p-3">Sales Agent</th>
                                            <th class="p-3">Tanggal</th>
                                            <th class="p-3">Skema Bayar</th>
                                            <th class="p-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#E8E4DA] text-[#161616]">
                                        @foreach($unit->bookings as $booking)
                                            <tr>
                                                <td class="p-3 font-bold text-[#B89B5E]">
                                                    <a href="{{ route('sales.bookings.show', $booking) }}" class="hover:underline">
                                                        {{ $booking->booking_number }}
                                                    </a>
                                                </td>
                                                <td class="p-3 font-medium">{{ $booking->customer->name }}</td>
                                                <td class="p-3 text-[#79766F]">{{ $booking->sales?->name ?? '-' }}</td>
                                                <td class="p-3 text-[#79766F]">{{ $booking->booking_date->format('d M Y') }}</td>
                                                <td class="p-3 font-semibold">{{ $booking->payment_scheme->label() }}</td>
                                                <td class="p-3">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $booking->status->badgeClass() }}">
                                                        {{ $booking->status->label() }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-4 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA] flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white border border-[#E8E4DA] flex items-center justify-center text-[#B89B5E]">
                                    <i class="fa-solid fa-check text-xs"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-[#161616] block">Unit Tersedia Bebas</span>
                                    <p class="text-[11px] text-[#79766F]">Belum ada surat pesanan aktif. Unit ini siap ditawarkan langsung ke konsumen potensial.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Site Visits Section -->
                    <div class="pt-4 border-t border-[#E8E4DA]">
                        <h4 class="text-xs font-bold text-[#161616] uppercase tracking-wider mb-3 flex items-center">
                            <i class="fa-solid fa-person-walking text-[#B89B5E] mr-2"></i>
                            Riwayat Kunjungan Lapangan (Site Visits)
                        </h4>

                        @if($unit->siteVisits->isNotEmpty())
                            <div class="overflow-x-auto rounded-lg border border-[#E8E4DA]">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-[#F7F6F2] text-[#161616] font-bold border-b border-[#E8E4DA]">
                                        <tr>
                                            <th class="p-3">Calon Konsumen / Lead</th>
                                            <th class="p-3">Sales Pendamping</th>
                                            <th class="p-3">Waktu Kunjungan</th>
                                            <th class="p-3">Status</th>
                                            <th class="p-3">Catatan / Feedback</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#E8E4DA] text-[#161616]">
                                        @foreach($unit->siteVisits as $visit)
                                            <tr>
                                                <td class="p-3 font-semibold text-[#161616]">
                                                    {{ $visit->lead?->name ?? 'Konsumen Langsung' }}
                                                </td>
                                                <td class="p-3 text-[#79766F]">{{ $visit->sales?->name ?? '-' }}</td>
                                                <td class="p-3 text-[#79766F]">{{ $visit->visit_date->format('d M Y H:i') }}</td>
                                                <td class="p-3">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $visit->status->badgeClass() }}">
                                                        {{ $visit->status->label() }}
                                                    </span>
                                                </td>
                                                <td class="p-3 text-[#79766F]">{{ $visit->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-3.5 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA] text-center text-xs text-[#79766F]">
                                Belum ada log kunjungan lapangan khusus untuk unit ini.
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right: Master Location, Cluster Facilities & Sales Action -->
        <div class="space-y-6">
            <!-- Location Context -->
            <x-card title="Kawasan & Lokasi" subtitle="Penempatan unit dalam master development">
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Proyek</span>
                        <a href="{{ route('inventory.projects.show', $unit->cluster->project) }}" class="font-bold text-[#B89B5E] hover:underline">
                            {{ $unit->cluster->project->name }}
                        </a>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Cluster</span>
                        <span class="font-bold text-[#161616]">{{ $unit->cluster->name }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Nomor Unit</span>
                        <span class="font-bold text-[#161616]">Unit {{ $unit->unit_number }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Blok Kaveling</span>
                        <span class="font-bold text-[#161616]">Blok {{ $unit->block ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Wilayah / Kota</span>
                        <span class="font-bold text-[#161616]">{{ $unit->cluster->project->city }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Pengembang</span>
                        <span class="font-bold text-[#161616]">{{ $unit->cluster->project->developer_name ?? 'Developer Resmi' }}</span>
                    </div>
                </div>

                <!-- Cluster Environment & Facilities -->
                <div class="mt-5 pt-4 border-t border-[#E8E4DA]">
                    <span class="text-[10px] font-bold text-[#79766F] uppercase tracking-wider block mb-2.5">
                        Fasilitas Kawasan Terpadu
                    </span>
                    <div class="grid grid-cols-2 gap-2 text-[11px] text-[#161616]">
                        <div class="p-2 rounded bg-[#F7F6F2] border border-[#E8E4DA] flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-[#B89B5E]"></i>
                            <span>Security 24 Jam</span>
                        </div>
                        <div class="p-2 rounded bg-[#F7F6F2] border border-[#E8E4DA] flex items-center gap-1.5">
                            <i class="fa-solid fa-torii-gate text-[#B89B5E]"></i>
                            <span>One Gate System</span>
                        </div>
                        <div class="p-2 rounded bg-[#F7F6F2] border border-[#E8E4DA] flex items-center gap-1.5">
                            <i class="fa-solid fa-video text-[#B89B5E]"></i>
                            <span>CCTV Kawasan</span>
                        </div>
                        <div class="p-2 rounded bg-[#F7F6F2] border border-[#E8E4DA] flex items-center gap-1.5">
                            <i class="fa-solid fa-network-wired text-[#B89B5E]"></i>
                            <span>Underground Cable</span>
                        </div>
                        <div class="p-2 rounded bg-[#F7F6F2] border border-[#E8E4DA] flex items-center gap-1.5">
                            <i class="fa-solid fa-person-swimming text-[#B89B5E]"></i>
                            <span>Clubhouse & Pool</span>
                        </div>
                        <div class="p-2 rounded bg-[#F7F6F2] border border-[#E8E4DA] flex items-center gap-1.5">
                            <i class="fa-solid fa-child-reaching text-[#B89B5E]"></i>
                            <span>Children Playground</span>
                        </div>
                    </div>
                </div>

                <!-- Sales Action Buttons -->
                <div class="mt-5 pt-4 border-t border-[#E8E4DA] space-y-2">
                    <a href="{{ route('inventory.siteplan.show', $unit->cluster->project) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] text-xs font-bold text-[#161616] hover:bg-[#E8E4DA] transition-colors">
                        <i class="fa-solid fa-map-location-dot text-[#B89B5E] mr-2"></i> Lihat di Siteplan Interaktif
                    </a>

                    @if($unit->isAvailable())
                        @if(auth()->user()->isSales() || auth()->user()->isManagerial())
                            <a href="{{ route('sales.bookings.create', ['unit_id' => $unit->id]) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-[#B89B5E] text-xs font-bold text-white hover:bg-[#A3884E] transition-colors shadow-sm">
                                <i class="fa-solid fa-file-signature mr-2"></i> Buat Surat Pesanan (SPP)
                            </a>
                        @else
                            <div class="p-3 rounded-lg bg-[#F0FDF4] border border-[#BBF7D0] text-center">
                                <span class="text-xs font-bold text-[#15803D]">Unit Tersedia untuk Dipasarkan</span>
                            </div>
                        @endif
                    @else
                        <div class="p-3 rounded-lg bg-[#FEE2E2] border border-[#FCA5A5] text-center">
                            <span class="text-xs font-bold text-[#991B1B]">Unit sudah terikat transaksi ({{ $unit->status->label() }})</span>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Sales Consultation Card -->
            <x-card title="Bantuan & Konsultasi Unit" subtitle="Informasi koordinasi tim sales & ketersediaan">
                <div class="p-3.5 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA] space-y-2 text-xs">
                    <div class="flex items-center gap-2 text-[#161616] font-semibold">
                        <i class="fa-solid fa-clock-rotate-left text-[#B89B5E]"></i>
                        <span>Estimasi Serah Terima: 12 - 18 Bulan</span>
                    </div>
                    <div class="flex items-center gap-2 text-[#161616] font-semibold">
                        <i class="fa-solid fa-building text-[#B89B5E]"></i>
                        <span>Marketing Gallery: Buka Setiap Hari</span>
                    </div>
                    <p class="text-[11px] text-[#79766F] pt-1 leading-relaxed">
                        Data ketersediaan dan pricelist terintegrasi langsung secara real-time dengan sistem finance & inventory pusat.
                    </p>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.app>
