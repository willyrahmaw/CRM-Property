@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'PROPFlow' }} — Property CRM & Inventory</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#F7F6F2] text-[#161616] antialiased flex">

    <!-- Flash message data receiver for SweetAlert2 -->
    <div id="flash-messages"
         data-success="{{ session('success') }}"
         data-error="{{ session('error') }}"
         data-warning="{{ session('warning') }}">
    </div>

    <!-- Hidden logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Sidebar (Desktop) -->
    <aside class="w-64 bg-[#161616] text-[#E8E4DA] flex-shrink-0 flex flex-col border-r border-[#262626] select-none">
        <!-- Brand Header -->
        <div class="h-16 px-6 flex items-center justify-between border-b border-[#262626]">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded bg-[#B89B5E] text-white flex items-center justify-center font-bold text-base tracking-wider shadow-xs">
                    P
                </div>
                <div class="flex flex-col">
                    <span class="text-white font-bold text-base tracking-tight leading-tight">PROP<span class="text-[#B89B5E]">Flow</span></span>
                    <span class="text-[9px] uppercase tracking-widest text-[#79766F]">Enterprise CRM</span>
                </div>
            </a>
        </div>

        <!-- Role Badge Strip -->
        <div class="px-5 py-2.5 bg-[#1c1c1c] border-b border-[#262626] flex items-center justify-between">
            <span class="text-[10px] uppercase font-bold text-[#79766F] tracking-wider">Peran Aktif</span>
            @php
                $role = auth()->user()->role;
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
            @endphp
            <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide {{ $roleBadgeClass }}">
                {{ $role->label() }}
            </span>
        </div>

        @php
            $authUser = auth()->user();
            $authRole = $authUser->role;
            $isManager = $authUser->isManagerial();
            $isSales = $authUser->isSales();
            $isFinance = $authUser->isFinance();
            $isAdminProp = $authUser->isAdminProperty();
            $isOwner = $authUser->isCompanyOwner() || $authUser->isSuperAdmin();
        @endphp

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
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
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

            <!-- 2. CRM & Prospek Group (Khusus Tim Sales & Managerial, tersembunyi dari Finance & Admin Property) -->
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
                        <a href="{{ route('crm.leads.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('crm.leads*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-user-group w-5 text-center mr-2 text-sm"></i>
                            <span>{{ $authUser->isSalesAgent() ? 'Prospek Saya (Leads)' : 'Daftar Prospek (Leads)' }}</span>
                        </a>
                        <a href="{{ route('crm.pipeline.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('crm.pipeline*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-layer-group w-5 text-center mr-2 text-sm"></i>
                            <span>{{ $authUser->isSalesAgent() ? 'Pipeline Penjualan Saya' : ($authUser->isTeamLeader() ? 'Sales Pipeline Tim' : 'Sales Pipeline Kanban') }}</span>
                        </a>
                        <a href="{{ route('crm.site-visits.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('crm.site-visits*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-calendar-check w-5 text-center mr-2 text-sm"></i>
                            <span>{{ $authUser->isSalesAgent() ? 'Jadwal Survei Lokasi' : 'Survei Lokasi (Site Visits)' }}</span>
                        </a>
                        <a href="{{ route('crm.customers.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('crm.customers*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-address-book w-5 text-center mr-2 text-sm"></i>
                            <span>{{ $authUser->isSalesAgent() ? 'Data Konsumen Saya' : 'Database Konsumen' }}</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- 3. Property Inventory Group (Disesuaikan untuk Admin Property vs Sales vs Finance vs Managerial) -->
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
                    <a href="{{ route('inventory.projects.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.projects*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                        <i class="fa-solid fa-city w-5 text-center mr-2 text-sm"></i>
                        <span>{{ $isAdminProp ? 'Kelola Proyek Kawasan' : 'Katalog Proyek Kawasan' }}</span>
                    </a>
                    <a href="{{ route('inventory.clusters.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.clusters*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                        <i class="fa-solid fa-sitemap w-5 text-center mr-2 text-sm"></i>
                        <span>{{ $isAdminProp ? 'Kelola Cluster & Tipe' : 'Cluster & Tipe Properti' }}</span>
                    </a>
                    <a href="{{ route('inventory.units.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.units*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
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
                    <a href="{{ route('inventory.siteplan.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('inventory.siteplan*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
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
                    <a href="{{ route('sales.bookings.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('sales.bookings*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
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
                        <a href="{{ route('sales.negotiations.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('sales.negotiations*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-handshake w-5 text-center mr-2 text-sm"></i>
                            <span>{{ $isManager ? 'Approval Negosiasi & Diskon' : 'Pengajuan Diskon & Nego' }}</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- 5. Finance & Commissions Group (Hanya untuk Finance, Managerial, dan Sales) -->
            @if ($isFinance || $isManager)
                <div>
                    <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">{{ 'Keuangan & KPR' }}</p>
                    <div class="space-y-0.5">
                        <a href="{{ route('finance.payments.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('finance.payments*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-receipt w-5 text-center mr-2 text-sm"></i>
                            <span>{{ $isFinance ? 'Verifikasi Pembayaran & Kwitansi' : 'Pembayaran & Kwitansi' }}</span>
                        </a>
                        <a href="{{ route('finance.mortgages.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('finance.mortgages*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-landmark w-5 text-center mr-2 text-sm"></i>
                            <span>{{ $isFinance ? 'KPR Management & Akad' : 'KPR Management' }}</span>
                        </a>
                        <a href="{{ route('finance.commissions.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('finance.commissions*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-coins w-5 text-center mr-2 text-sm"></i>
                            <span>{{ $isFinance ? 'Pencairan Komisi Sales' : 'Approval Komisi Sales' }}</span>
                        </a>
                    </div>
                </div>
            @elseif ($isSales)
                <div>
                    <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">Pendapatan Sales</p>
                    <div class="space-y-0.5">
                        <a href="{{ route('finance.commissions.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('finance.commissions*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-coins w-5 text-center mr-2 text-sm text-[#B89B5E]"></i>
                            <span>Hak Komisi Saya</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- 6. Reporting & Analytics Group (Hanya untuk Managerial & Finance) -->
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
                        <a href="{{ route('reports.sales') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('reports.*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
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
                        <a href="{{ route('settings.users.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('settings.users*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-shield-halved w-5 text-center mr-2 text-sm"></i>
                            <span>{{ 'User & Hak Akses' }}</span>
                        </a>
                        <a href="{{ route('settings.commissions.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('settings.commissions*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-sliders w-5 text-center mr-2 text-sm"></i>
                            <span>Pengaturan Komisi</span>
                        </a>
                        <a href="{{ route('settings.website.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('settings.website*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-globe w-5 text-center mr-2 text-sm"></i>
                            <span>Pengaturan Website</span>
                        </a>
                    </div>
                </div>
            @elseif ($authUser->isSalesManager())
                <div>
                    <p class="px-3 text-[10px] font-semibold tracking-wider text-[#79766F] uppercase mb-1">{{ 'Manajemen Tim' }}</p>
                    <div class="space-y-0.5">
                        <a href="{{ route('settings.users.index') }}" class="flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ request()->routeIs('settings.users*') ? 'bg-[#262626] text-[#B89B5E] border-l-2 border-[#B89B5E]' : 'text-[#E8E4DA] hover:bg-[#262626] hover:text-white' }}">
                            <i class="fa-solid fa-users-gear w-5 text-center mr-2 text-sm"></i>
                            <span>Manajemen Tim Sales</span>
                        </a>
                    </div>
                </div>
            @endif
        </nav>

        <!-- Sidebar User Footer -->
        <div class="p-4 border-t border-[#262626] bg-[#1a1a1a]">
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
                <button type="button" data-confirm-logout class="p-1.5 text-[#79766F] hover:text-white hover:bg-[#262626] rounded transition-colors flex-shrink-0" title="Keluar">
                    <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-[#E8E4DA] flex items-center justify-between px-8 z-10">
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-medium text-[#79766F]">Company:</span>
                    <span class="px-2.5 py-1 rounded bg-[#F7F6F2] border border-[#E8E4DA] text-xs font-semibold text-[#161616]">
                        {{ auth()->user()->company->name ?? 'PROPFlow Demo Realty' }}
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <div class="relative">
                    <button type="button" class="w-9 h-9 rounded-lg border border-[#E8E4DA] flex items-center justify-center text-[#79766F] hover:text-[#161616] hover:bg-[#F7F6F2] transition-colors">
                        <i class="fa-regular fa-bell text-sm"></i>
                    </button>
                </div>
                <div class="h-6 w-px bg-[#E8E4DA]"></div>
                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 group" title="Buka Profil Saya">
                    <div class="text-right">
                        <div class="text-xs font-semibold text-[#161616] group-hover:text-[#B89B5E] transition-colors">{{ auth()->user()->name ?? 'Administrator' }}</div>
                        <div class="text-[10px] text-[#79766F]">{{ auth()->user()->email }}</div>
                    </div>
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border border-[#E8E4DA] group-hover:border-[#B89B5E] transition-colors">
                    @else
                        <div class="w-8 h-8 rounded-full bg-[#161616] text-[#B89B5E] flex items-center justify-center text-xs font-bold border border-[#E8E4DA] group-hover:border-[#B89B5E] transition-colors">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide {{ $roleBadgeClass }}">
                        {{ auth()->user()->role->label() }}
                    </span>
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-8">
            {{ $slot }}
        </main>
    </div>

</body>
</html>
