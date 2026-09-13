<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — PROPFlow Enterprise CRM</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#F7F6F2] text-[#161616] antialiased flex flex-col justify-center py-10 sm:px-6 lg:px-8">

    <!-- Flash message data receiver for SweetAlert2 -->
    <div id="flash-messages"
         data-success="{{ session('success') }}"
         data-error="{{ session('error') }}"
         data-warning="{{ session('warning') }}">
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-lg text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-[#161616] text-[#B89B5E] text-2xl font-bold mb-3 shadow-xs border border-[#262626]">
            P
        </div>
        <h2 class="text-2xl font-bold tracking-tight text-[#161616]">
            PROP<span class="text-[#B89B5E]">Flow</span>
        </h2>
        <p class="mt-1 text-xs uppercase tracking-widest text-[#79766F]">
            Property Sales CRM & Inventory Management
        </p>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-lg">
        <div class="bg-white py-8 px-6 sm:px-8 border border-[#E8E4DA] rounded-xl shadow-xs">
            <form id="login-form" action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-[#161616] uppercase tracking-wider mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                            <i class="fa-regular fa-envelope text-sm"></i>
                        </div>
                        <input id="email"
                               name="email"
                               type="email"
                               autocomplete="email"
                               required
                               value="{{ old('email', 'owner@propflow.local') }}"
                               class="block w-full pl-9 pr-3 py-2.5 text-sm bg-white border border-[#E8E4DA] rounded-lg text-[#161616] placeholder-[#79766F] focus:outline-none focus:ring-1 focus:ring-[#B89B5E] focus:border-[#B89B5E] transition-colors"
                               placeholder="email@developer.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-[#991B1B]">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-[#161616] uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input id="password"
                               name="password"
                               type="password"
                               autocomplete="current-password"
                               required
                               value="CrmProperty123!"
                               class="block w-full pl-9 pr-3 py-2.5 text-sm bg-white border border-[#E8E4DA] rounded-lg text-[#161616] placeholder-[#79766F] focus:outline-none focus:ring-1 focus:ring-[#B89B5E] focus:border-[#B89B5E] transition-colors"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-[#991B1B]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember"
                               name="remember"
                               type="checkbox"
                               class="h-4 w-4 rounded border-[#E8E4DA] text-[#B89B5E] focus:ring-[#B89B5E]">
                        <label for="remember" class="ml-2 block text-xs text-[#79766F]">
                            Ingat saya di perangkat ini
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-[#161616] hover:bg-[#262626] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B89B5E] transition-colors shadow-xs">
                        Masuk ke Sistem
                    </button>
                </div>
            </form>

            <!-- Quick Login Demo Section -->
            <div class="mt-6 pt-6 border-t border-[#E8E4DA]">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#79766F]">
                        <i class="fa-solid fa-bolt text-[#B89B5E] mr-1"></i> Quick Demo Login
                    </span>
                    <span class="text-[10px] text-[#79766F]">1-Klik Akses Peran</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <!-- Owner -->
                    <button type="button"
                            data-quick-login
                            data-email="owner@propflow.local"
                            data-password="CrmProperty123!"
                            data-auto-submit="true"
                            class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] hover:bg-white hover:border-[#B89B5E] text-left transition-all group">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#161616] text-[#B89B5E] flex items-center justify-center text-xs font-bold flex-shrink-0">
                                <i class="fa-solid fa-crown"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-[#161616] group-hover:text-[#B89B5E] transition-colors truncate">Owner</span>
                                <span class="block text-[10px] text-[#79766F] truncate">Bambang Wijaya</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right text-[11px] text-[#79766F] group-hover:text-[#161616] transition-transform group-hover:translate-x-0.5"></i>
                    </button>

                    <!-- Sales Manager -->
                    <button type="button"
                            data-quick-login
                            data-email="manager@propflow.local"
                            data-password="CrmProperty123!"
                            data-auto-submit="true"
                            class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] hover:bg-white hover:border-[#B89B5E] text-left transition-all group">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#262626] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-[#161616] group-hover:text-[#B89B5E] transition-colors truncate">Sales Manager</span>
                                <span class="block text-[10px] text-[#79766F] truncate">Hendrik Pratama</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right text-[11px] text-[#79766F] group-hover:text-[#161616] transition-transform group-hover:translate-x-0.5"></i>
                    </button>

                    <!-- Finance -->
                    <button type="button"
                            data-quick-login
                            data-email="finance@propflow.local"
                            data-password="CrmProperty123!"
                            data-auto-submit="true"
                            class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] hover:bg-white hover:border-[#B89B5E] text-left transition-all group">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#15803D] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-[#161616] group-hover:text-[#B89B5E] transition-colors truncate">Finance Admin</span>
                                <span class="block text-[10px] text-[#79766F] truncate">Siti Rahmawati</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right text-[11px] text-[#79766F] group-hover:text-[#161616] transition-transform group-hover:translate-x-0.5"></i>
                    </button>

                    <!-- Property Admin -->
                    <button type="button"
                            data-quick-login
                            data-email="admin@propflow.local"
                            data-password="CrmProperty123!"
                            data-auto-submit="true"
                            class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] hover:bg-white hover:border-[#B89B5E] text-left transition-all group">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#0284C7] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                <i class="fa-solid fa-city"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-[#161616] group-hover:text-[#B89B5E] transition-colors truncate">Property Admin</span>
                                <span class="block text-[10px] text-[#79766F] truncate">Dedi Irawan</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right text-[11px] text-[#79766F] group-hover:text-[#161616] transition-transform group-hover:translate-x-0.5"></i>
                    </button>

                    <!-- Sales Agent -->
                    <button type="button"
                            data-quick-login
                            data-email="andi@propflow.local"
                            data-password="CrmProperty123!"
                            data-auto-submit="true"
                            class="flex items-center justify-between p-2.5 rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] hover:bg-white hover:border-[#B89B5E] text-left transition-all group sm:col-span-2">
                        <div class="flex items-center space-x-2.5">
                            <div class="w-8 h-8 rounded-lg bg-[#B89B5E] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                <i class="fa-solid fa-user-tag"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-bold text-[#161616] group-hover:text-[#B89B5E] transition-colors truncate">Sales Agent</span>
                                <span class="block text-[10px] text-[#79766F] truncate">Andi Setiawan (andi@propflow.local)</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-arrow-right text-[11px] text-[#79766F] group-hover:text-[#161616] transition-transform group-hover:translate-x-0.5"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
