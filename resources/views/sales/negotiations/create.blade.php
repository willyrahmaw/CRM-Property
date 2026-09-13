<x-layouts.app title="Ajukan Negosiasi & Diskon Baru">
    <x-page-header
        title="Ajukan Negosiasi & Permohonan Diskon"
        subtitle="Daftarkan penawaran harga khusus konsumen untuk persetujuan pimpinan (Sales Manager / Direksi)."
        :breadcrumbs="[
            ['label' => 'Penjualan', 'url' => route('sales.bookings.index')],
            ['label' => 'Negosiasi & Diskon', 'url' => route('sales.negotiations.index')],
            ['label' => 'Ajukan Negosiasi', 'url' => '#'],
        ]">
        <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('sales.negotiations.index')">
            Kembali
        </x-button>
    </x-page-header>

    <div class="max-w-3xl">
        <form action="{{ route('sales.negotiations.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Data Prospek & Unit -->
            <x-card title="1. Pihak Konsumen & Unit Pilihan" subtitle="Tentukan prospek dan kaveling properti yang dinegosiasikan">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Prospek Konsumen (Lead) <span class="text-[#991B1B]">*</span>
                        </label>
                        <select name="lead_id" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            <option value="">-- Pilih Prospek Konsumen --</option>
                            @foreach ($leads as $lead)
                                <option value="{{ $lead->id }}" {{ (old('lead_id', $selectedLead?->id) === $lead->id) ? 'selected' : '' }}>
                                    {{ $lead->name }} ({{ $lead->phone ?? 'Tanpa No. HP' }}) • Sales: {{ $lead->assignedSales?->name ?? 'Belum Ditugaskan' }}
                                </option>
                            @endforeach
                        </select>
                        @error('lead_id')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Unit Properti yang Dinegosiasikan <span class="text-[#991B1B]">*</span>
                        </label>
                        <select name="property_unit_id" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            <option value="">-- Pilih Unit Properti --</option>
                            @foreach ($units as $u)
                                <option value="{{ $u->id }}" {{ (old('property_unit_id', $selectedUnit?->id) === $u->id) ? 'selected' : '' }}>
                                    Unit {{ $u->unit_number }} (Blok {{ $u->block ?? '-' }}) — {{ $u->cluster->project->name }} (Pricelist: Rp {{ number_format($u->selling_price, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('property_unit_id')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <!-- Section 2: Struktur Penawaran & Diskon -->
            <x-card title="2. Struktur Penawaran & Permohonan Diskon" subtitle="Rincian nominal penawaran konsumen atau potongan harga khusus">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Harga Penawaran Konsumen (Rp)
                            </label>
                            <input type="number" name="customer_offer_price" value="{{ old('customer_offer_price') }}" min="10000000" step="1000000"
                                   placeholder="Contoh: 820000000"
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] font-bold focus:outline-none focus:border-[#B89B5E]">
                            <p class="text-[11px] text-[#79766F] mt-1">Masukkan jika konsumen menawar angka harga total tertentu.</p>
                            @error('customer_offer_price')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                ATAU Nominal Diskon yang Diajukan (Rp)
                            </label>
                            <input type="number" name="discount_amount" value="{{ old('discount_amount') }}" min="0" step="500000"
                                   placeholder="Contoh: 25000000"
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] font-bold focus:outline-none focus:border-[#B89B5E]">
                            <p class="text-[11px] text-[#79766F] mt-1">Potongan harga tunai yang dimohonkan.</p>
                            @error('discount_amount')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Kategori Program / Promo Terkait
                        </label>
                        <input type="text" name="promo_description" value="{{ old('promo_description') }}"
                               placeholder="Contoh: Promo Early Bird Gathering, Subsidi DP 5%, Promo Hari Kemerdekaan"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Alasan / Catatan Justifikasi Penawaran
                        </label>
                        <textarea name="notes" rows="3"
                                  placeholder="Konsumen siap bayar tanda jadi hari ini jika disetujui diskon 25 juta, pembayaran skema cash bertahap 12x..."
                                  class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <x-button variant="secondary" :href="route('sales.negotiations.index')">
                    Batal
                </x-button>
                <x-button type="submit" variant="gold" icon="fa-solid fa-paper-plane">
                    Kirim Pengajuan Negosiasi
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
