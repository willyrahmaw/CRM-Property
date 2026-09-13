<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Terjadi Kendala') — PROPFlow Enterprise CRM</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#F7F6F2] text-[#161616] antialiased flex flex-col justify-between p-4 sm:p-6 lg:p-8">

    <!-- Top Brand Header -->
    <header class="w-full max-w-4xl mx-auto flex items-center justify-between py-2">
        <a href="{{ url('/') }}" class="flex items-center space-x-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#161616] text-[#B89B5E] flex items-center justify-center font-bold text-base tracking-wider border border-[#262626] shadow-xs">
                P
            </div>
            <div class="flex flex-col">
                <span class="text-[#161616] font-bold text-base tracking-tight leading-tight">PROP<span class="text-[#B89B5E]">Flow</span></span>
                <span class="text-[9px] uppercase tracking-widest text-[#79766F]">Enterprise CRM</span>
            </div>
        </a>

        @auth
            <div class="flex items-center space-x-2 text-xs">
                <span class="text-[#79766F] hidden sm:inline">Masuk sebagai:</span>
                <span class="px-2 py-0.5 rounded-full bg-white border border-[#E8E4DA] font-semibold text-[#161616]">
                    {{ auth()->user()->name }} ({{ auth()->user()->role->label() }})
                </span>
            </div>
        @else
            <a href="{{ route('login') }}" class="text-xs font-semibold text-[#B89B5E] hover:underline">
                Masuk ke Akun
            </a>
        @endauth
    </header>

    <!-- Centered Error Card -->
    <main class="w-full max-w-xl mx-auto my-auto">
        <div class="bg-white rounded-2xl border border-[#E8E4DA] p-6 sm:p-10 text-center shadow-xs">
            <!-- Icon & Code Pill -->
            <div class="mb-5 flex flex-col items-center">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-[#F7F6F2] border border-[#E8E4DA] flex items-center justify-center mb-3">
                    @yield('icon')
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold tracking-wider uppercase bg-[#161616] text-white">
                    Status @yield('code')
                </span>
            </div>

            <!-- Error Title & Description -->
            <h1 class="text-xl sm:text-2xl font-bold text-[#161616] tracking-tight mb-2.5">
                @yield('headline')
            </h1>

            <p class="text-xs sm:text-sm text-[#79766F] leading-relaxed max-w-md mx-auto mb-6">
                @yield('message')
            </p>

            @hasSection('detail')
                <div class="bg-[#F7F6F2] border border-[#E8E4DA] rounded-xl p-3.5 text-xs text-[#161616] text-left mb-6 font-mono break-words leading-relaxed">
                    @yield('detail')
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-3 pt-2 border-t border-[#E8E4DA]">
                <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href = '{{ url('/') }}'"
                        class="px-4 py-2.5 rounded-lg border border-[#E8E4DA] bg-white text-xs font-semibold text-[#161616] hover:bg-[#F7F6F2] transition-colors">
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Halaman Sebelumnya
                </button>

                <a href="{{ auth()->check() ? route('dashboard') : route('login') }}"
                   class="px-5 py-2.5 rounded-lg bg-[#161616] text-xs font-semibold text-white hover:bg-[#262626] transition-colors shadow-xs">
                    <i class="fa-solid fa-house mr-1.5 text-[#B89B5E]"></i>
                    {{ auth()->check() ? 'Buka Dashboard CRM' : 'Halaman Masuk' }}
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-4xl mx-auto text-center text-[11px] text-[#79766F] py-2">
        <p>PROPFlow Property Sales CRM & Inventory Management &copy; {{ date('Y') }}. Hak Cipta Dilindungi.</p>
    </footer>

</body>
</html>
