@php
    $authUser = auth()->user();
    $role = $authUser->role;
    $roleBadgeClass = match($role) {
        \App\Enums\UserRole::SUPER_ADMIN => 'bg-[#B89B5E] text-white',
        \App\Enums\UserRole::COMPANY_OWNER => 'bg-[#D7C49E] text-[#161616]',
        \App\Enums\UserRole::SALES_MANAGER => 'bg-[#262626] text-[#B89B5E] border border-[#B89B5E]/50',
        \App\Enums\UserRole::TEAM_LEADER => 'bg-[#374151] text-white',
        \App\Enums\UserRole::SALES_AGENT => 'bg-[#15803D] text-white',
        \App\Enums\UserRole::FINANCE => 'bg-[#0369A1] text-white',
        \App\Enums\UserRole::ADMIN_PROPERTY => 'bg-[#B45309] text-white',
        default => 'bg-[#262626] text-white',
    };

    $authRole = $authUser->role;
    $isManager = $authUser->isManagerial();
    $isSales = $authUser->isSales();
    $isFinance = $authUser->isFinance();
    $isAdminProp = $authUser->isAdminProperty();
    $isOwner = $authUser->isCompanyOwner() || $authUser->isSuperAdmin();
@endphp

<!-- Role Badge Strip -->
<div class="px-5 py-2.5 bg-[#1c1c1c] border-b border-[#262626] flex items-center justify-between flex-shrink-0">
    <span class="text-[10px] uppercase font-bold text-[#79766F] tracking-wider">Peran Aktif</span>
    <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide {{ $roleBadgeClass }}">
        {{ $role->label() }}
    </span>
</div>

<!-- Navigation Links -->
<nav class="flex-1 px-3 py-4 space-y-5 overflow-y-auto">
    <!-- 1. Overview Group -->
    <div>
        <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">
            @if($isOwner)
                Ikhtisar Eksekutif
            @elseif($authUser->isSalesManager())
                Ikhtisar Sales Manager
            @elseif($authUser->isTeamLeader())
                Ikhtisar Tim Sales
            @elseif($authUser->isSalesAgent())
                Ikhtisar Sales
            @elseif($isFinance)
                Ikhtisar Keuangan
            @elseif($isAdminProp)
                Ikhtisar Inventori
            @else
                Ikhtisar
            @endif
        </p>
        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
            <i class="fa-solid fa-chart-line w-5 text-center mr-2 text-sm text-[#B89B5E]"></i>
            <span>
                @if($isOwner)
                    Dashboard Eksekutif
                @elseif($authUser->isSalesManager())
                    Dashboard Sales Manager
                @elseif($authUser->isTeamLeader())
                    Dashboard Team Leader
                @elseif($authUser->isSalesAgent())
                    Dashboard Sales Saya
                @elseif($isFinance)
                    Dashboard Keuangan
                @elseif($isAdminProp)
                    Dashboard Inventori
                @else
                    Dashboard
                @endif
            </span>
        </a>
    </div>

    <!-- 2. CRM & Prospek Group (Khusus Tim Sales & Managerial) -->
    @if ($isSales || $isManager)
        <div>
            <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">
                @if($authUser->isSalesAgent())
                    {{ 'CRM & Prospek Saya' }}
                @elseif($authUser->isTeamLeader())
                    {{ 'CRM & Pipeline Tim' }}
                @elseif($authUser->isSalesManager())
                    {{ 'CRM & Penjualan Tim' }}
                @else
                    {{ 'CRM & Pipeline Prospek' }}
                @endif
            </p>
            <div class="space-y-0.5">
                <a href="{{ route('crm.leads.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('crm.leads*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-user-group w-5 text-center mr-2 text-sm"></i>
                    <span>{{ $authUser->isSalesAgent() ? 'Prospek Saya (Leads)' : 'Daftar Prospek (Leads)' }}</span>
                </a>
                <a href="{{ route('crm.pipeline.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('crm.pipeline*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-layer-group w-5 text-center mr-2 text-sm"></i>
                    <span>{{ $authUser->isSalesAgent() ? 'Pipeline Penjualan Saya' : ($authUser->isTeamLeader() ? 'Sales Pipeline Tim' : 'Sales Pipeline Kanban') }}</span>
                </a>
                <a href="{{ route('crm.site-visits.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('crm.site-visits*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-calendar-check w-5 text-center mr-2 text-sm"></i>
                    <span>{{ $authUser->isSalesAgent() ? 'Jadwal Survei Lokasi' : 'Survei Lokasi (Site Visits)' }}</span>
                </a>
                <a href="{{ route('crm.customers.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('crm.customers*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-address-book w-5 text-center mr-2 text-sm"></i>
                    <span>{{ $authUser->isSalesAgent() ? 'Data Konsumen Saya' : 'Database Konsumen' }}</span>
                </a>
            </div>
        </div>
    @endif

    <!-- 3. Property Inventory Group -->
    <div>
        <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">
            @if($isAdminProp)
                {{ 'Master Data & Inventori' }}
            @elseif($isSales)
                {{ 'Produk & Ketersediaan' }}
            @elseif($isFinance)
                {{ 'Referensi Properti' }}
            @else
                {{ 'Properti & Inventori' }}
            @endif
        </p>
        <div class="space-y-0.5">
            <a href="{{ route('inventory.projects.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.projects*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                <i class="fa-solid fa-city w-5 text-center mr-2 text-sm"></i>
                <span>{{ $isAdminProp ? 'Kelola Proyek Kawasan' : 'Katalog Proyek Kawasan' }}</span>
            </a>
            <a href="{{ route('inventory.clusters.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.clusters*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                <i class="fa-solid fa-sitemap w-5 text-center mr-2 text-sm"></i>
                <span>{{ $isAdminProp ? 'Kelola Cluster & Tipe' : 'Cluster & Tipe Properti' }}</span>
            </a>
            <a href="{{ route('inventory.units.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.units*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                <i class="fa-solid fa-house-chimney w-5 text-center mr-2 text-sm"></i>
                <span>
                    @if($isAdminProp)
                        {{ 'Kelola Unit & Spesifikasi' }}
                    @elseif($isFinance)
                        {{ 'Daftar Unit & Pricelist' }}
                    @else
                        {{ 'Stok Unit Kavling' }}
                    @endif
                </span>
            </a>
            <a href="{{ route('inventory.siteplan.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.siteplan*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                <i class="fa-solid fa-map-location-dot w-5 text-center mr-2 text-sm"></i>
                <span>{{ $isAdminProp ? 'Master Siteplan Interaktif' : 'Interactive Siteplan' }}</span>
            </a>
        </div>
    </div>

    <!-- 4. Sales Transactions Group -->
    <div>
        <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">
            @if($isFinance)
                Verifikasi Transaksi
            @elseif($isAdminProp)
                Status Penjualan
            @elseif($authUser->isSalesManager())
                Transaksi & Approval
            @else
                Transaksi Penjualan
            @endif
        </p>
        <div class="space-y-0.5">
            <a href="{{ route('sales.bookings.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('sales.bookings*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                <i class="fa-solid fa-file-signature w-5 text-center mr-2 text-sm"></i>
                <span>
                    @if($isFinance)
                        Berkas Pemesanan (SPP)
                    @elseif($isAdminProp)
                        Monitoring Unit Terpesan
                    @else
                        Pemesanan Unit (SPP)
                    @endif
                </span>
            </a>
            @if ($isSales || $isManager)
                <a href="{{ route('sales.negotiations.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('sales.negotiations*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-handshake w-5 text-center mr-2 text-sm"></i>
                    <span>{{ $isManager ? 'Approval Negosiasi & Diskon' : 'Pengajuan Diskon & Nego' }}</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 5. Finance & Commissions Group -->
    @if ($isFinance || $isManager)
        <div>
            <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">{{ 'Keuangan & KPR' }}</p>
            <div class="space-y-0.5">
                <a href="{{ route('finance.payments.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('finance.payments*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-receipt w-5 text-center mr-2 text-sm"></i>
                    <span>{{ $isFinance ? 'Verifikasi Pembayaran & Kwitansi' : 'Pembayaran & Kwitansi' }}</span>
                </a>
                <a href="{{ route('finance.mortgages.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('finance.mortgages*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-landmark w-5 text-center mr-2 text-sm"></i>
                    <span>{{ $isFinance ? 'KPR Management & Akad' : 'KPR Management' }}</span>
                </a>
                <a href="{{ route('finance.commissions.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('finance.commissions*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-coins w-5 text-center mr-2 text-sm"></i>
                    <span>{{ $isFinance ? 'Pencairan Komisi Sales' : 'Approval Komisi Sales' }}</span>
                </a>
            </div>
        </div>
    @elseif ($isSales)
        <div>
            <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">Pendapatan Sales</p>
            <div class="space-y-0.5">
                <a href="{{ route('finance.commissions.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('finance.commissions*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-coins w-5 text-center mr-2 text-sm text-[#B89B5E]"></i>
                    <span>Hak Komisi Saya</span>
                </a>
            </div>
        </div>
    @endif

    <!-- 6. Reporting & Analytics Group -->
    @if ($isManager || $isFinance)
        <div>
            <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">
                @if($isFinance)
                    {{ 'Laporan Keuangan' }}
                @elseif($authUser->isSalesManager())
                    {{ 'Laporan Penjualan' }}
                @else
                    {{ 'Laporan & Analitik' }}
                @endif
            </p>
            <div class="space-y-0.5">
                <a href="{{ route('reports.sales') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('reports.*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center mr-2 text-sm"></i>
                    <span>
                        @if($isFinance)
                            {{ 'Laporan Realisasi & Arus Kas' }}
                        @elseif($authUser->isSalesManager())
                            {{ 'Laporan Kinerja & Omset Tim' }}
                        @else
                            {{ 'Laporan Penjualan Eksekutif' }}
                        @endif
                    </span>
                </a>
            </div>
        </div>
    @endif

    <!-- 7. Administration & Team Group -->
    @if ($isOwner)
        <div>
            <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">{{ 'Konfigurasi Sistem' }}</p>
            <div class="space-y-0.5">
                <a href="{{ route('settings.users.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('settings.users*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-shield-halved w-5 text-center mr-2 text-sm"></i>
                    <span>{{ 'User & Hak Akses' }}</span>
                </a>
                <a href="{{ route('settings.commissions.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('settings.commissions*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-sliders w-5 text-center mr-2 text-sm"></i>
                    <span>Pengaturan Komisi</span>
                </a>
                <a href="{{ route('settings.website.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('settings.website*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-globe w-5 text-center mr-2 text-sm"></i>
                    <span>Pengaturan Website</span>
                </a>
            </div>
        </div>
    @elseif ($authUser->isSalesManager())
        <div>
            <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">{{ 'Manajemen Tim' }}</p>
            <div class="space-y-0.5">
                <a href="{{ route('settings.users.index') }}" class="flex items-center px-3 py-2.5 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('settings.users*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                    <i class="fa-solid fa-users-gear w-5 text-center mr-2 text-sm"></i>
                    <span>Manajemen Tim Sales</span>
                </a>
            </div>
        </div>
    @endif
</nav>

<!-- Sidebar User Footer -->
<div class="p-4 border-t border-[#262626] bg-[#1a1a1a] flex-shrink-0">
    <div class="flex items-center justify-between">
        <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 overflow-hidden group flex-1 mr-2" title="Kelola Profil Saya">
            @if(auth()->user()->avatar_url)
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border border-[#B89B5E] flex-shrink-0">
            @else
                <div class="w-8 h-8 rounded-full bg-[#262626] border border-[#B89B5E] flex items-center justify-center text-xs font-bold text-[#D7C49E] flex-shrink-0 group-hover:bg-[#B89B5E] group-hover:text-white transition-colors">
                    {{ auth()->user()->initials }}
                </div>
            @endif
            <div class="truncate">
                <div class="text-xs font-semibold text-white truncate group-hover:text-[#B89B5E] transition-colors">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="text-[10px] text-[#79766F] truncate">{{ auth()->user()->role->label() ?? 'Agent' }}</div>
            </div>
        </a>
        <button type="button" data-confirm-logout class="p-2 text-[#79766F] hover:text-white hover:bg-[#262626] rounded transition-colors flex-shrink-0" title="Keluar">
            <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
        </button>
    </div>
</div>
