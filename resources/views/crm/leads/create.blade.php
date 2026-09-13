<x-layouts.app title="Tambah Lead Baru">
    <x-page-header
        title="Pendaftaran Prospek Baru"
        subtitle="Masukkan data calon pembeli potensial ke dalam sistem CRM."
        :breadcrumbs="[
            ['label' => 'CRM', 'url' => route('crm.leads.index')],
            ['label' => 'Leads', 'url' => route('crm.leads.index')],
            ['label' => 'Tambah Lead', 'url' => '#'],
        ]">
        <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('crm.leads.index')">
            Kembali
        </x-button>
    </x-page-header>

    <div class="max-w-4xl">
        <form action="{{ route('crm.leads.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Profil Calon Pembeli -->
            <x-card title="Informasi Calon Pembeli" subtitle="Data identitas dan kontak prospek">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Nama Lengkap <span class="text-[#991B1B]">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                               placeholder="Contoh: Budi Santoso">
                        @error('name')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Nomor WhatsApp / Telepon <span class="text-[#991B1B]">*</span>
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                               placeholder="08123456789">
                        @error('phone')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Alamat Email (Opsional)
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                               placeholder="budi.santoso@gmail.com">
                        @error('email')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <!-- Section 2: Sumber & Marketing Attribution -->
            <x-card title="Sumber Akuisisi & Kampanye" subtitle="Saluran pemasaran tempat calon pembeli berasal">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Sumber Lead <span class="text-[#991B1B]">*</span>
                        </label>
                        <select name="source" required class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            @foreach ($sources as $source)
                                <option value="{{ $source->value }}" {{ old('source') === $source->value ? 'selected' : '' }}>
                                    {{ $source->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('source')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Nama Kampanye / Promo (Opsional)
                        </label>
                        <input type="text" name="campaign" value="{{ old('campaign') }}"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                               placeholder="Contoh: Meta Ads - Promo DP 0%">
                    </div>
                </div>
            </x-card>

            <!-- Section 3: Preferensi Minat Properti & Anggaran -->
            <x-card title="Minat Properti & Anggaran" subtitle="Kebutuhan unit dan kesiapan finansial calon pembeli">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Project Properti yang Diminati
                        </label>
                        <select name="interested_project_id" class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            <option value="">-- Pilih Project --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ old('interested_project_id') === $project->id ? 'selected' : '' }}>
                                    {{ $project->name }} ({{ $project->city }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Tipe Properti / Spesifikasi yang Dicari
                        </label>
                        <input type="text" name="property_type_interest" value="{{ old('property_type_interest') }}"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                               placeholder="Contoh: 2 Lantai, 3 Kamar Tidur">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Budget Minimum (Rp)
                        </label>
                        <input type="number" name="budget_min" value="{{ old('budget_min', 0) }}" min="0" step="1000000"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Budget Maksimum (Rp)
                        </label>
                        <input type="number" name="budget_max" value="{{ old('budget_max', 0) }}" min="0" step="1000000"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Target Rencana Pembelian (Hari)
                        </label>
                        <input type="number" name="purchase_target_days" value="{{ old('purchase_target_days', 30) }}" min="1"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                               placeholder="Contoh: 14 atau 30">
                        <p class="text-[10px] text-[#79766F] mt-1">Mempengaruhi kalkulasi Lead Scoring otomatis.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Penugasan Sales PIC (Opsional)
                        </label>
                        <select name="assigned_sales_id" class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            <option value="">-- Otomatis (Round Robin) --</option>
                            @foreach ($salesAgents as $agent)
                                <option value="{{ $agent->id }}" {{ old('assigned_sales_id') === $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }} ({{ $agent->role->label() }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-[#79766F] mt-1">Jika dikosongkan, sistem akan mengalokasikan sales via Lead Assignment Engine.</p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                        Catatan Tambahan
                    </label>
                    <textarea name="notes" rows="3"
                              class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"
                              placeholder="Kebutuhan khusus calon pembeli, jadwal senggang untuk dihubungi, dll.">{{ old('notes') }}</textarea>
                </div>
            </x-card>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <x-button variant="secondary" :href="route('crm.leads.index')">
                    Batal
                </x-button>
                <x-button type="submit" variant="gold" icon="fa-solid fa-check">
                    Simpan & Daftarkan Prospek
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
