<x-layouts.app title="Daftarkan Pengajuan KPR Baru">
    <x-page-header
        title="Daftarkan Pengajuan KPR Baru"
        subtitle="Registrasikan permohonan pembiayaan bank konsumen ke dalam sistem monitoring KPR."
        :breadcrumbs="[
            ['label' => 'Keuangan', 'url' => route('finance.payments.index')],
            ['label' => 'KPR Management & Akad', 'url' => route('finance.mortgages.index')],
            ['label' => 'Tambah Pengajuan KPR', 'url' => '#'],
        ]">
        <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('finance.mortgages.index')">
            Kembali
        </x-button>
    </x-page-header>

    <div class="max-w-4xl">
        @if ($eligibleBookings->isEmpty())
            <x-card>
                <x-empty-state
                    icon="fa-solid fa-file-invoice-dollar"
                    title="Tidak Ada Transaksi KPR Menunggu"
                    message="Saat ini semua transaksi booking berskema KPR telah terdaftar pada modul manajemen KPR. Transaksi baru yang memilih metode KPR akan otomatis muncul di sini." />
                <div class="flex justify-center pt-4">
                    <x-button variant="outline" size="sm" :href="route('finance.mortgages.index')" icon="fa-solid fa-arrow-left">
                        Kembali ke Daftar KPR
                    </x-button>
                </div>
            </x-card>
        @else
            <form action="{{ route('finance.mortgages.store') }}" method="POST" class="space-y-6">
                @csrf

                <x-card title="Pilih Transaksi Pemesanan" subtitle="Pilih surat pesanan (SPP) konsumen yang mengajukan pembiayaan KPR">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Transaksi Booking Unit <span class="text-[#991B1B]">*</span>
                        </label>
                        <select name="booking_id" required class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] font-medium focus:outline-none focus:border-[#B89B5E]">
                            <option value="">-- Pilih Transaksi Konsumen --</option>
                            @foreach ($eligibleBookings as $elig)
                                <option value="{{ $elig->id }}" {{ old('booking_id', $preselectedBookingId) === $elig->id ? 'selected' : '' }}>
                                    {{ $elig->booking_number }} — {{ $elig->customer->name }} (Unit {{ $elig->propertyUnit->unit_number }}, {{ $elig->propertyUnit->cluster->name }}) — Final Rp {{ number_format($elig->final_price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('booking_id')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>
                </x-card>

                <x-card title="Rincian Permohonan Pembiayaan Bank" subtitle="Informasi bank rekanan penyalur dan plafon kredit yang diajukan">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Bank Rekanan KPR <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="text" name="bank_name" value="{{ old('bank_name') }}" required
                                       placeholder="Contoh: Bank Central Asia (BCA) / Bank Mandiri / BTN"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('bank_name')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Plafon Pengajuan (Rp) <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="number" name="submission_amount" value="{{ old('submission_amount') }}" required min="1000000"
                                       placeholder="Contoh: 850000000"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] font-bold focus:outline-none focus:border-[#B89B5E]">
                                @error('submission_amount')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Tenor (Tahun) <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="number" name="tenor_years" value="{{ old('tenor_years', 15) }}" required min="1" max="40"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('tenor_years')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Suku Bunga (% p.a)
                                </label>
                                <input type="number" step="0.01" name="interest_rate" value="{{ old('interest_rate') }}"
                                       placeholder="Contoh: 6.25"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Tahapan Awal <span class="text-[#991B1B]">*</span>
                                </label>
                                <select name="status" required class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] font-semibold focus:outline-none focus:border-[#B89B5E]">
                                    @foreach ($statuses as $st)
                                        <option value="{{ $st->value }}" {{ old('status') === $st->value ? 'selected' : '' }}>
                                            {{ $st->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Tanggal Masuk Berkas
                                </label>
                                <input type="date" name="application_date" value="{{ old('application_date', date('Y-m-d')) }}"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Plafon Disetujui (Opsional jika sudah ada)
                                </label>
                                <input type="number" name="approved_amount" value="{{ old('approved_amount') }}"
                                       placeholder="Nominal SP3K..."
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Catatan Pengajuan KPR
                            </label>
                            <textarea name="notes" rows="3"
                                      placeholder="Contoh: Berkas KTP, NPWP, slip gaji 3 bulan dan rekening koran telah lengkap diserahkan ke bank."
                                      class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 mt-6 border-t border-[#E8E4DA]">
                        <x-button variant="outline" size="sm" :href="route('finance.mortgages.index')">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary" size="sm" icon="fa-solid fa-paper-plane">
                            Simpan Pengajuan KPR
                        </x-button>
                    </div>
                </x-card>
            </form>
        @endif
    </div>
</x-layouts.app>
