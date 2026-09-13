<x-layouts.app :title="'Update KPR — ' . $mortgage->booking->customer->name">
    <x-page-header
        :title="'Update KPR & Akad — ' . $mortgage->booking->customer->name"
        :subtitle="'Transaksi ' . $mortgage->booking->booking_number . ' • Unit ' . $mortgage->booking->propertyUnit->unit_number . ' (' . $mortgage->booking->propertyUnit->cluster->name . ')'"
        :breadcrumbs="[
            ['label' => 'Keuangan', 'url' => route('finance.payments.index')],
            ['label' => 'KPR Management & Akad', 'url' => route('finance.mortgages.index')],
            ['label' => 'Update ' . $mortgage->bank_name, 'url' => '#'],
        ]">
        <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('finance.mortgages.index')">
            Kembali
        </x-button>
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Column: Summary & Status Overview (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Current Status Card -->
            <x-card title="Tahapan KPR Saat Ini">
                <div class="space-y-3.5">
                    <div class="flex items-center justify-between pb-3 border-b border-[#E8E4DA]">
                        <span class="text-xs text-[#79766F]">Status Saat Ini</span>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $mortgage->status->badgeClass() }}">
                            {{ $mortgage->status->label() }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pb-3 border-b border-[#E8E4DA]">
                        <span class="text-xs text-[#79766F]">Bank Penyalur</span>
                        <div class="flex items-center gap-1.5">
                            <i class="fa-solid fa-building-columns text-[#B89B5E] text-xs"></i>
                            <span class="font-bold text-[#161616] text-xs">{{ $mortgage->bank_name }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pb-3 border-b border-[#E8E4DA]">
                        <span class="text-xs text-[#79766F]">Plafon Pengajuan</span>
                        <span class="font-bold text-[#161616] text-xs">Rp {{ number_format($mortgage->submission_amount, 0, ',', '.') }}</span>
                    </div>

                    @if ($mortgage->approved_amount)
                        <div class="flex items-center justify-between pb-3 border-b border-[#E8E4DA]">
                            <span class="text-xs text-[#15803D] font-semibold">Plafon Disetujui (SP3K)</span>
                            <span class="font-black text-[#15803D] text-xs">Rp {{ number_format($mortgage->approved_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-xs text-[#79766F]">
                        <span>Tenor & Bunga</span>
                        <span class="font-semibold text-[#161616]">
                            {{ $mortgage->tenor_years }} Tahun @if($mortgage->interest_rate) • {{ $mortgage->interest_rate }}% p.a @endif
                        </span>
                    </div>
                </div>
            </x-card>

            <!-- Customer & Unit Card -->
            <x-card title="Informasi Pemesan & Unit">
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-[#79766F] block text-[10px] uppercase font-bold">Nama Konsumen</span>
                        <span class="text-sm font-bold text-[#161616]">{{ $mortgage->booking->customer->name }}</span>
                        @if ($mortgage->booking->customer->gender)
                            <span class="text-[11px] text-[#79766F] block">Sapaan: {{ $mortgage->booking->customer->salutation }}</span>
                        @endif
                    </div>

                    <div>
                        <span class="text-[#79766F] block text-[10px] uppercase font-bold mb-1">WhatsApp Konsumen</span>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-[#161616]">{{ $mortgage->booking->customer->phone }}</span>
                            @if ($mortgage->booking->customer->phone)
                                <a href="{{ $mortgage->booking->customer->getWhatsAppUrl("Halo {$mortgage->booking->customer->salutation} {$mortgage->booking->customer->name}, kami dari Finance ingin menginformasikan pembaruan progres pengajuan KPR Bank {$mortgage->bank_name} untuk unit {$mortgage->booking->propertyUnit->unit_number}...") }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#DCFCE7] hover:bg-[#BBF7D0] text-[#15803D] text-[11px] font-bold transition-colors">
                                    <i class="fa-brands fa-whatsapp text-sm"></i>
                                    <span>Chat WA</span>
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[#E8E4DA]">
                        <span class="text-[#79766F] block text-[10px] uppercase font-bold">Kaveling Properti</span>
                        <span class="font-bold text-[#161616] text-xs">Unit {{ $mortgage->booking->propertyUnit->unit_number }}</span>
                        <span class="text-[11px] text-[#79766F] block">{{ $mortgage->booking->propertyUnit->cluster->name }} — {{ $mortgage->booking->propertyUnit->cluster->project->name }}</span>
                    </div>

                    <div class="pt-2 border-t border-[#E8E4DA] flex justify-between">
                        <span class="text-[#79766F]">Harga Pengikatan:</span>
                        <span class="font-bold text-[#161616]">Rp {{ number_format($mortgage->booking->final_price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Important Dates Card -->
            <x-card title="Jadwal & Milestone Penting">
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-[#79766F]">Masuk Berkas:</span>
                        <span class="font-medium text-[#161616]">{{ $mortgage->application_date?->translatedFormat('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#79766F]">Appraisal Bank:</span>
                        <span class="font-medium text-[#161616]">{{ $mortgage->appraisal_date?->translatedFormat('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[#79766F]">Surat SP3K:</span>
                        <span class="font-bold text-[#B89B5E]">{{ $mortgage->sp3k_date?->translatedFormat('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-[#E8E4DA]">
                        <span class="text-[#15803D] font-bold">Akad Kredit:</span>
                        <span class="font-black text-[#15803D]">{{ $mortgage->contract_date?->translatedFormat('d F Y') ?? 'Belum Akad' }}</span>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Right Column: Full Edit Form (8 Cols) -->
        <div class="lg:col-span-8">
            <form action="{{ route('finance.mortgages.update', $mortgage) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <x-card title="Formulir Pembaruan Progres KPR" subtitle="Ubah tahapan status, nominal persetujuan bank, dan jadwal realisasi akad kredit">
                    <div class="space-y-4">
                        <!-- Tahapan KPR Dropdown -->
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Status Tahapan KPR <span class="text-[#991B1B]">*</span>
                            </label>
                            <select name="status" required class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] font-bold focus:outline-none focus:border-[#B89B5E]">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" {{ old('status', $mortgage->status->value) === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bank & Plafon Pengajuan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Bank Penyalur KPR <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="text" name="bank_name" value="{{ old('bank_name', $mortgage->bank_name) }}" required
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('bank_name')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Plafon Diajukan (Rp) <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="number" name="submission_amount" value="{{ old('submission_amount', (int)$mortgage->submission_amount) }}" required
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('submission_amount')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- SP3K: Plafon Disetujui & Tanggal SP3K -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA]">
                            <div>
                                <label class="block text-xs font-semibold text-[#15803D] uppercase mb-1">
                                    Plafon Disetujui Bank / SP3K (Rp)
                                </label>
                                <input type="number" name="approved_amount" value="{{ old('approved_amount', $mortgage->approved_amount ? (int)$mortgage->approved_amount : null) }}"
                                       placeholder="Contoh: 850000000"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] font-bold focus:outline-none focus:border-[#B89B5E]">
                                <span class="text-[10px] text-[#79766F] block mt-1">Isi jika surat penegasan persetujuan kredit (SP3K) telah terbit.</span>
                                @error('approved_amount')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Tanggal Terbit SP3K
                                </label>
                                <input type="date" name="sp3k_date" value="{{ old('sp3k_date', $mortgage->sp3k_date?->toDateString()) }}"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                <span class="text-[10px] text-[#79766F] block mt-1">Otomatis diisi tanggal hari ini jika status dipilih Disetujui (SP3K).</span>
                                @error('sp3k_date')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Tenor, Bunga, Estimasi Cicilan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Tenor (Tahun) <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="number" name="tenor_years" value="{{ old('tenor_years', $mortgage->tenor_years) }}" required min="1" max="40"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('tenor_years')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Suku Bunga (% p.a)
                                </label>
                                <input type="number" step="0.01" name="interest_rate" value="{{ old('interest_rate', $mortgage->interest_rate) }}"
                                       placeholder="6.50"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('interest_rate')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Est. Cicilan (Rp/bln)
                                </label>
                                <input type="number" name="estimated_installment" value="{{ old('estimated_installment', $mortgage->estimated_installment ? (int)$mortgage->estimated_installment : null) }}"
                                       placeholder="Contoh: 7500000"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('estimated_installment')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Realisasi Akad Kredit Highlight Box -->
                        <div class="p-4 rounded-xl bg-[#F7F6F2] border-2 border-[#B89B5E] space-y-3">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-handshake text-[#B89B5E] text-lg"></i>
                                <div>
                                    <span class="font-bold text-xs text-[#161616] uppercase block">Pelaksanaan Akad Kredit (Contract Signing)</span>
                                    <span class="text-[11px] text-[#79766F]">Tahap puncak realisasi pembiayaan bank dan penandatanganan akta notaris</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                                <div>
                                    <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                        Tanggal Realisasi Akad Kredit
                                    </label>
                                    <input type="date" name="contract_date" value="{{ old('contract_date', $mortgage->contract_date?->toDateString()) }}"
                                           class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] font-bold focus:outline-none focus:border-[#B89B5E]">
                                    @error('contract_date')
                                        <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                        Tanggal Masuk Berkas
                                    </label>
                                    <input type="date" name="application_date" value="{{ old('application_date', $mortgage->application_date?->toDateString()) }}"
                                           class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Catatan Analis Kredit / Bank Rekanan
                            </label>
                            <textarea name="notes" rows="3"
                                      placeholder="Contoh: Appraisal sudah selesai, berkas disetujui tanpa syarat tambahan. Notaris rekanan: Kantor Notaris Bambang SH."
                                      class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">{{ old('notes', $mortgage->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 mt-6 border-t border-[#E8E4DA]">
                        <x-button variant="outline" size="sm" :href="route('finance.mortgages.index')">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary" size="sm" icon="fa-solid fa-floppy-disk">
                            Simpan Pembaruan KPR & Akad
                        </x-button>
                    </div>
                </x-card>
            </form>
        </div>
    </div>
</x-layouts.app>
