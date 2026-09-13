<x-layouts.app title="Detail Negosiasi & Permohonan Diskon">
    <x-page-header
        title="Detail Pengajuan Negosiasi & Diskon"
        :subtitle="'Unit ' . $negotiation->propertyUnit->unit_number . ' • ' . $negotiation->lead->name"
        :breadcrumbs="[
            ['label' => 'Penjualan', 'url' => route('sales.bookings.index')],
            ['label' => 'Negosiasi & Diskon', 'url' => route('sales.negotiations.index')],
            ['label' => 'Detail #' . substr($negotiation->id, 0, 8), 'url' => '#'],
        ]">
        <div class="flex items-center gap-3">
            <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('sales.negotiations.index')">
                Kembali
            </x-button>
            @if($negotiation->isApproved() && $negotiation->propertyUnit->isAvailable() && (auth()->user()->isSales() || auth()->user()->isManagerial()))
                <x-button variant="gold" icon="fa-solid fa-file-signature" :href="route('sales.bookings.create', ['unit_id' => $negotiation->property_unit_id, 'discount_amount' => $negotiation->discount_amount])">
                    Lanjut Buat Booking (SPP)
                </x-button>
            @endif
        </div>
    </x-page-header>

    <!-- Top Price Structure Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-[#E8E4DA] p-5 shadow-sm">
            <span class="text-[10px] text-[#79766F] uppercase font-bold tracking-wider block">Harga Pricelist Awal</span>
            <span class="text-xl font-bold text-[#161616] mt-1 block">
                Rp {{ number_format($negotiation->initial_price, 0, ',', '.') }}
            </span>
            <span class="text-[11px] text-[#79766F] mt-1 block">Harga resmi unit kaveling</span>
        </div>

        <div class="bg-white rounded-xl border border-[#E8E4DA] p-5 shadow-sm">
            <span class="text-[10px] text-[#79766F] uppercase font-bold tracking-wider block">Potongan Diskon Diajukan</span>
            <span class="text-xl font-bold text-[#B89B5E] mt-1 block">
                -Rp {{ number_format($negotiation->discount_amount, 0, ',', '.') }}
            </span>
            <span class="text-[11px] text-[#B89B5E] font-semibold mt-1 block">
                {{ $negotiation->discount_percentage }}% dari harga pricelist
            </span>
        </div>

        <div class="bg-white rounded-xl border border-[#E8E4DA] p-5 shadow-sm">
            <span class="text-[10px] text-[#79766F] uppercase font-bold tracking-wider block">Harga Final Konsumen</span>
            <span class="text-xl font-black text-[#15803D] mt-1 block">
                Rp {{ number_format($negotiation->final_price, 0, ',', '.') }}
            </span>
            <span class="text-[11px] text-[#15803D] font-medium mt-1 block">Nominal kesepakatan akhir</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Negotiation Content & Unit Specs -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Negotiation Details Card -->
            <x-card title="Rincian Permohonan Negosiasi" subtitle="Justifikasi dan program promosi yang diajukan sales agent">
                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b border-[#E8E4DA]">
                        <div>
                            <span class="text-[#79766F] block">Status Persetujuan:</span>
                            <span class="inline-block px-2.5 py-1 rounded-full font-bold text-[11px] mt-1 {{ $negotiation->approval_status->badgeClass() }}">
                                {{ $negotiation->approval_status->label() }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[#79766F] block">Program Promo:</span>
                            <span class="font-bold text-[#161616] text-sm mt-1 block">
                                {{ $negotiation->promo_description ?? 'Permohonan Diskon Khusus Konsumen' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="text-[#79766F] font-semibold block mb-1">Catatan & Justifikasi Penawaran:</span>
                        <div class="p-3.5 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA] text-sm text-[#161616] leading-relaxed whitespace-pre-line">
                            {{ $negotiation->notes ?? 'Tidak ada catatan tambahan untuk permohonan negosiasi ini.' }}
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Unit Specification Card -->
            <x-card title="Data Unit Properti yang Dinegosiasikan" subtitle="Objek fisik bangunan dan masterplan kawasan">
                <div class="flex flex-col sm:flex-row gap-5 items-start">
                    <div class="w-full sm:w-44 h-32 rounded-xl overflow-hidden bg-[#161616] border border-[#E8E4DA] shrink-0">
                        <img src="{{ $negotiation->propertyUnit->image_url }}" alt="Unit {{ $negotiation->propertyUnit->unit_number }}" class="w-full h-full object-cover">
                    </div>

                    <div class="flex-1 space-y-3 text-xs w-full">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="text-base font-bold text-[#161616]">Unit {{ $negotiation->propertyUnit->unit_number }}</h4>
                                <p class="text-[#79766F]">
                                    Blok {{ $negotiation->propertyUnit->block ?? '-' }} • {{ $negotiation->propertyUnit->cluster->name }} ({{ $negotiation->propertyUnit->cluster->project->name }})
                                </p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $negotiation->propertyUnit->status->badgeClass() }}">
                                {{ $negotiation->propertyUnit->status->label() }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-[#E8E4DA]">
                            <div class="p-2 rounded bg-[#F7F6F2]">
                                <span class="text-[10px] text-[#79766F] block">LB</span>
                                <span class="font-bold text-[#161616]">{{ $negotiation->propertyUnit->building_area }} m²</span>
                            </div>
                            <div class="p-2 rounded bg-[#F7F6F2]">
                                <span class="text-[10px] text-[#79766F] block">LT</span>
                                <span class="font-bold text-[#161616]">{{ $negotiation->propertyUnit->land_area }} m²</span>
                            </div>
                            <div class="p-2 rounded bg-[#F7F6F2]">
                                <span class="text-[10px] text-[#79766F] block">Kamar</span>
                                <span class="font-bold text-[#161616]">{{ $negotiation->propertyUnit->bedrooms }} KT / {{ $negotiation->propertyUnit->bathrooms }} KM</span>
                            </div>
                            <div class="p-2 rounded bg-[#F7F6F2]">
                                <span class="text-[10px] text-[#79766F] block">Pricelist</span>
                                <span class="font-bold text-[#15803D]">Rp {{ number_format($negotiation->propertyUnit->selling_price / 1000000, 0, ',', '.') }} Jt</span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right: Approval Panel & Contact Info -->
        <div class="space-y-6">
            <!-- Managerial Decision Card -->
            @if(auth()->user()->isManagerial() && $negotiation->isPending())
                <x-card title="Persetujuan Manajerial" subtitle="Wewenang Sales Manager & Direksi">
                    <div class="space-y-4">
                        <div class="p-3 rounded-lg bg-[#FEF3C7] border border-[#FDE68A] text-xs text-[#92400E]">
                            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>
                            Diskon sebesar <strong>Rp {{ number_format($negotiation->discount_amount, 0, ',', '.') }} ({{ $negotiation->discount_percentage }}%)</strong> memerlukan persetujuan sebelum dapat diterbitkan Surat Pesanan (SPP).
                        </div>

                        <!-- Approve Form -->
                        <form action="{{ route('sales.negotiations.approve', $negotiation) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Catatan Persetujuan (Opsional)
                                </label>
                                <input type="text" name="notes" placeholder="Disetujui untuk penutupan penjualan minggu ini..."
                                       class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            </div>

                            <button type="button"
                                    data-confirm-action
                                    data-title="Setujui Diskon Penjualan?"
                                    data-text="Diskon sebesar Rp {{ number_format($negotiation->discount_amount, 0, ',', '.') }} akan disetujui untuk pemesanan unit ini."
                                    data-confirm-text="Ya, Setujui Diskon"
                                    class="w-full py-2.5 rounded-lg bg-[#15803D] hover:bg-[#166534] text-white text-xs font-bold transition-colors flex items-center justify-center">
                                <i class="fa-solid fa-check mr-2"></i> Setujui Diskon Ini
                            </button>
                        </form>

                        <div class="relative flex py-1 items-center">
                            <div class="flex-grow border-t border-[#E8E4DA]"></div>
                            <span class="flex-shrink mx-2 text-[10px] text-[#79766F] uppercase">atau</span>
                            <div class="flex-grow border-t border-[#E8E4DA]"></div>
                        </div>

                        <!-- Reject Form -->
                        <form action="{{ route('sales.negotiations.reject', $negotiation) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Alasan Penolakan Diskon
                                </label>
                                <input type="text" name="reason" placeholder="Diskon melampaui batas margin proyek..."
                                       class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            </div>

                            <button type="button"
                                    data-confirm-action
                                    data-title="Tolak Pengajuan Diskon?"
                                    data-text="Pengajuan potongan harga khusus ini akan ditolak."
                                    data-confirm-text="Ya, Tolak"
                                    data-is-danger="true"
                                    class="w-full py-2 rounded-lg bg-white border border-[#991B1B] text-[#991B1B] hover:bg-[#FEE2E2] text-xs font-semibold transition-colors flex items-center justify-center">
                                <i class="fa-solid fa-xmark mr-2"></i> Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                </x-card>
            @endif

            <!-- Lead / Customer Card -->
            <x-card title="Pihak Konsumen & Sales" subtitle="Data pemohon negosiasi">
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Nama Prospek</span>
                        <a href="{{ route('crm.leads.show', $negotiation->lead) }}" class="font-bold text-[#161616] hover:text-[#B89B5E]">
                            {{ $negotiation->lead->name }}
                        </a>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Nomor Telepon</span>
                        <span class="font-medium text-[#161616]">{{ $negotiation->lead->phone ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Sales Agent</span>
                        <span class="font-bold text-[#161616]">{{ $negotiation->sales->name }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F]">Tanggal Pengajuan</span>
                        <span class="text-[#161616]">{{ $negotiation->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($negotiation->approver)
                        <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                            <span class="text-[#79766F]">Diverifikasi Oleh</span>
                            <span class="font-bold text-[#161616]">{{ $negotiation->approver->name }}</span>
                        </div>
                    @endif
                </div>

                @if($negotiation->isApproved() && $negotiation->propertyUnit->isAvailable())
                    <div class="mt-4 pt-3 border-t border-[#E8E4DA]">
                        <a href="{{ route('sales.bookings.create', ['unit_id' => $negotiation->property_unit_id, 'discount_amount' => $negotiation->discount_amount]) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-[#B89B5E] text-xs font-bold text-white hover:bg-[#A3884E] transition-colors shadow-sm">
                            <i class="fa-solid fa-file-signature mr-2"></i> Terbitkan SPP Booking Sekarang
                        </a>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-layouts.app>
