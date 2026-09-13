<x-layouts.app title="Pengaturan Skema Komisi Penjualan">
    <x-page-header
        title="Pengaturan Skema & Kebijakan Komisi"
        subtitle="Konfigurasi tarif komisi penjualan, persentase bagi hasil berjenjang, dan ketentuan pencairan dana khusus Company Owner."
        :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Konfigurasi', 'url' => '#'],
            ['label' => 'Skema Komisi', 'url' => '#'],
        ]">
        <div class="flex items-center gap-2">
            <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('finance.commissions.index')">
                Monitoring Komisi
            </x-button>
        </div>
    </x-page-header>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-[#FEF2F2] border border-[#FCA5A5] text-xs text-[#991B1B]">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Terdapat kesalahan pengisian konfigurasi:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="commission-settings-form" action="{{ route('settings.commissions.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Settings Inputs -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Card 1: Tarif & Skema Bagi Hasil Berjenjang -->
                <x-card title="Tarif & Skema Bagi Hasil Berjenjang" subtitle="Tentukan persentase total komisi dan alokasi untuk Sales Closing, Team Leader, dan Kantor">
                    <div class="space-y-4">
                        <div>
                            <label for="input-total-rate" class="block text-xs font-bold text-[#161616] uppercase mb-1">
                                Total Tarif Komisi Penjualan (%) <span class="text-[#991B1B]">*</span>
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <input type="number"
                                       step="0.05"
                                       min="0.1"
                                       max="25"
                                       id="input-total-rate"
                                       name="total_rate"
                                       value="{{ old('total_rate', $settings['total_rate']) }}"
                                       required
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-xs font-bold text-[#79766F]">
                                    % dari Harga Final Unit
                                </div>
                            </div>
                            <p class="text-[11px] text-[#79766F] mt-1">
                                Standar umum pengembang luxury properti adalah 2.0% s/d 3.0% dari harga final transaksi (setelah diskon).
                            </p>
                        </div>

                        <div class="pt-3 border-t border-[#E8E4DA]">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-[#161616] uppercase">Alokasi Pembagian Komisi Berjenjang</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-[#79766F]">Total Alokasi:</span>
                                    <span id="total-share-sum" class="font-mono font-bold text-xs text-[#161616]">100%</span>
                                    <span id="total-share-badge" class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#15803D] text-white">Tepat 100%</span>
                                </div>
                            </div>
                            <p class="text-[11px] text-[#79766F] mb-3">
                                Persentase di bawah dihitung dari total nominal komisi yang teralokasikan. Jumlah total ketiganya wajib tepat 100%.
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-[#161616] mb-1">
                                        <i class="fa-solid fa-user-tie text-[#B89B5E]"></i>
                                        <span>Sales Closing</span>
                                    </div>
                                    <div class="relative">
                                        <input type="number"
                                               step="0.5"
                                               min="1"
                                               max="100"
                                               id="input-sales-share"
                                               name="sales_share"
                                               value="{{ old('sales_share', $settings['sales_share']) }}"
                                               required
                                               class="w-full px-2.5 py-1.5 text-xs font-bold rounded border border-[#E8E4DA] bg-white text-[#161616]">
                                        <span class="absolute right-2 top-1.5 text-xs text-[#79766F]">%</span>
                                    </div>
                                    <span class="text-[10px] text-[#79766F] block mt-1">PIC pemesanan unit</span>
                                </div>

                                <div class="p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-[#161616] mb-1">
                                        <i class="fa-solid fa-users-gear text-[#B89B5E]"></i>
                                        <span>Team Leader</span>
                                    </div>
                                    <div class="relative">
                                        <input type="number"
                                               step="0.5"
                                               min="0"
                                               max="100"
                                               id="input-tl-share"
                                               name="team_leader_share"
                                               value="{{ old('team_leader_share', $settings['team_leader_share']) }}"
                                               required
                                               class="w-full px-2.5 py-1.5 text-xs font-bold rounded border border-[#E8E4DA] bg-white text-[#161616]">
                                        <span class="absolute right-2 top-1.5 text-xs text-[#79766F]">%</span>
                                    </div>
                                    <span class="text-[10px] text-[#79766F] block mt-1">Supervisor / Koordinator</span>
                                </div>

                                <div class="p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                                    <div class="flex items-center gap-1.5 text-xs font-bold text-[#161616] mb-1">
                                        <i class="fa-solid fa-building text-[#B89B5E]"></i>
                                        <span>Agency / Kantor</span>
                                    </div>
                                    <div class="relative">
                                        <input type="number"
                                               step="0.5"
                                               min="0"
                                               max="100"
                                               id="input-agency-share"
                                               name="agency_share"
                                               value="{{ old('agency_share', $settings['agency_share']) }}"
                                               required
                                               class="w-full px-2.5 py-1.5 text-xs font-bold rounded border border-[#E8E4DA] bg-white text-[#161616]">
                                        <span class="absolute right-2 top-1.5 text-xs text-[#79766F]">%</span>
                                    </div>
                                    <span class="text-[10px] text-[#79766F] block mt-1">Retensi kas pengembang</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>

                <!-- Card 2: Kebijakan Otomatisasi & Syarat Pencairan -->
                <x-card title="Kebijakan Pencairan & Otomatisasi Sistem" subtitle="Atur trigger penerbitan komisi otomatis dan syarat kepatuhan berkas sebelum dana dicairkan">
                    <div class="space-y-4">
                        <!-- Auto Generate Checkbox -->
                        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA]">
                            <div class="pt-0.5">
                                <input type="checkbox"
                                       id="auto_generate_on_booking_fee"
                                       name="auto_generate_on_booking_fee"
                                       value="1"
                                       {{ old('auto_generate_on_booking_fee', $settings['auto_generate_on_booking_fee']) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded text-[#B89B5E] border-[#E8E4DA] focus:ring-[#B89B5E]">
                            </div>
                            <div>
                                <label for="auto_generate_on_booking_fee" class="text-xs font-bold text-[#161616] block cursor-pointer">
                                    Otomatis Terbitkan Draft Komisi saat Booking Fee Terverifikasi
                                </label>
                                <span class="text-[11px] text-[#79766F] block mt-0.5">
                                    Jika diaktifkan, saat tim Finance memverifikasi pembayaran Tanda Jadi (Booking Fee), sistem secara otomatis menghitung dan menerbitkan komisi sales & team leader berstatus <em>Menunggu Approval</em>.
                                </span>
                            </div>
                        </div>

                        <!-- Disbursement Policy Select -->
                        <div>
                            <label for="disbursement_policy" class="block text-xs font-bold text-[#161616] uppercase mb-1">
                                Ketentuan Waktu Pencairan Dana Komisi <span class="text-[#991B1B]">*</span>
                            </label>
                            <select id="disbursement_policy"
                                    name="disbursement_policy"
                                    class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                                <option value="after_booking_fee" {{ old('disbursement_policy', $settings['disbursement_policy']) === 'after_booking_fee' ? 'selected' : '' }}>
                                    Pencairan Cepat: Dapat dicairkan segera setelah Booking Fee lunas & disetujui Manajemen
                                </option>
                                <option value="after_dp_paid" {{ old('disbursement_policy', $settings['disbursement_policy']) === 'after_dp_paid' ? 'selected' : '' }}>
                                    Pencairan Standar: Dapat dicairkan setelah Uang Muka (DP) lunas terverifikasi
                                </option>
                                <option value="after_akad" {{ old('disbursement_policy', $settings['disbursement_policy']) === 'after_akad' ? 'selected' : '' }}>
                                    Pencairan Penuh: Hanya dapat dicairkan setelah Akad Kredit Bank (KPR) / Pelunasan Penuh
                                </option>
                            </select>
                            <p class="text-[11px] text-[#79766F] mt-1">
                                Panduan operasional internal bagi tim Finance saat memproses persetujuan pencairan transfer dana.
                            </p>
                        </div>

                        <!-- Terms and Conditions Textarea -->
                        <div>
                            <label for="terms_and_conditions" class="block text-xs font-bold text-[#161616] uppercase mb-1">
                                SOP & Syarat Dokumen Pengajuan Pencairan Komisi
                            </label>
                            <textarea id="terms_and_conditions"
                                      name="terms_and_conditions"
                                      rows="3"
                                      placeholder="Masukkan syarat administrasi internal bagi agen sales..."
                                      class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">{{ old('terms_and_conditions', $settings['terms_and_conditions']) }}</textarea>
                            <p class="text-[11px] text-[#79766F] mt-0.5">
                                Syarat ini akan menjadi acuan tertulis bagi tim Finance dan Sales Manager sebelum menyetujui pencairan.
                            </p>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Right Col: Interactive Live Calculator & Submit -->
            <div class="space-y-6">
                <!-- Card 3: Simulator Interaktif Real-Time -->
                <x-card title="Simulator Perhitungan Real-Time" subtitle="Pratinjau langsung bagi hasil berdasarkan konfigurasi tarif yang Anda tetapkan">
                    <div class="space-y-4">
                        <div>
                            <label for="input-sim-price" class="block text-[10px] font-bold text-[#161616] uppercase mb-1">
                                Simulasi Harga Unit Properti (Rp)
                            </label>
                            <input type="number"
                                   id="input-sim-price"
                                   value="1500000000"
                                   step="50000000"
                                   class="w-full px-2.5 py-1.5 text-xs font-mono font-bold rounded border border-[#E8E4DA] bg-white text-[#161616]">
                            <span class="text-[10px] text-[#79766F] block mt-0.5">Ubah nominal di atas untuk melihat simulasi kalkulasi</span>
                        </div>

                        <!-- Live Calculation Results -->
                        <div class="p-3.5 rounded-xl bg-[#161616] text-white space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-[#262626]">
                                <span class="text-xs text-[#E8E4DA]">Total Alokasi Komisi</span>
                                <span id="sim-total-comm" class="font-bold text-sm text-[#B89B5E]">Rp 37.500.000</span>
                            </div>

                            <div class="space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="block text-[#E8E4DA] font-semibold">1. Sales Closing</span>
                                        <span id="sim-sales-effective" class="text-[10px] text-[#79766F]">1.50% dari harga</span>
                                    </div>
                                    <span id="sim-sales-comm" class="font-bold text-[#15803D]">Rp 22.500.000</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="block text-[#E8E4DA] font-semibold">2. Team Leader</span>
                                        <span id="sim-tl-effective" class="text-[10px] text-[#79766F]">0.50% dari harga</span>
                                    </div>
                                    <span id="sim-tl-comm" class="font-bold text-white">Rp 7.500.000</span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="block text-[#E8E4DA] font-semibold">3. Kas Agency / Kantor</span>
                                        <span id="sim-agency-effective" class="text-[10px] text-[#79766F]">0.50% dari harga</span>
                                    </div>
                                    <span id="sim-agency-comm" class="font-bold text-[#B89B5E]">Rp 7.500.000</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA] text-xs space-y-1">
                            <div class="flex items-center gap-1.5 font-bold text-[#161616]">
                                <i class="fa-solid fa-circle-info text-[#B89B5E]"></i>
                                <span>Keterangan Skema:</span>
                            </div>
                            <p class="text-[11px] text-[#79766F] leading-relaxed">
                                Seluruh transaksi baru yang dibuat setelah pengaturan ini disimpan akan otomatis menggunakan formula tarif terbaru yang ditetapkan oleh Owner.
                            </p>
                        </div>
                    </div>
                </x-card>

                <!-- Card 4: Action Buttons -->
                <x-card title="Otoritas & Eksekusi">
                    <div class="space-y-3">
                        <p class="text-xs text-[#79766F]">
                            Perubahan skema komisi hanya dapat dilakukan oleh akun dengan hak akses <strong>Company Owner</strong> atau <strong>Super Admin</strong>.
                        </p>

                        <button type="submit"
                                data-confirm-action
                                data-title="Simpan Skema Komisi?"
                                data-text="Konfigurasi tarif komisi dan kebijakan pembagian hasil akan diberlakukan ke seluruh transaksi perusahaan."
                                data-confirm-text="Ya, Terapkan Skema"
                                class="w-full py-2.5 px-4 rounded-lg bg-[#161616] hover:bg-[#262626] text-white text-xs font-bold flex items-center justify-center gap-2 transition-colors shadow-sm">
                            <i class="fa-solid fa-floppy-disk text-[#B89B5E]"></i>
                            <span>Simpan Pengaturan Komisi</span>
                        </button>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</x-layouts.app>
