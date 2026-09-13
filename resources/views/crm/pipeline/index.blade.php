<x-layouts.app title="Sales Pipeline Kanban">
    <x-page-header
        title="Sales Pipeline Kanban"
        subtitle="Visualisasi alur pergerakan prospek dari kontak perdana hingga closing dan serah terima unit.">
        <x-button variant="gold" icon="fa-solid fa-plus" :href="route('crm.leads.create')">
            Tambah Lead
        </x-button>
        <x-button variant="outline" icon="fa-solid fa-list" :href="route('crm.leads.index')">
            Tampilan Tabel
        </x-button>
    </x-page-header>

    <!-- Horizontal Kanban Board -->
    <div class="flex space-x-3 sm:space-x-4 overflow-x-auto pb-6 select-none snap-x snap-mandatory" style="min-height: calc(100vh - 220px); -webkit-overflow-scrolling: touch;">
        @foreach ($stages as $stage)
            @php
                $stageLeads = $leadsByStage->get($stage->value, collect());
            @endphp
            <div class="w-[82vw] sm:w-80 flex-shrink-0 flex flex-col bg-[#F7F6F2] rounded-xl border border-[#E8E4DA] overflow-hidden snap-start">
                <!-- Column Header -->
                <div class="px-4 py-3 bg-white border-b border-[#E8E4DA] flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $stage->badgeClass() }}"></span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-[#161616]">{{ $stage->label() }}</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#E8E4DA] text-[#161616]">
                        {{ $stageLeads->count() }}
                    </span>
                </div>

                <!-- Cards Container -->
                <div class="flex-1 p-3 space-y-3 overflow-y-auto max-h-[720px]">
                    @forelse ($stageLeads as $lead)
                        <div class="bg-white p-4 rounded-lg border border-[#E8E4DA] shadow-xs hover:border-[#B89B5E] transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[9px] font-mono text-[#79766F]">{{ $lead->code }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $lead->temperature->badgeClass() }}">
                                    {{ $lead->temperature->name }} • {{ $lead->score }}pts
                                </span>
                            </div>

                            <a href="{{ route('crm.leads.show', $lead) }}" class="text-sm font-bold text-[#161616] hover:text-[#B89B5E] transition-colors block mb-1">
                                {{ $lead->name }}
                            </a>

                            <div class="text-[11px] text-[#79766F] mb-3">
                                <div><i class="fa-brands fa-whatsapp text-[#15803D] mr-1"></i> {{ $lead->phone }}</div>
                                @if ($lead->interestedProject)
                                    <div class="mt-0.5"><i class="fa-solid fa-building text-[#B89B5E] mr-1"></i> {{ $lead->interestedProject->name }}</div>
                                @endif
                            </div>

                            <div class="pt-2.5 border-t border-[#E8E4DA] flex items-center justify-between text-[11px]">
                                <span class="font-bold text-[#161616]">
                                    @if ($lead->budget_max > 0)
                                        Rp {{ number_format($lead->budget_max / 1000000) }}jt
                                    @else
                                        -
                                    @endif
                                </span>

                                <div class="flex items-center space-x-1.5 text-[#79766F]" title="Sales PIC: {{ $lead->assignedSales?->name ?? 'Belum Ditugaskan' }}">
                                    <div class="w-5 h-5 rounded-full bg-[#161616] text-[#B89B5E] text-[9px] font-bold flex items-center justify-center">
                                        {{ substr($lead->assignedSales?->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-[10px] font-medium truncate max-w-[80px]">
                                        {{ $lead->assignedSales?->name ?? 'Unassigned' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-[#79766F] border border-dashed border-[#E8E4DA] rounded-lg">
                            Kosong
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-layouts.app>
