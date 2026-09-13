<x-layouts.app :title="$isSalesManager ? 'Manajemen Tim Sales' : 'Manajemen Pengguna & Hak Akses'">
    <x-page-header
        :title="$isSalesManager ? 'Manajemen Tim Sales' : 'Pengguna & Struktur Peran'"
        :subtitle="$isSalesManager ? 'Kelola anggota tim penjualan, koordinator / Team Leader, beban prospek (lead), dan keaktifan agen sales.' : 'Kelola akun tim developer, wewenang jabatan, status keaktifan, dan pembagian tugas operasional CRM.'">
    </x-page-header>

    @if ($isSalesManager)
        <!-- Sales Team Metrics Overview for Sales Manager -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
            <div class="bg-white p-3.5 rounded-xl border border-[#E8E4DA] shadow-xs">
                <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Total Tim Sales</span>
                <div class="text-xl font-extrabold text-[#161616]">{{ $metrics['total'] }}</div>
                <span class="text-[10px] text-[#79766F] mt-0.5 block">Seluruh pemasar di bawah Anda</span>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-[#E8E4DA] shadow-xs">
                <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Team Leader</span>
                <div class="text-xl font-extrabold text-[#B89B5E]">{{ $metrics['team_leaders'] }}</div>
                <span class="text-[10px] text-[#79766F] mt-0.5 block">Koordinator lapangan</span>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-[#E8E4DA] shadow-xs">
                <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Sales / Agent</span>
                <div class="text-xl font-extrabold text-[#161616]">{{ $metrics['sales_agents'] }}</div>
                <span class="text-[10px] text-[#79766F] mt-0.5 block">PIC penanganan prospek</span>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-[#E8E4DA] shadow-xs">
                <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Tim Aktif</span>
                <div class="text-xl font-extrabold text-[#15803D]">{{ $metrics['active_sales'] }}</div>
                <span class="text-[10px] text-[#79766F] mt-0.5 block">Siap terima alokasi lead</span>
            </div>
        </div>
    @else
        <!-- Role Metrics Overview for Owner / Super Admin -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
            <div class="bg-white p-3.5 rounded-xl border border-[#E8E4DA] shadow-xs">
                <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Total Pengguna</span>
                <div class="text-xl font-extrabold text-[#161616]">{{ $metrics['total'] }}</div>
                <span class="text-[10px] text-[#79766F] mt-0.5 block">Seluruh akun terdaftar</span>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-[#E8E4DA] shadow-xs">
                <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Sales & Marketing</span>
                <div class="text-xl font-extrabold text-[#B89B5E]">{{ $metrics['sales'] }}</div>
                <span class="text-[10px] text-[#79766F] mt-0.5 block">Agen & Team Leader</span>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-[#E8E4DA] shadow-xs">
                <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Manajerial</span>
                <div class="text-xl font-extrabold text-[#161616]">{{ $metrics['managerial'] }}</div>
                <span class="text-[10px] text-[#79766F] mt-0.5 block">Owner & Sales Manager</span>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-[#E8E4DA] shadow-xs">
                <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Finance & Kasir</span>
                <div class="text-xl font-extrabold text-[#15803D]">{{ $metrics['finance'] }}</div>
                <span class="text-[10px] text-[#79766F] mt-0.5 block">Verifikasi Pembayaran</span>
            </div>

            <div class="bg-white p-3.5 rounded-xl border border-[#E8E4DA] shadow-xs col-span-2 sm:col-span-1">
                <span class="block text-[10px] uppercase font-bold text-[#79766F] tracking-wider mb-1">Property Admin</span>
                <div class="text-xl font-extrabold text-[#0284C7]">{{ $metrics['property_admin'] }}</div>
                <span class="text-[10px] text-[#79766F] mt-0.5 block">Stok & Masterplan</span>
            </div>
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('settings.users.index') }}" class="flex flex-col sm:flex-row gap-3 items-center justify-between">
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('settings.users.index') }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ empty($filters['role']) ? 'bg-[#161616] text-white' : 'bg-[#F7F6F2] text-[#79766F] hover:bg-[#E8E4DA]' }}">
                    Semua ({{ $metrics['total'] }})
                </a>
                @foreach ($roles as $role)
                    <a href="{{ route('settings.users.index', array_merge($filters, ['role' => $role->value])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors {{ ($filters['role'] ?? '') === $role->value ? 'bg-[#161616] text-white' : 'bg-[#F7F6F2] text-[#79766F] hover:bg-[#E8E4DA]' }}">
                        {{ $role->label() }}
                    </a>
                @endforeach
            </div>

            <div class="w-full sm:w-72 flex gap-2">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama, email, telepon..."
                       class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                <button type="submit" class="px-3 py-2 bg-[#161616] text-white rounded-lg text-xs hover:bg-[#262626]">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </form>
    </x-card>

    <!-- Users Table -->
    <x-card :title="$isSalesManager ? 'Daftar Anggota Tim Sales' : 'Daftar Pengguna & Otoritas Sistem'">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="text-[#79766F] uppercase border-b border-[#E8E4DA] bg-[#F7F6F2]">
                    <tr>
                        <th class="py-2.5 px-3">Pengguna</th>
                        <th class="py-2.5 px-3">Peran & Wewenang</th>
                        <th class="py-2.5 px-3">Kontak</th>
                        <th class="py-2.5 px-3 text-center">Beban Lead</th>
                        <th class="py-2.5 px-3 text-center">Status Akun</th>
                        <th class="py-2.5 px-3 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E8E4DA]">
                    @forelse ($users as $u)
                        <tr class="hover:bg-[#F7F6F2]/50">
                            <td class="py-3 px-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-[#161616] text-[#B89B5E] text-xs font-bold flex items-center justify-center flex-shrink-0 border border-[#B89B5E]/40">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-[#161616] block">{{ $u->name }}</span>
                                        <span class="text-[10px] text-[#79766F]">{{ $u->company?->name ?? 'PROPFlow System' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3 px-3">
                                @php
                                    $roleBadge = match($u->role) {
                                        \App\Enums\UserRole::SUPER_ADMIN => 'bg-[#161616] text-[#B89B5E] border border-[#B89B5E]',
                                        \App\Enums\UserRole::COMPANY_OWNER => 'bg-[#161616] text-white border border-[#262626]',
                                        \App\Enums\UserRole::SALES_MANAGER => 'bg-[#262626] text-white',
                                        \App\Enums\UserRole::TEAM_LEADER => 'bg-[#D7C49E] text-[#161616]',
                                        \App\Enums\UserRole::SALES_AGENT => 'bg-[#F7F6F2] text-[#161616] border border-[#E8E4DA]',
                                        \App\Enums\UserRole::FINANCE => 'bg-[#DCFCE7] text-[#15803D] border border-[#BBF7D0]',
                                        \App\Enums\UserRole::ADMIN_PROPERTY => 'bg-[#E0F2FE] text-[#0369A1] border border-[#BAE6FD]',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $roleBadge }}">
                                    {{ $u->role->label() }}
                                </span>
                            </td>

                            <td class="py-3 px-3 text-[#79766F]">
                                <div><i class="fa-regular fa-envelope text-[10px] mr-1 text-[#B89B5E]"></i> {{ $u->email }}</div>
                                @if ($u->phone)
                                    <div class="mt-0.5"><i class="fa-solid fa-phone text-[10px] mr-1 text-[#79766F]"></i> {{ $u->phone }}</div>
                                @endif
                            </td>

                            <td class="py-3 px-3 text-center">
                                @if ($u->isSales())
                                    <span class="font-bold text-[#161616]">{{ $u->assigned_leads_count }} Lead</span>
                                @else
                                    <span class="text-[#79766F]">-</span>
                                @endif
                            </td>

                            <td class="py-3 px-3 text-center">
                                @if ($u->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-[#DCFCE7] text-[#15803D]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#15803D] mr-1.5"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-[#FEE2E2] text-[#991B1B]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#991B1B] mr-1.5"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-3 text-right">
                                @if (auth()->user()->canManageUser($u))
                                    <form action="{{ route('settings.users.toggle-status', $u) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                data-confirm-action
                                                data-title="{{ $u->is_active ? 'Nonaktifkan Akun?' : 'Aktifkan Akun?' }}"
                                                data-text="{{ $u->is_active ? 'Pengguna ' . $u->name . ' tidak akan dapat login ke dalam sistem CRM.' : 'Pengguna ' . $u->name . ' akan dapat kembali login dan menerima penugasan prospek.' }}"
                                                data-confirm-text="{{ $u->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}"
                                                data-is-danger="{{ $u->is_active ? 'true' : 'false' }}"
                                                class="px-2.5 py-1 rounded text-[10px] font-semibold transition-colors {{ $u->is_active ? 'bg-[#F7F6F2] text-[#991B1B] hover:bg-[#FEE2E2]' : 'bg-[#DCFCE7] text-[#15803D] hover:bg-[#BBF7D0]' }}"
                                                title="{{ $u->is_active ? 'Nonaktifkan Akses' : 'Aktifkan Akun' }}">
                                            {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                @elseif ($u->id === auth()->id())
                                    <span class="text-[10px] font-semibold text-[#79766F] italic">Akun Anda</span>
                                @else
                                    <span class="text-[10px] font-semibold text-[#79766F] italic">Dilindungi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-[#79766F]">
                                Tidak ada data pengguna yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </x-card>
</x-layouts.app>
