<x-layouts.app :title="$lead->name . ' — Dossier Prospek'">
    <!-- Page Header & Action Bar -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <nav class="flex items-center space-x-2 text-xs text-[#79766F] mb-1.5">
                <a href="{{ route('dashboard') }}" class="hover:text-[#161616] transition-colors">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-[#79766F]/60"></i>
                <a href="{{ route('crm.leads.index') }}" class="hover:text-[#161616] transition-colors">CRM Leads</a>
                <i class="fa-solid fa-chevron-right text-[9px] text-[#79766F]/60"></i>
                <span class="text-[#161616] font-semibold">{{ $lead->name }}</span>
            </nav>
            <div class="flex items-center gap-3">
                <h1 class="text-xl md:text-2xl font-bold text-[#161616] tracking-tight">Dossier Prospek</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-[#F7F6F2] border border-[#E8E4DA] text-[#79766F]">
                    {{ $lead->code }}
                </span>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <x-button variant="outline" size="sm" icon="fa-solid fa-arrow-left" :href="route('crm.leads.index')">
                Kembali
            </x-button>

            <x-button variant="outline" size="sm" icon="fa-solid fa-calendar-plus" :href="route('crm.site-visits.create', ['lead_id' => $lead->id, 'project_id' => $lead->interested_project_id])">
                Survei Lokasi
            </x-button>

            <x-button variant="outline" size="sm" icon="fa-solid fa-tags" :href="route('sales.negotiations.create', ['lead_id' => $lead->id])">
                Ajukan Diskon
            </x-button>

            <x-button variant="gold" size="sm" icon="fa-solid fa-file-signature" :href="route('sales.bookings.create', ['customer_name' => $lead->name, 'customer_phone' => $lead->phone, 'lead_id' => $lead->id])">
                Booking SPP
            </x-button>
        </div>
    </div>

    <!-- Executive Dossier Hero Card -->
    <div class="bg-white rounded-xl border border-[#E8E4DA] p-5 sm:p-6 mb-6 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left: Profile Info -->
            <div class="flex items-start sm:items-center space-x-4">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl bg-[#161616] text-[#B89B5E] text-xl font-bold border-2 border-[#B89B5E] flex items-center justify-center flex-shrink-0 shadow-sm">
                    {{ strtoupper(substr($lead->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <h2 class="text-xl sm:text-2xl font-bold text-[#161616] tracking-tight truncate">
                            {{ $lead->name }}
                        </h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $lead->status->badgeClass() }}">
                            {{ $lead->status->label() }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $lead->temperature->badgeClass() }}">
                            <i class="fa-solid fa-fire text-[10px] mr-1"></i>{{ $lead->temperature->label() }}
                        </span>
                        @if ($lead->campaign)
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-[#F7F6F2] border border-[#E8E4DA] text-[#79766F]">
                                <i class="fa-solid fa-bullhorn text-[10px] mr-1"></i>{{ $lead->campaign }}
                            </span>
                        @endif
                    </div>

                    <!-- Quick Contact Strip -->
                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-[#79766F]">
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}?text=Halo%20{{ urlencode($lead->name) }}%2C%20terima%20kasih%20telah%20menghubungi%20kami."
                           target="_blank"
                           class="inline-flex items-center font-semibold text-[#15803D] hover:text-[#166534] transition-colors bg-[#DCFCE7] border border-[#BBF7D0] px-2.5 py-1 rounded-lg">
                            <i class="fa-brands fa-whatsapp mr-1.5 text-sm"></i>
                            {{ $lead->phone }}
                        </a>

                        @if ($lead->email)
                            <a href="mailto:{{ $lead->email }}" class="inline-flex items-center hover:text-[#161616] transition-colors">
                                <i class="fa-regular fa-envelope mr-1.5 text-[#B89B5E]"></i>
                                {{ $lead->email }}
                            </a>
                        @endif

                        <span class="inline-flex items-center">
                            <i class="fa-regular fa-calendar-check mr-1.5 text-[#79766F]"></i>
                            Terdaftar: {{ $lead->created_at->translatedFormat('d M Y, H:i') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right: Executive Score & Timing Pills -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 border-t lg:border-t-0 lg:border-l border-[#E8E4DA] pt-4 lg:pt-0 lg:pl-6">
                <!-- Qualification Score -->
                <div class="bg-[#F7F6F2] rounded-lg p-3 border border-[#E8E4DA]">
                    <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Skor Kualifikasi</span>
                    <div class="flex items-baseline space-x-1.5">
                        <span class="text-xl font-extrabold text-[#161616]">{{ $lead->score }}</span>
                        <span class="text-xs font-semibold text-[#79766F]">/ 100</span>
                    </div>
                    <div class="w-full bg-[#E8E4DA] rounded-full h-1.5 mt-2 overflow-hidden">
                        <div class="bg-[#B89B5E] h-1.5 rounded-full" style="width: {{ min(100, max(0, $lead->score)) }}%"></div>
                    </div>
                </div>

                <!-- Target Timing -->
                <div class="bg-[#F7F6F2] rounded-lg p-3 border border-[#E8E4DA]">
                    <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Target Beli</span>
                    <div class="text-xl font-extrabold text-[#161616]">
                        {{ $lead->purchase_target_days ? $lead->purchase_target_days . ' Hari' : 'Fleksibel' }}
                    </div>
                    <span class="block text-[10px] text-[#79766F] mt-1">Estimasi Keputusan</span>
                </div>

                <!-- Budget Overview -->
                <div class="col-span-2 sm:col-span-1 bg-[#F7F6F2] rounded-lg p-3 border border-[#E8E4DA]">
                    <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Alokasi Budget</span>
                    <div class="text-sm font-extrabold text-[#161616] truncate">
                        Rp {{ number_format($lead->budget_min / 1000000, 0, ',', '.') }}jt - {{ number_format($lead->budget_max / 1000000, 0, ',', '.') }}jt
                    </div>
                    <span class="block text-[10px] text-[#79766F] mt-1">Rentang Dana</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Column: Profile & Specifications (4 Columns) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Property Preference Card -->
            <x-card title="Preferensi & Alokasi Properti" subtitle="Kriteria properti idaman yang dicari konsumen">
                <div class="space-y-3.5 text-xs">
                    <div class="flex items-start justify-between pb-3 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F] font-medium">Proyek Kawasan</span>
                        <div class="text-right font-bold text-[#161616]">
                            @if ($lead->interestedProject)
                                <a href="{{ route('inventory.projects.show', $lead->interestedProject) }}" class="text-[#B89B5E] hover:underline flex items-center justify-end">
                                    <i class="fa-solid fa-building mr-1"></i>
                                    {{ $lead->interestedProject->name }}
                                </a>
                            @else
                                <span class="text-[#79766F]">Belum ditentukan spesifik</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start justify-between pb-3 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F] font-medium">Tipe Properti</span>
                        <span class="font-bold text-[#161616] text-right">
                            {{ $lead->property_type_interest ?? 'Semua Tipe Properti' }}
                        </span>
                    </div>

                    <div class="flex items-start justify-between pb-3 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F] font-medium">Rentang Budget</span>
                        <span class="font-bold text-[#161616] text-right">
                            Rp {{ number_format($lead->budget_min, 0, ',', '.') }} <br>
                            <span class="text-[11px] font-normal text-[#79766F]">s/d Rp {{ number_format($lead->budget_max, 0, ',', '.') }}</span>
                        </span>
                    </div>

                    <div class="flex items-start justify-between pb-3 border-b border-[#E8E4DA]">
                        <span class="text-[#79766F] font-medium">Sumber Lead</span>
                        <span class="font-semibold text-[#161616] capitalize">
                            <i class="fa-solid fa-bullhorn text-[#B89B5E] mr-1"></i>
                            {{ $lead->source?->label() ?? $lead->source?->value ?? 'Direct / Organic' }}
                        </span>
                    </div>

                    @if ($lead->notes)
                        <div class="pt-1">
                            <span class="text-[11px] font-semibold text-[#79766F] block mb-1.5">Catatan Minat & Kebutuhan Khusus:</span>
                            <div class="bg-[#F7F6F2] border-l-2 border-[#B89B5E] p-3 rounded-r-lg text-xs text-[#161616] italic leading-relaxed">
                                "{{ $lead->notes }}"
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Sales Agent PIC Card -->
            <x-card title="Sales PIC Penanggung Jawab" subtitle="Petugas yang mendampingi follow-up prospek">
                @if ($lead->assignedSales)
                    <div class="flex items-center space-x-3.5 p-3 rounded-xl bg-[#F7F6F2] border border-[#E8E4DA]">
                        <div class="w-11 h-11 rounded-full bg-[#161616] text-[#B89B5E] font-bold text-sm flex items-center justify-center flex-shrink-0 border border-[#B89B5E]/50">
                            {{ strtoupper(substr($lead->assignedSales->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-[#161616] truncate">{{ $lead->assignedSales->name }}</h4>
                            <p class="text-[11px] text-[#79766F] truncate">{{ $lead->assignedSales->email }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-white border border-[#E8E4DA] text-[#161616]">
                                    {{ $lead->assignedSales->role->label() }}
                                </span>
                                @if ($lead->assignedSales->phone)
                                    <a href="tel:{{ $lead->assignedSales->phone }}" class="text-[11px] text-[#B89B5E] hover:underline font-medium">
                                        <i class="fa-solid fa-phone text-[10px] mr-0.5"></i> {{ $lead->assignedSales->phone }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4 px-3 rounded-xl bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E]">
                        <i class="fa-solid fa-circle-exclamation text-xl mb-1 text-[#D97706]"></i>
                        <p class="text-xs font-semibold">Belum Ditugaskan ke Sales</p>
                        <p class="text-[11px] text-[#B45309] mt-0.5">Prospek ini menunggu penugasan dari Sales Manager / Team Leader.</p>
                    </div>
                @endif
            </x-card>

            <!-- Update Pipeline Stage Form Card -->
            <x-card title="Kelola Tahapan Pipeline" subtitle="Perbarui perkembangan negosiasi & status prospek">
                <form action="{{ route('crm.leads.update-status', $lead) }}" method="POST" class="space-y-3.5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] mb-1.5">Tahapan Baru</label>
                        <select name="status" class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] font-medium">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" {{ $lead->status === $status ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] mb-1.5">
                            Alasan Kegagalan (Wajib jika status LOST)
                        </label>
                        <input type="text" name="lost_reason" value="{{ $lead->lost_reason }}"
                               placeholder="Contoh: Harga melebihi plafon, KPR ditolak, kompetitor..."
                               class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] mb-1.5">Catatan Perubahan</label>
                        <textarea name="notes" rows="2" placeholder="Tuliskan alasan atau ringkasan perubahan status..."
                                  class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"></textarea>
                    </div>

                    <x-button type="submit" variant="primary" size="sm" class="w-full" icon="fa-solid fa-rotate">
                        Perbarui Status Prospek
                    </x-button>
                </form>
            </x-card>
        </div>

        <!-- Right Column: Activities, Site Visits & Negotiations Hub (8 Columns) -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Fast Activity Logger Card -->
            <x-card title="Catat Follow-Up & Progres Baru" subtitle="Rekam riwayat komunikasi atau rencanakan follow-up mendatang">
                <form action="{{ route('crm.activities.store') }}" method="POST" class="space-y-3.5">
                    @csrf
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] mb-1.5">Jenis Aktivitas</label>
                            <select name="activity_type" required class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] font-medium">
                                <option value="whatsapp">WhatsApp Message</option>
                                <option value="call">Panggilan Telepon</option>
                                <option value="meeting">Meeting Tatap Muka</option>
                                <option value="site_visit">Survei Lokasi (Site Visit)</option>
                                <option value="email">Email Komersial</option>
                                <option value="note">Catatan Internal Sales</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] mb-1.5">Waktu Aktivitas</label>
                            <input type="datetime-local" name="activity_date" value="{{ now()->format('Y-m-d\TH:i') }}" required
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] mb-1.5">Ringkasan / Hasil Komunikasi</label>
                        <input type="text" name="result" placeholder="Contoh: Diskusi denah 2 lantai, menanyakan promo subsidi DP dan simulasi KPR..."
                               class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] mb-1.5">Catatan Lengkap Percakapan</label>
                            <textarea name="notes" rows="2" placeholder="Rincian poin keberatan, preferensi kavling, dsb..."
                                      class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] mb-1.5">Jadwal Follow-Up Lanjutan (Reminder)</label>
                            <input type="datetime-local" name="next_follow_up_date"
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            <p class="text-[10px] text-[#79766F] mt-1">Sistem otomatis menampilkan pengingat di dashboard sales penanggung jawab.</p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-1">
                        <x-button type="submit" variant="gold" size="sm" icon="fa-solid fa-paper-plane">
                            Simpan Catatan Follow-Up
                        </x-button>
                    </div>
                </form>
            </x-card>

            <!-- Interaction & History Hub (Tabbed System) -->
            <div class="bg-white rounded-xl border border-[#E8E4DA] overflow-hidden shadow-xs">
                <!-- Tab Navigation Bar -->
                <div class="p-4 border-b border-[#E8E4DA] flex flex-wrap items-center justify-between gap-3 bg-[#F7F6F2]/60">
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                                data-tab-target="#tab-timeline"
                                data-tab-group="lead-dossier"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-[#161616] text-white shadow-sm"
                                aria-selected="true">
                            <i class="fa-solid fa-clock-rotate-left mr-1.5"></i>
                            Riwayat Follow-Up
                            <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-white/20 text-white">
                                {{ $lead->activities->count() }}
                            </span>
                        </button>

                        <button type="button"
                                data-tab-target="#tab-site-visits"
                                data-tab-group="lead-dossier"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-[#F7F6F2] text-[#79766F] hover:text-[#161616] hover:bg-[#E8E4DA]"
                                aria-selected="false">
                            <i class="fa-solid fa-map-location-dot mr-1.5 text-[#B89B5E]"></i>
                            Survei Lokasi (Visits)
                            <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-[#E8E4DA] text-[#161616]">
                                {{ $lead->siteVisits->count() }}
                            </span>
                        </button>

                        <button type="button"
                                data-tab-target="#tab-negotiations"
                                data-tab-group="lead-dossier"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-[#F7F6F2] text-[#79766F] hover:text-[#161616] hover:bg-[#E8E4DA]"
                                aria-selected="false">
                            <i class="fa-solid fa-tags mr-1.5 text-[#B89B5E]"></i>
                            Negosiasi & Diskon
                            <span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-[#E8E4DA] text-[#161616]">
                                {{ $lead->negotiations->count() }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Tab Panel 1: Activity Timeline -->
                <div id="tab-timeline" data-tab-panel-group="lead-dossier" class="p-5 sm:p-6">
                    @if ($lead->activities->isEmpty())
                        <x-empty-state
                            icon="fa-regular fa-clock"
                            title="Belum ada riwayat aktivitas"
                            message="Catatan follow-up, telepon, chat WhatsApp, atau perubahan status akan otomatis terangkum secara kronologis di sini." />
                    @else
                        <div class="relative pl-6 sm:pl-8 space-y-6 before:absolute before:left-3 sm:before:left-4 before:top-2 before:bottom-2 before:w-[2px] before:bg-[#E8E4DA]">
                            @foreach ($lead->activities as $act)
                                <div class="relative">
                                    <!-- Activity Type Icon Node -->
                                    <div class="absolute -left-6 sm:-left-8 top-0.5 w-6 h-6 sm:w-7 sm:h-7 rounded-full bg-white border border-[#E8E4DA] flex items-center justify-center shadow-xs">
                                        @if ($act->activity_type === 'whatsapp')
                                            <i class="fa-brands fa-whatsapp text-xs text-[#15803D]"></i>
                                        @elseif ($act->activity_type === 'call')
                                            <i class="fa-solid fa-phone text-[10px] text-[#0369A1]"></i>
                                        @elseif ($act->activity_type === 'meeting')
                                            <i class="fa-solid fa-handshake text-[10px] text-[#B45309]"></i>
                                        @elseif ($act->activity_type === 'site_visit')
                                            <i class="fa-solid fa-map-location-dot text-[10px] text-[#7E22CE]"></i>
                                        @elseif ($act->activity_type === 'status_change')
                                            <i class="fa-solid fa-rotate text-[10px] text-[#B89B5E]"></i>
                                        @else
                                            <i class="fa-solid fa-comment-dots text-[10px] text-[#161616]"></i>
                                        @endif
                                    </div>

                                    <!-- Content Body -->
                                    <div class="bg-[#F7F6F2] border border-[#E8E4DA] rounded-xl p-4 transition-all hover:border-[#D7C49E]">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1.5">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-bold text-[#161616]">
                                                    {{ $act->result ?? ucfirst($act->activity_type) }}
                                                </span>
                                                <span class="px-2 py-0.2 rounded text-[10px] font-semibold bg-white border border-[#E8E4DA] text-[#79766F] uppercase">
                                                    {{ $act->activity_type }}
                                                </span>
                                            </div>
                                            <span class="text-[11px] text-[#79766F]">
                                                {{ $act->activity_date->translatedFormat('d M Y, H:i') }}
                                                <span class="text-[10px] text-[#79766F]/70">({{ $act->activity_date->diffForHumans() }})</span>
                                            </span>
                                        </div>

                                        @if ($act->notes)
                                            <p class="text-xs text-[#161616] leading-relaxed bg-white/70 p-2.5 rounded-lg border border-[#E8E4DA]/60 mt-2">
                                                {{ $act->notes }}
                                            </p>
                                        @endif

                                        <div class="mt-3 pt-2.5 border-t border-[#E8E4DA] flex flex-wrap items-center justify-between gap-2 text-[11px] text-[#79766F]">
                                            <span class="inline-flex items-center">
                                                <i class="fa-regular fa-user mr-1 text-[#B89B5E]"></i>
                                                {{ $act->user?->name ?? 'Sistem' }}
                                            </span>

                                            @if ($act->next_follow_up_date)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-[#FEF3C7] border border-[#FDE68A] text-[#92400E] font-semibold text-[10px]">
                                                    <i class="fa-regular fa-calendar-plus mr-1"></i>
                                                    Reminder: {{ $act->next_follow_up_date->translatedFormat('d M Y H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Tab Panel 2: Site Visits (Survei Lokasi) -->
                <div id="tab-site-visits" data-tab-panel-group="lead-dossier" class="hidden p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#E8E4DA]">
                        <div>
                            <h4 class="text-sm font-bold text-[#161616]">Agenda Survei Lokasi & Show Unit</h4>
                            <p class="text-xs text-[#79766F]">Daftar kunjungan fisik ke lokasi proyek oleh prospek ini</p>
                        </div>
                        <x-button variant="gold" size="sm" icon="fa-solid fa-calendar-plus" :href="route('crm.site-visits.create', ['lead_id' => $lead->id, 'project_id' => $lead->interested_project_id])">
                            Jadwalkan Kunjungan Baru
                        </x-button>
                    </div>

                    @if ($lead->siteVisits->isEmpty())
                        <x-empty-state
                            icon="fa-solid fa-map-location-dot"
                            title="Belum ada agenda kunjungan lokasi"
                            message="Jadwalkan agenda kunjungan survei rumah contoh dan kawasan untuk mempercepat kualifikasi prospek ini.">
                            <x-button variant="outline" size="sm" icon="fa-solid fa-calendar-plus" :href="route('crm.site-visits.create', ['lead_id' => $lead->id, 'project_id' => $lead->interested_project_id])">
                                Jadwalkan Kunjungan Sekarang
                            </x-button>
                        </x-empty-state>
                    @else
                        <div class="space-y-3">
                            @foreach ($lead->siteVisits as $visit)
                                <div class="bg-[#F7F6F2] rounded-xl border border-[#E8E4DA] p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-[#B89B5E] transition-colors">
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-bold text-[#161616]">
                                                <i class="fa-solid fa-building mr-1 text-[#B89B5E]"></i>
                                                {{ $visit->project?->name ?? 'Proyek' }}
                                                @if ($visit->propertyUnit)
                                                    <span class="font-semibold text-[#79766F]">• Unit {{ $visit->propertyUnit->unit_number }}</span>
                                                @endif
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $visit->status->badgeClass() }}">
                                                {{ $visit->status->label() }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-[#79766F] flex items-center space-x-4">
                                            <span>
                                                <i class="fa-regular fa-calendar mr-1"></i>
                                                {{ $visit->visit_date->translatedFormat('d F Y, H:i') }}
                                            </span>
                                            @if ($visit->sales)
                                                <span>
                                                    <i class="fa-regular fa-user mr-1"></i>
                                                    PIC: {{ $visit->sales->name }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($visit->result || $visit->notes)
                                            <p class="text-xs text-[#161616] mt-1 italic">
                                                "{{ $visit->result ?? $visit->notes }}"
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <x-button variant="outline" size="sm" :href="route('crm.site-visits.index', ['search' => $lead->name])">
                                            Kelola Status
                                        </x-button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Tab Panel 3: Negotiations & Discounts -->
                <div id="tab-negotiations" data-tab-panel-group="lead-dossier" class="hidden p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#E8E4DA]">
                        <div>
                            <h4 class="text-sm font-bold text-[#161616]">Pengajuan Negosiasi & Diskon</h4>
                            <p class="text-xs text-[#79766F]">Riwayat penawaran harga khusus atau promo unit untuk prospek ini</p>
                        </div>
                        <x-button variant="gold" size="sm" icon="fa-solid fa-tags" :href="route('sales.negotiations.create', ['lead_id' => $lead->id])">
                            Ajukan Diskon Baru
                        </x-button>
                    </div>

                    @if ($lead->negotiations->isEmpty())
                        <x-empty-state
                            icon="fa-solid fa-handshake"
                            title="Belum ada pengajuan negosiasi harga"
                            message="Pengajuan diskon di luar wewenang standar dapat diajukan kepada Sales Manager melalui modul negosiasi.">
                            <x-button variant="outline" size="sm" icon="fa-solid fa-tags" :href="route('sales.negotiations.create', ['lead_id' => $lead->id])">
                                Ajukan Negosiasi Sekarang
                            </x-button>
                        </x-empty-state>
                    @else
                        <div class="space-y-3">
                            @foreach ($lead->negotiations as $neg)
                                <div class="bg-[#F7F6F2] rounded-xl border border-[#E8E4DA] p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-[#B89B5E] transition-colors">
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs font-bold text-[#161616]">
                                                Unit {{ $neg->propertyUnit?->unit_number ?? '-' }}
                                                @if ($neg->propertyUnit?->cluster)
                                                    <span class="font-normal text-[#79766F]">({{ $neg->propertyUnit->cluster->name }})</span>
                                                @endif
                                            </span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $neg->approval_status->badgeClass() }}">
                                                {{ $neg->approval_status->label() }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-[#79766F] flex flex-wrap items-center gap-x-4 gap-y-1">
                                            <span>
                                                Harga Awal: <strong class="text-[#161616]">Rp {{ number_format($neg->initial_price, 0, ',', '.') }}</strong>
                                            </span>
                                            <span>
                                                Tawaran: <strong class="text-[#15803D]">Rp {{ number_format($neg->customer_offer_price, 0, ',', '.') }}</strong>
                                            </span>
                                            @if ($neg->discount_amount > 0)
                                                <span>
                                                    Diskon: <strong class="text-[#B89B5E]">Rp {{ number_format($neg->discount_amount, 0, ',', '.') }} ({{ $neg->discount_percentage }}%)</strong>
                                                </span>
                                            @endif
                                        </div>
                                        @if ($neg->promo_description)
                                            <p class="text-xs text-[#79766F]">
                                                Promo: {{ $neg->promo_description }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <x-button variant="outline" size="sm" :href="route('sales.negotiations.show', $neg)">
                                            Lihat Review
                                        </x-button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
