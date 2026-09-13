<x-layouts.app title="Daftar Leads">
    <x-page-header
        title="Manajemen Leads & Prospek"
        subtitle="Kelola seluruh prospek masuk dari omnichannel, tracking suhu minat, dan pembagian sales.">
        <x-button variant="gold" icon="fa-solid fa-plus" :href="route('crm.leads.create')">
            Tambah Lead
        </x-button>
        <x-button variant="secondary" icon="fa-solid fa-layer-group" :href="route('crm.pipeline.index')">
            Kanban Pipeline
        </x-button>
    </x-page-header>

    <!-- Filters Bar -->
    <x-card class="mb-6">
        <form action="{{ route('crm.leads.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-[#79766F] uppercase mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Nama, kode, telepon..."
                       class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] text-[#161616] focus:bg-white focus:outline-none focus:border-[#B89B5E]">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-[#79766F] uppercase mb-1">Tahapan Status</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] text-[#161616] focus:bg-white focus:outline-none focus:border-[#B89B5E]">
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" {{ ($filters['status'] ?? '') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-[#79766F] uppercase mb-1">Suhu Minat</label>
                <select name="temperature" class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] text-[#161616] focus:bg-white focus:outline-none focus:border-[#B89B5E]">
                    <option value="">Semua Suhu</option>
                    @foreach ($temperatures as $tp)
                        <option value="{{ $tp->value }}" {{ ($filters['temperature'] ?? '') === $tp->value ? 'selected' : '' }}>
                            {{ $tp->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-[#79766F] uppercase mb-1">Sales PIC</label>
                <select name="sales_id" class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] text-[#161616] focus:bg-white focus:outline-none focus:border-[#B89B5E]">
                    <option value="">Semua Sales</option>
                    @foreach ($salesAgents as $agent)
                        <option value="{{ $agent->id }}" {{ ($filters['sales_id'] ?? '') === $agent->id ? 'selected' : '' }}>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <x-button type="submit" variant="primary" size="md" class="w-full">
                    Filter
                </x-button>
                <a href="{{ route('crm.leads.index') }}" class="px-3 py-2 text-xs font-medium rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] hover:bg-[#E8E4DA] text-[#161616] transition-colors" title="Reset Filter">
                    <i class="fa-solid fa-arrow-rotate-right"></i>
                </a>
            </div>
        </form>
    </x-card>

    <!-- Leads Table -->
    <x-card :padding="false">
        @if ($leads->isEmpty())
            <x-empty-state
                icon="fa-solid fa-user-plus"
                title="Belum ada leads yang cocok"
                message="Lead baru akan muncul di sini setelah ditambahkan manual atau diintegrasikan melalui marketing channel."
                actionText="Tambah Lead Baru"
                :actionUrl="route('crm.leads.create')" />
        @else
            <x-table>
                <thead>
                    <tr>
                        <th>Kode & Nama</th>
                        <th>Kontak</th>
                        <th>Sumber</th>
                        <th>Suhu & Skor</th>
                        <th>Rentang Budget</th>
                        <th>Sales PIC</th>
                        <th>Status</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leads as $lead)
                        <tr>
                            <td>
                                <span class="text-[10px] font-mono text-[#79766F] block">{{ $lead->code }}</span>
                                <a href="{{ route('crm.leads.show', $lead) }}" class="font-semibold text-[#161616] hover:text-[#B89B5E] transition-colors">
                                    {{ $lead->name }}
                                </a>
                            </td>
                            <td>
                                @if ($lead->phone)
                                    <a href="{{ $lead->getWhatsAppUrl() }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 font-semibold text-[#15803D] hover:text-[#166534] bg-[#DCFCE7] hover:bg-[#BBF7D0] border border-[#BBF7D0] px-2 py-0.5 rounded text-xs transition-colors shadow-2xs group"
                                       title="1-Click WhatsApp: Langsung kirim pesan tanpa simpan nomor">
                                        <i class="fa-brands fa-whatsapp text-sm group-hover:scale-110 transition-transform"></i>
                                        <span>{{ $lead->phone }}</span>
                                    </a>
                                @else
                                    <span class="text-[#79766F] text-xs">-</span>
                                @endif
                                @if ($lead->email)
                                    <div class="text-[#79766F] text-[11px] mt-0.5">{{ $lead->email }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="px-2.5 py-1 rounded bg-[#F7F6F2] border border-[#E8E4DA] text-[#161616] text-[10px] font-medium">
                                    {{ $lead->source->label() }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-1.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $lead->temperature->badgeClass() }}">
                                        {{ $lead->temperature->name }}
                                    </span>
                                    <span class="font-bold text-[#161616] text-xs">
                                        {{ $lead->score }} pts
                                    </span>
                                </div>
                            </td>
                            <td class="font-semibold text-[#161616]">
                                @if ($lead->budget_min > 0 || $lead->budget_max > 0)
                                    Rp {{ number_format($lead->budget_min / 1000000) }}jt - {{ number_format($lead->budget_max / 1000000) }}jt
                                @else
                                    <span class="text-[#79766F] font-normal">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($lead->assignedSales)
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-[#161616] text-[#B89B5E] text-[10px] font-bold flex items-center justify-center">
                                            {{ substr($lead->assignedSales->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-[#161616]">{{ $lead->assignedSales->name }}</span>
                                    </div>
                                @else
                                    <span class="text-[#991B1B] font-semibold text-[11px]">Belum Ditugaskan</span>
                                @endif
                            </td>
                            <td>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $lead->status->badgeClass() }}">
                                    {{ $lead->status->label() }}
                                </span>
                            </td>
                            <td class="action-col">
                                <a href="{{ route('crm.leads.show', $lead) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border border-[#E8E4DA] bg-white hover:bg-[#F7F6F2] hover:border-[#161616] text-[#161616] font-semibold text-xs transition-colors">
                                    Detail <i class="fa-solid fa-chevron-right ml-1.5 text-[10px] text-[#79766F]"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>

            @if ($leads->hasPages())
                <div class="prop-table-pagination">
                    {{ $leads->links() }}
                </div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
