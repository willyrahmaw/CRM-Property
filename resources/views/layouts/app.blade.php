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

    <!-- Mobile Off-Canvas Backdrop -->
    <div id="mobile-sidebar-backdrop" class="fixed inset-0 bg-black/70 z-40 backdrop-blur-xs transition-opacity duration-300 ease-in-out opacity-0 hidden lg:hidden"></div>

    <!-- Mobile Off-Canvas Drawer -->
    <div id="mobile-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-[#161616] text-[#E8E4DA] flex flex-col border-r border-[#262626] select-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden shadow-2xl">
        <!-- Mobile Drawer Header -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-[#262626] flex-shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded bg-[#B89B5E] text-white flex items-center justify-center font-bold text-base tracking-wider shadow-xs">
                    P
                </div>
                <div class="flex flex-col">
                    <span class="text-white font-bold text-base tracking-tight leading-tight">PROP<span class="text-[#B89B5E]">Flow</span></span>
                    <span class="text-[9px] uppercase tracking-widest text-[#79766F]">Enterprise CRM</span>
                </div>
            </a>
            <button type="button" data-mobile-menu-close class="w-9 h-9 rounded-lg flex items-center justify-center text-[#79766F] hover:text-white hover:bg-[#262626] transition-colors" aria-label="Tutup Menu">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <x-layouts.sidebar-content />
    </div>

    <!-- Sidebar (Desktop) -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-[#161616] text-[#E8E4DA] flex-shrink-0 border-r border-[#262626] select-none">
        <!-- Brand Header -->
        <div class="h-16 px-6 flex items-center justify-between border-b border-[#262626] flex-shrink-0">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded bg-[#B89B5E] text-white flex items-center justify-center font-bold text-base tracking-wider">
                    P
                </div>
                <div class="flex flex-col">
                    <span class="text-white font-bold text-base tracking-tight leading-tight">PROP<span class="text-[#B89B5E]">Flow</span></span>
                    <span class="text-[9px] uppercase tracking-widest text-[#79766F]">Enterprise CRM</span>
                </div>
            </a>
        </div>

        <x-layouts.sidebar-content />
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-[#E8E4DA] flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 flex-shrink-0">
            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Hamburger Menu Button (Mobile) -->
                <button type="button" data-mobile-menu-open class="lg:hidden w-9 h-9 rounded-lg border border-[#E8E4DA] flex items-center justify-center text-[#161616] hover:bg-[#F7F6F2] transition-colors" aria-label="Buka Menu Navigasi">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>

                <!-- Mobile Brand Header (Mobile Only) -->
                <a href="{{ route('dashboard') }}" class="flex lg:hidden items-center space-x-2">
                    <div class="w-7 h-7 rounded bg-[#B89B5E] text-white flex items-center justify-center font-bold text-xs">
                        P
                    </div>
                    <span class="font-bold text-sm text-[#161616] tracking-tight">PROP<span class="text-[#B89B5E]">Flow</span></span>
                </a>

                <!-- Company Badge -->
                <div class="hidden sm:flex items-center space-x-2">
                    <span class="text-xs font-medium text-[#79766F]">Company:</span>
                    <span class="px-2.5 py-1 rounded bg-[#F7F6F2] border border-[#E8E4DA] text-xs font-semibold text-[#161616] truncate max-w-[200px]">
                        {{ auth()->user()->company->name ?? 'PROPFlow Demo Realty' }}
                    </span>
                </div>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-3">
                <!-- Timezone Quick Switcher / PT Indicator -->
                @php
                    $activeTz = auth()->user()?->getTimezoneEnum() ?? \App\Enums\IndonesianTimezone::WIB;
                    $canChangeTz = auth()->user()?->isCompanyOwner() || auth()->user()?->isSuperAdmin();
                @endphp
                <div class="relative" data-timezone-dropdown>
                    @if($canChangeTz)
                        <button type="button"
                                data-timezone-toggle
                                class="flex items-center space-x-1.5 px-2.5 py-1.5 rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] hover:bg-white hover:border-[#B89B5E] text-xs font-semibold text-[#161616] transition-colors shadow-2xs"
                                title="Ganti Zona Waktu PT (Berlaku untuk seluruh akun & transaksi PT)">
                            <i class="fa-regular fa-clock text-[#B89B5E] text-xs"></i>
                            <span class="font-bold text-[11px]">{{ $activeTz->code() }}</span>
                            <span class="text-[10px] text-[#79766F] font-normal hidden lg:inline">({{ $activeTz->utcOffset() }})</span>
                            <i class="fa-solid fa-chevron-down text-[9px] text-[#79766F]"></i>
                        </button>

                        <!-- Dropdown Menu for Company Owner -->
                        <div data-timezone-menu
                             class="hidden absolute right-0 mt-2 w-72 bg-white border border-[#E8E4DA] rounded-xl shadow-lg py-1.5 z-50">
                            <div class="px-3 py-1.5 border-b border-[#E8E4DA] mb-1">
                                <p class="text-[10px] font-bold text-[#161616] uppercase tracking-wider">Zona Waktu PT Pengembang</p>
                                <p class="text-[10px] text-[#79766F]">Perubahan berlaku untuk seluruh akun PT ini</p>
                            </div>
                            @foreach(\App\Enums\IndonesianTimezone::cases() as $tzOption)
                                <form action="{{ route('timezone.switch') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="timezone" value="{{ $tzOption->value }}">
                                    <button type="submit"
                                            class="w-full text-left px-3 py-2 text-xs flex items-center justify-between hover:bg-[#F7F6F2] transition-colors {{ $activeTz === $tzOption ? 'bg-[#F7F6F2] text-[#B89B5E] font-bold' : 'text-[#161616]' }}">
                                        <div class="min-w-0 pr-2">
                                            <div class="font-semibold flex items-center gap-1.5">
                                                <span>{{ $tzOption->code() }}</span>
                                                <span class="text-[10px] text-[#79766F] font-normal">({{ $tzOption->utcOffset() }})</span>
                                            </div>
                                            <div class="text-[10px] text-[#79766F] font-normal truncate">{{ $tzOption->regions() }}</div>
                                        </div>
                                        @if($activeTz === $tzOption)
                                            <i class="fa-solid fa-check text-xs text-[#B89B5E] flex-shrink-0"></i>
                                        @endif
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center space-x-1.5 px-2.5 py-1.5 rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] text-xs font-semibold text-[#161616] shadow-2xs"
                             title="Zona Waktu Operasional PT: {{ $activeTz->label() }} (Seluruh akun PT terikat di zona waktu ini)">
                            <i class="fa-regular fa-clock text-[#B89B5E] text-xs"></i>
                            <span class="font-bold text-[11px]">{{ $activeTz->code() }}</span>
                            <span class="text-[10px] text-[#79766F] font-normal hidden lg:inline">({{ $activeTz->utcOffset() }})</span>
                        </div>
                    @endif
                </div>

                <button type="button" class="w-9 h-9 rounded-lg border border-[#E8E4DA] flex items-center justify-center text-[#79766F] hover:text-[#161616] hover:bg-[#F7F6F2] transition-colors" title="Notifikasi">
                    <i class="fa-regular fa-bell text-sm"></i>
                </button>
                <div class="h-6 w-px bg-[#E8E4DA]"></div>
                <a href="{{ route('profile.edit') }}" class="flex items-center space-x-2 sm:space-x-3 group" title="Buka Profil Saya">
                    <div class="text-right hidden md:block">
                        <div class="text-xs font-semibold text-[#161616] group-hover:text-[#B89B5E] transition-colors truncate max-w-[150px]">{{ auth()->user()->name ?? 'Administrator' }}</div>
                        <div class="text-[10px] text-[#79766F] truncate max-w-[150px]">{{ auth()->user()->email }}</div>
                    </div>
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover border border-[#E8E4DA] group-hover:border-[#B89B5E] transition-colors">
                    @else
                        <div class="w-8 h-8 rounded-full bg-[#161616] text-[#B89B5E] flex items-center justify-center text-xs font-bold border border-[#E8E4DA] group-hover:border-[#B89B5E] transition-colors">
                            {{ auth()->user()->initials }}
                        </div>
                    @endif
                    <span class="hidden sm:inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wide {{ match(auth()->user()->role) {
                        \App\Enums\UserRole::SUPER_ADMIN => 'bg-[#B89B5E] text-white',
                        \App\Enums\UserRole::COMPANY_OWNER => 'bg-[#D7C49E] text-[#161616]',
                        \App\Enums\UserRole::SALES_MANAGER => 'bg-[#262626] text-[#B89B5E] border border-[#B89B5E]/50',
                        \App\Enums\UserRole::TEAM_LEADER => 'bg-[#374151] text-white',
                        \App\Enums\UserRole::SALES_AGENT => 'bg-[#15803D] text-white',
                        \App\Enums\UserRole::FINANCE => 'bg-[#0369A1] text-white',
                        \App\Enums\UserRole::ADMIN_PROPERTY => 'bg-[#B45309] text-white',
                        default => 'bg-[#262626] text-white',
                    } }}">
                        {{ auth()->user()->role->label() }}
                    </span>
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>
    </div>

</body>
</html>
