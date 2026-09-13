<x-layouts.app title="Formulir Booking Unit">
    <x-page-header
        title="Formulir Pemesanan Unit Properti"
        subtitle="Buat surat pesanan resmi, validasi ketersediaan unit, dan kunci unit dari perebutan sales lain."
        :breadcrumbs="[
            ['label' => 'Penjualan', 'url' => route('sales.bookings.index')],
            ['label' => 'Bookings', 'url' => route('sales.bookings.index')],
            ['label' => 'Buat Booking', 'url' => '#'],
        ]">
        <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('sales.bookings.index')">
            Kembali
        </x-button>
    </x-page-header>

    <div class="max-w-4xl">
        <form action="{{ route('sales.bookings.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Pemilihan Unit Properti -->
            <x-card title="Pilih Unit Properti" subtitle="Hanya unit dengan status Available yang dapat dipesan">
                <div>
                    <label class="block text-xs font-semibold text-[#161616] uppercase mb-1.5">
                        Unit Tersedia <span class="text-[#991B1B]">*</span>
                    </label>
                    <select name="property_unit_id" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        <option value="">-- Pilih Unit Properti --</option>
                        @foreach ($availableUnits as $unit)
                            <option value="{{ $unit->id }}" {{ old('property_unit_id', $selectedUnitId) === $unit->id ? 'selected' : '' }}>
                                Unit {{ $unit->unit_number }} (Blok {{ $unit->block ?? '-' }}) — {{ $unit->cluster->name }} ({{ $unit->cluster->project->name }}) — Rp {{ number_format($unit->selling_price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_unit_id')
                        <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                    @enderror
                </div>
            </x-card>

            <!-- Section 2: Data Konsumen / Pembeli -->
            <x-card title="Data Calon Pembeli (Konsumen)" subtitle="Gunakan data konsumen/prospek yang sudah ada, atau daftarkan konsumen baru">
                <div class="space-y-4">
                    <!-- Dropdown Pilihan Data yang Sudah Ada -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if ($customers->isNotEmpty())
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    <i class="fa-solid fa-address-book text-[#B89B5E] mr-1"></i>
                                    Pilih dari Konsumen Terdaftar (Opsional)
                                </label>
                                <select name="customer_id" id="booking-customer-select" class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                    <option value="">-- Buat Konsumen Baru di Bawah --</option>
                                    @foreach ($customers as $c)
                                        <option value="{{ $c->id }}"
                                                data-name="{{ $c->name }}"
                                                data-gender="{{ $c->gender?->value ?? '' }}"
                                                data-phone="{{ $c->phone }}"
                                                data-email="{{ $c->email ?? '' }}"
                                                data-nik="{{ $c->nik ?? '' }}"
                                                {{ old('customer_id', $selectedCustomerId) === $c->id ? 'selected' : '' }}>
                                            {{ $c->name }} ({{ $c->phone }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        @if (isset($leads) && $leads->isNotEmpty())
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    <i class="fa-solid fa-user-group text-[#B89B5E] mr-1"></i>
                                    Atau Pilih dari Prospek Aktif / Leads (Opsional)
                                </label>
                                <select name="lead_id" id="booking-lead-select" class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                    <option value="">-- Tanpa Relasi Lead --</option>
                                    @foreach ($leads as $l)
                                        <option value="{{ $l->id }}"
                                                data-name="{{ $l->name }}"
                                                data-gender="{{ $l->gender?->value ?? '' }}"
                                                data-phone="{{ $l->phone }}"
                                                data-email="{{ $l->email ?? '' }}"
                                                data-nik=""
                                                {{ old('lead_id', $selectedLeadId) === $l->id ? 'selected' : '' }}>
                                            {{ $l->name }} ({{ $l->phone }}) — {{ $l->status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div id="booking-selected-info" class="hidden p-3 rounded-lg bg-[#F7F6F2] border border-[#B89B5E] text-xs text-[#161616]">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle-check text-[#15803D]"></i>
                            <span>Menggunakan data terdaftar: <strong id="booking-selected-name"></strong> (<span id="booking-selected-phone"></span>). Kolom di bawah terisi otomatis atau boleh dikosongkan.</span>
                        </div>
                    </div>

                    <div class="relative flex py-1 items-center">
                        <div class="flex-grow border-t border-[#E8E4DA]"></div>
                        <span class="flex-shrink mx-4 text-[10px] uppercase font-bold text-[#79766F] bg-white px-2 py-0.5 rounded border border-[#E8E4DA]">
                            Formulir Identitas Calon Pembeli
                        </span>
                        <div class="flex-grow border-t border-[#E8E4DA]"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Nama Lengkap Konsumen <span id="customer-name-required-mark" class="text-[#991B1B]">*</span>
                            </label>
                            <input type="text" name="customer_name" id="booking-customer-name" value="{{ old('customer_name', request('customer_name')) }}"
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                                   placeholder="Sesuai KTP (Otomatis jika memilih data di atas)">
                            @error('customer_name')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Nomor WhatsApp / Telepon <span id="customer-phone-required-mark" class="text-[#991B1B]">*</span>
                            </label>
                            <input type="text" name="customer_phone" id="booking-customer-phone" value="{{ old('customer_phone', request('customer_phone')) }}"
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                                   placeholder="08123456789 (Otomatis jika memilih data di atas)">
                            @error('customer_phone')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Jenis Kelamin (Sapaan WA)
                            </label>
                            <select name="customer_gender" id="booking-customer-gender" class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                <option value="">-- Pilih Jenis Kelamin (Default: Bapak/Ibu) --</option>
                                <option value="male" {{ old('customer_gender') === 'male' ? 'selected' : '' }}>Laki-laki (Sapaan: Bapak)</option>
                                <option value="female" {{ old('customer_gender') === 'female' ? 'selected' : '' }}>Perempuan (Sapaan: Ibu)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                NIK KTP (Opsional)
                            </label>
                            <input type="text" name="customer_nik" id="booking-customer-nik" value="{{ old('customer_nik') }}"
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                                   placeholder="16 digit NIK">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Email (Opsional)
                            </label>
                            <input type="email" name="customer_email" id="booking-customer-email" value="{{ old('customer_email') }}"
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                                   placeholder="konsumen@email.com">
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Section 3: Skema Finansial Pemesanan -->
            <x-card title="Ketentuan Pembayaran & Booking Fee" subtitle="Aturan nominal tanda jadi dan diskon yang disepakati">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Nominal Booking Fee (Rp) <span class="text-[#991B1B]">*</span>
                        </label>
                        <input type="number" name="booking_fee" value="{{ old('booking_fee', 10000000) }}" min="1000000" step="500000" required
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        @error('booking_fee')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Diskon yang Disetujui (Rp)
                        </label>
                        <input type="number" name="discount_amount" value="{{ old('discount_amount', 0) }}" min="0" step="1000000"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Skema Pembayaran <span class="text-[#991B1B]">*</span>
                        </label>
                        <select name="payment_scheme" required class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            @foreach ($schemes as $scheme)
                                <option value="{{ $scheme->value }}" {{ old('payment_scheme') === $scheme->value ? 'selected' : '' }}>
                                    {{ $scheme->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                        Catatan Transaksi / Permintaan Khusus
                    </label>
                    <textarea name="notes" rows="2"
                              class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                              placeholder="Bonus kanopi, AC 2 unit, atau ketentuan pembayaran bertahap...">{{ old('notes') }}</textarea>
                </div>
            </x-card>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <x-button variant="secondary" :href="route('sales.bookings.index')">
                    Batal
                </x-button>
                <x-button type="submit" variant="gold" icon="fa-solid fa-lock">
                    Kunci Unit & Buat Booking
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
