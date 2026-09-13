<x-layouts.app title="Jadwalkan Site Visit Baru">
    <x-page-header
        title="Jadwalkan Site Visit & Survei Lokasi"
        subtitle="Agendakan pendampingan prospek konsumen untuk survei unit dan masterplan perumahan."
        :breadcrumbs="[
            ['label' => 'CRM', 'url' => route('crm.leads.index')],
            ['label' => 'Site Visits', 'url' => route('crm.site-visits.index')],
            ['label' => 'Jadwalkan Kunjungan', 'url' => '#'],
        ]">
        <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('crm.site-visits.index')">
            Kembali
        </x-button>
    </x-page-header>

    <div class="max-w-3xl">
        <form action="{{ route('crm.site-visits.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Data Prospek & Proyek Tujuan -->
            <x-card title="1. Pihak Konsumen & Kawasan Tujuan" subtitle="Pilih prospek dan proyek yang akan dikunjungi">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Prospek Konsumen (Lead) <span class="text-[#991B1B]">*</span>
                        </label>
                        <select name="lead_id" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            <option value="">-- Pilih Prospek Konsumen --</option>
                            @foreach ($leads as $lead)
                                <option value="{{ $lead->id }}" {{ (old('lead_id', $selectedLead?->id) === $lead->id) ? 'selected' : '' }}>
                                    {{ $lead->name }} ({{ $lead->phone ?? '-' }}) • Sales: {{ $lead->assignedSales?->name ?? 'Belum Ditugaskan' }}
                                </option>
                            @endforeach
                        </select>
                        @error('lead_id')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Proyek Perumahan Tujuan <span class="text-[#991B1B]">*</span>
                            </label>
                            <select name="project_id" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                <option value="">-- Pilih Proyek Kawasan --</option>
                                @foreach ($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ (old('project_id', $selectedProject?->id) === $proj->id) ? 'selected' : '' }}>
                                        {{ $proj->name }} ({{ $proj->city }})
                                    </option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Unit Kaveling Minat (Opsional)
                            </label>
                            <select name="property_unit_id" class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                <option value="">-- Show Unit / Umum --</option>
                                @foreach ($units as $u)
                                    <option value="{{ $u->id }}" {{ old('property_unit_id') === $u->id ? 'selected' : '' }}>
                                        Unit {{ $u->unit_number }} ({{ $u->cluster->name }} - {{ $u->cluster->project->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Section 2: Waktu Kunjungan & Catatan -->
            <x-card title="2. Jadwal Waktu & Catatan Pendampingan" subtitle="Tentukan hari dan jam survei serta preferensi konsumen">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Tanggal & Waktu Kunjungan <span class="text-[#991B1B]">*</span>
                        </label>
                        <input type="datetime-local" name="visit_date"
                               value="{{ old('visit_date', now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i')) }}"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               required
                               class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        <p class="text-[11px] text-[#79766F] mt-1">Pilih tanggal dan jam rencana survei lokasi.</p>
                        @error('visit_date')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Catatan Khusus / Kebutuhan Konsumen
                        </label>
                        <textarea name="notes" rows="3"
                                  placeholder="Konsumen ingin melihat rumah contoh tipe hook, membawa keluarga 3 orang, perlu disiapkan simulasi KPR BCA..."
                                  class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <x-button variant="secondary" :href="route('crm.site-visits.index')">
                    Batal
                </x-button>
                <x-button type="submit" variant="gold" icon="fa-solid fa-calendar-check">
                    Jadwalkan Site Visit
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
