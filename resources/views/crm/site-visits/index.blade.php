<x-layouts.app title="Site Visits & Survei Lokasi Properti">
    <x-page-header
        title="Site Visits & Survei Lokasi"
        subtitle="Jadwal pendampingan konsumen survei show unit, rumah contoh, dan masterplan kawasan.">
        <x-button variant="gold" icon="fa-solid fa-calendar-plus" :href="route('crm.site-visits.create')">
            Jadwalkan Kunjungan Baru
        </x-button>
    </x-page-header>

    <!-- Metrics Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            title="Total Kunjungan"
            :value="$metrics['total']"
            icon="fa-solid fa-calendar-check"
            helper="Seluruh agenda survei lokasi" />

        <x-stat-card
            title="Terjadwal Mendatang"
            :value="$metrics['scheduled']"
            icon="fa-solid fa-clock"
            helper="Menunggu pelaksanaan survei" />

        <x-stat-card
            title="Selesai / Hadir"
            :value="$metrics['completed']"
            icon="fa-solid fa-circle-check"
            helper="Konsumen hadir survei unit" />

        <x-stat-card
            title="Tidak Hadir / Batal"
            :value="$metrics['no_show']"
            icon="fa-solid fa-user-xmark"
            helper="Perlu dijadwalkan ulang" />
    </div>

    <!-- Filter & Search Bar -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('crm.site-visits.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('crm.site-visits.index') }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ empty($currentStatus) ? 'bg-[#161616] text-white' : 'bg-[#F7F6F2] text-[#79766F] hover:bg-[#E8E4DA]' }}">
                    Semua ({{ $metrics['total'] }})
                </a>
                @foreach ($statuses as $st)
                    <a href="{{ route('crm.site-visits.index', array_merge($filters, ['status' => $st->value])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ $currentStatus === $st->value ? 'bg-[#161616] text-white' : 'bg-[#F7F6F2] text-[#79766F] hover:bg-[#E8E4DA]' }}">
                        {{ $st->label() }}
                    </a>
                @endforeach
            </div>

            <div class="w-full sm:w-72 flex gap-2">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari prospek, proyek, sales..."
                       class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                <button type="submit" class="px-3 py-2 bg-[#161616] text-white rounded-lg text-xs hover:bg-[#262626]">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    </x-card>

    <!-- Site Visits Table -->
    <x-card :padding="false">
        @if ($siteVisits->isEmpty())
            <x-empty-state
                icon="fa-solid fa-calendar-xmark"
                title="Belum ada jadwal site visit"
                message="Agenda kunjungan lokasi konsumen akan tampil di sini." />
        @else
            <x-table>
                <thead>
                    <tr>
                        <th class="cell-fit">Waktu & Tanggal</th>
                        <th class="cell-wrap">Prospek Konsumen</th>
                        <th class="cell-wrap">Proyek & Lokasi</th>
                        <th class="cell-fit">Unit Minat</th>
                        <th class="cell-fit">Sales Pendamping</th>
                        <th class="cell-fit">Status</th>
                        <th class="cell-wrap">Hasil / Catatan</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($siteVisits as $visit)
                        <tr>
                            <td class="cell-fit">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA] flex items-center justify-center text-[#B89B5E] shrink-0">
                                        <i class="fa-solid fa-calendar-day text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-[#161616] block text-xs">
                                            {{ $visit->visit_date->translatedFormat('d M Y') }}
                                        </span>
                                        <span class="text-[11px] text-[#79766F] block">
                                            Pukul {{ $visit->visit_date->format('H:i') }} {{ auth()->user()?->getTimezoneCode() ?? 'WIB' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="cell-wrap">
                                @if($visit->lead)
                                    <a href="{{ route('crm.leads.show', $visit->lead) }}" class="font-bold text-[#161616] hover:text-[#B89B5E] block transition-colors">
                                        {{ $visit->lead->name }}
                                    </a>
                                    @if($visit->lead->phone)
                                        <div class="mt-0.5">
                                            <a href="{{ $visit->lead->getWhatsAppUrl("Halo {$visit->lead->salutation} {$visit->lead->name}, saya {$visit->sales->name} dari {$visit->project->name}. Mengonfirmasi jadwal survei lokasi unit properti pada " . $visit->visit_date->translatedFormat('l, d F Y') . " pukul " . $visit->visit_date->format('H:i') . " WIB. Apakah jadwalnya masih sesuai?") }}"
                                               target="_blank"
                                               title="Chat WhatsApp Konfirmasi Jadwal Survei"
                                               class="inline-flex items-center gap-1 text-[11px] text-[#15803D] hover:text-[#166534] font-medium transition-colors">
                                                <i class="fa-brands fa-whatsapp text-xs"></i>
                                                <span>{{ $visit->lead->phone }}</span>
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-[#79766F] block">-</span>
                                    @endif
                                @else
                                    <span class="font-bold text-[#161616] block">Konsumen</span>
                                @endif
                            </td>
                            <td class="cell-wrap">
                                <span class="font-semibold text-[#161616] block">{{ $visit->project->name }}</span>
                                <span class="text-[11px] text-[#79766F] block">
                                    <i class="fa-solid fa-location-dot text-[#B89B5E] mr-1"></i>{{ $visit->project->city }}
                                </span>
                            </td>
                            <td class="cell-fit">
                                @if($visit->propertyUnit)
                                    <span class="font-medium text-[#161616] block">
                                        Unit {{ $visit->propertyUnit->unit_number }}
                                    </span>
                                    <span class="text-[10px] text-[#79766F] block">
                                        {{ $visit->propertyUnit->cluster->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-[#79766F] italic">Show Unit / Umum</span>
                                @endif
                            </td>
                            <td class="cell-fit">
                                <span class="text-xs font-semibold text-[#161616] block">{{ $visit->sales->name }}</span>
                                <span class="text-[10px] text-[#79766F] block">Sales Agent</span>
                            </td>
                            <td class="cell-fit">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $visit->status->badgeClass() }}">
                                    {{ $visit->status->label() }}
                                </span>
                            </td>
                            <td class="cell-wrap">
                                @if($visit->result)
                                    <span class="text-xs font-semibold text-[#161616] block">{{ $visit->result }}</span>
                                @endif
                                @if($visit->notes)
                                    <span class="text-[11px] text-[#79766F] block line-clamp-2">{{ $visit->notes }}</span>
                                @endif
                                @if(!$visit->result && !$visit->notes)
                                    <span class="text-xs text-[#79766F] italic">-</span>
                                @endif
                            </td>
                            <td class="action-col">
                                @if($visit->status === \App\Enums\SiteVisitStatus::SCHEDULED)
                                    <div class="flex items-center gap-1.5">
                                        <!-- Complete action -->
                                        <form action="{{ route('crm.site-visits.update-status', $visit) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="completed">
                                            <input type="hidden" name="result" value="Konsumen hadir survei lokasi & show unit.">
                                            <button type="button"
                                                    data-confirm-action
                                                    data-title="Konfirmasi Kehadiran Konsumen?"
                                                    data-text="Kunjungan akan ditandai selesai/hadir dan skor prospek akan bertambah +20 poin."
                                                    data-confirm-text="Ya, Tandai Hadir"
                                                    class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-[#15803D] hover:bg-[#166534] text-white text-xs font-semibold transition-colors"
                                                    title="Tandai Selesai / Hadir">
                                                <i class="fa-solid fa-check mr-1"></i> Hadir
                                            </button>
                                        </form>

                                        <!-- Cancel action -->
                                        <form action="{{ route('crm.site-visits.update-status', $visit) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="button"
                                                    data-confirm-action
                                                    data-title="Batalkan Jadwal Kunjungan?"
                                                    data-text="Jadwal survei ini akan dibatalkan."
                                                    data-confirm-text="Ya, Batalkan"
                                                    data-is-danger="true"
                                                    class="inline-flex items-center px-2 py-1.5 rounded-lg border border-[#E8E4DA] bg-white text-xs font-medium text-[#79766F] hover:bg-[#FEE2E2] hover:text-[#991B1B] hover:border-[#FCA5A5] transition-colors"
                                                    title="Batalkan Jadwal">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-[#79766F] italic">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>

            @if ($siteVisits->hasPages())
                <div class="prop-table-pagination">
                    {{ $siteVisits->links() }}
                </div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
