<x-layouts.app :title="'Profil Saya — ' . $user->name">
    <x-page-header
        title="Profil Pengguna & Keamanan Akun"
        subtitle="Kelola informasi identitas pribadi, foto avatar, dan kredensial keamanan akun Anda"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Profil Saya', 'url' => route('profile.edit')],
        ]">
    </x-page-header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Left Column: User Summary & Role Identity Card -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Profile Identity Card -->
            <div class="bg-white rounded-xl border border-[#E8E4DA] p-6 shadow-xs text-center">
                <div class="relative inline-block mx-auto mb-4">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover border-2 border-[#B89B5E] shadow-xs">
                    @else
                        <div class="w-24 h-24 rounded-full bg-[#161616] border-2 border-[#B89B5E] flex items-center justify-center text-white text-2xl font-bold tracking-wider mx-auto shadow-xs">
                            {{ $user->initials }}
                        </div>
                    @endif
                    <span class="absolute bottom-1 right-1 w-4 h-4 rounded-full bg-[#15803D] border-2 border-white" title="Status Akun Aktif"></span>
                </div>

                <h3 class="text-base font-bold text-[#161616] tracking-tight">{{ $user->name }}</h3>
                <p class="text-xs text-[#79766F] mt-0.5">{{ $user->email }}</p>

                <div class="mt-3 flex items-center justify-center gap-2">
                    @php
                        $roleBadgeClass = match($user->role) {
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
                    <span class="px-3 py-1 rounded-full text-xs font-bold tracking-wide {{ $roleBadgeClass }}">
                        {{ $user->role->label() }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#F7F6F2] border border-[#E8E4DA] text-[#161616]">
                        {{ $user->company->name ?? 'PROPFlow' }}
                    </span>
                </div>

                @if($user->avatar_path)
                    <form action="{{ route('profile.avatar.destroy') }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-[#991B1B] hover:text-red-700 font-medium inline-flex items-center gap-1.5 transition-colors">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                            <span>Hapus Foto Profil</span>
                        </button>
                    </form>
                @endif

                <div class="mt-6 pt-5 border-t border-[#E8E4DA] text-left space-y-3 text-xs">
                    <div class="flex items-center justify-between text-[#79766F]">
                        <span>Terdaftar Sejak:</span>
                        <span class="font-semibold text-[#161616]">{{ $accountStats['joined_at'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[#79766F]">
                        <span>Nomor Kontak:</span>
                        <span class="font-semibold text-[#161616]">{{ $user->phone ?: 'Belum diatur' }}</span>
                    </div>
                    @if(isset($accountStats['total_leads']))
                        <div class="flex items-center justify-between text-[#79766F]">
                            <span>Total Prospek Saya:</span>
                            <span class="font-bold text-[#B89B5E]">{{ $accountStats['total_leads'] }} Leads</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between text-[#79766F]">
                        <span>Status Keanggotaan:</span>
                        <span class="inline-flex items-center gap-1 text-[#15803D] font-bold">
                            <i class="fa-solid fa-circle-check text-xs"></i> Aktif Terverifikasi
                        </span>
                    </div>

                    @if($user->bank_name && $user->bank_account_number)
                        <div class="pt-2 border-t border-[#E8E4DA] text-xs">
                            <span class="text-[#79766F] block text-[10px] uppercase font-bold mb-0.5">Rekening Pencairan Komisi:</span>
                            <span class="font-semibold text-[#161616] block">{{ $user->bank_name }} — {{ $user->bank_account_number }}</span>
                            <span class="text-[10px] text-[#79766F] block">a.n {{ $user->bank_account_holder ?: $user->name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Role Responsibility Card -->
            <div class="bg-white rounded-xl border border-[#E8E4DA] p-6 shadow-xs">
                <div class="flex items-center space-x-2.5 mb-3">
                    <div class="w-8 h-8 rounded bg-[#F7F6F2] border border-[#E8E4DA] text-[#B89B5E] flex items-center justify-center text-sm">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-[#161616] uppercase tracking-wider">Otoritas Peran Sistem</h4>
                        <p class="text-[10px] text-[#79766F]">{{ $user->role->label() }}</p>
                    </div>
                </div>
                <p class="text-xs text-[#79766F] leading-relaxed">
                    @switch($user->role)
                        @case(\App\Enums\UserRole::SUPER_ADMIN)
                            Memegang wewenang universal dalam arsitektur sistem, pengelolaan data master, dan pemeliharaan hak akses seluruh entitas pengembang.
                            @break
                        @case(\App\Enums\UserRole::COMPANY_OWNER)
                            Memegang kendali penuh operasional perusahaan pengembang, persetujuan negosiasi harga khusus, konfigurasi website resmi, serta audit keuangan penjualan.
                            @break
                        @case(\App\Enums\UserRole::SALES_MANAGER)
                            Memimpin tim penjualan, mengawasi seluruh tahapan sales pipeline, melakukan review negosiasi diskon, serta verifikasi komisi penjualan tim.
                            @break
                        @case(\App\Enums\UserRole::TEAM_LEADER)
                            Mengkoordinir jadwal survei lokasi anggota tim pemasar, mendampingi follow-up prospek potensial, dan memantau realisasi pemesanan unit.
                            @break
                        @case(\App\Enums\UserRole::SALES_AGENT)
                            Bertanggung jawab terhadap penerimaan prospek, konsultasi properti konsumen, pelaksanaan kunjungan lapangan kaveling, serta penerbitan Surat Pesanan (SPP).
                            @break
                        @case(\App\Enums\UserRole::FINANCE)
                            Memvalidasi mutasi kas masuk konsumen (UTJ, DP, Angsuran), pengesahan kwitansi resmi, proses akad KPR perbankan, dan pencairan komisi sales.
                            @break
                        @case(\App\Enums\UserRole::ADMIN_PROPERTY)
                            Mengelola master inventori unit kaveling, spesifikasi teknis bangunan, tipe arsitektur cluster, dan denah interaktif siteplan kawasan.
                            @break
                        @default
                            Pengguna terdaftar dalam lingkungan operasional CRM Properti PROPFlow.
                    @endswitch
                </p>
            </div>
        </div>

        <!-- Right Column: Edit Profile & Change Password Forms -->
        <div class="lg:col-span-8 space-y-6">
            <!-- 1. Edit Profile Information Card -->
            <div class="bg-white rounded-xl border border-[#E8E4DA] shadow-xs">
                <div class="px-6 py-4 border-b border-[#E8E4DA] flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded bg-[#F7F6F2] border border-[#E8E4DA] text-[#B89B5E] flex items-center justify-center text-sm">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#161616]">Informasi Data Diri & Kontak</h3>
                            <p class="text-[11px] text-[#79766F]">Perbarui data nama, email resmi, nomor WhatsApp, serta foto profil Anda</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Avatar Upload -->
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] mb-1.5">
                            Foto Profil / Avatar
                        </label>
                        <div class="flex items-center gap-4">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-14 h-14 rounded-full object-cover border border-[#E8E4DA]">
                            @else
                                <div class="w-14 h-14 rounded-full bg-[#161616] border border-[#E8E4DA] text-[#B89B5E] flex items-center justify-center font-bold text-sm">
                                    {{ $user->initials }}
                                </div>
                            @endif
                            <div class="flex-1">
                                <input
                                    type="file"
                                    name="avatar"
                                    id="avatar"
                                    accept="image/png,image/jpeg,image/jpg,image/webp"
                                    class="block w-full text-xs text-[#79766F] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#161616] file:text-white hover:file:bg-[#262626] file:cursor-pointer">
                                <p class="text-[10px] text-[#79766F] mt-1">Format: JPG, PNG, atau WEBP. Ukuran maksimal 2 MB.</p>
                                @error('avatar')
                                    <p class="text-xs text-[#991B1B] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Full Name -->
                        <div>
                            <label for="name" class="block text-xs font-semibold text-[#161616] mb-1.5">
                                Nama Lengkap <span class="text-red-600">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                    <i class="fa-solid fa-user text-xs"></i>
                                </div>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    value="{{ old('name', $user->name) }}"
                                    required
                                    class="w-full pl-9 pr-3 py-2 bg-white border border-[#E8E4DA] rounded-lg text-xs text-[#161616] placeholder-[#79766F] focus:outline-none focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                            </div>
                            @error('name')
                                <p class="text-xs text-[#991B1B] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-semibold text-[#161616] mb-1.5">
                                Alamat Email Login <span class="text-red-600">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                    <i class="fa-solid fa-envelope text-xs"></i>
                                </div>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    value="{{ old('email', $user->email) }}"
                                    required
                                    class="w-full pl-9 pr-3 py-2 bg-white border border-[#E8E4DA] rounded-lg text-xs text-[#161616] placeholder-[#79766F] focus:outline-none focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                            </div>
                            @error('email')
                                <p class="text-xs text-[#991B1B] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone / WhatsApp -->
                        <div>
                            <label for="phone" class="block text-xs font-semibold text-[#161616] mb-1.5">
                                Nomor Handphone / WhatsApp
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                    <i class="fa-solid fa-phone text-xs"></i>
                                </div>
                                <input
                                    type="text"
                                    name="phone"
                                    id="phone"
                                    value="{{ old('phone', $user->phone) }}"
                                    placeholder="Contoh: 081234567890"
                                    class="w-full pl-9 pr-3 py-2 bg-white border border-[#E8E4DA] rounded-lg text-xs text-[#161616] placeholder-[#79766F] focus:outline-none focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                            </div>
                            <p class="text-[10px] text-[#79766F] mt-1">Digunakan sebagai nomor kontak resmi follow-up properti.</p>
                            @error('phone')
                                <p class="text-xs text-[#991B1B] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Company Developer (Read-only) -->
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] mb-1.5">
                                Perusahaan Pengembang
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                    <i class="fa-solid fa-building text-xs"></i>
                                </div>
                                <input
                                    type="text"
                                    value="{{ $user->company->name ?? 'PROPFlow System' }}"
                                    disabled
                                    class="w-full pl-9 pr-3 py-2 bg-[#F7F6F2] border border-[#E8E4DA] rounded-lg text-xs text-[#79766F] cursor-not-allowed">
                            </div>
                            <p class="text-[10px] text-[#79766F] mt-1">Konteks multi-tenant terikat pada perusahaan terdaftar.</p>
                        </div>

                        <!-- Timezone / 3 Zona Waktu Indonesia -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-[#161616] mb-1.5">
                                Zona Waktu Operasional PT (3 Waktu Indonesia)
                            </label>
                            @if($user->isCompanyOwner() || $user->isSuperAdmin())
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                        <i class="fa-solid fa-clock text-xs"></i>
                                    </div>
                                    <select
                                        name="timezone"
                                        id="timezone"
                                        class="w-full pl-9 pr-8 py-2 bg-white border border-[#E8E4DA] rounded-lg text-xs text-[#161616] focus:outline-none focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                                        @foreach(\App\Enums\IndonesianTimezone::cases() as $tz)
                                            <option value="{{ $tz->value }}" {{ old('timezone', $user->company?->timezone ?? $user->timezone ?? 'Asia/Jakarta') === $tz->value ? 'selected' : '' }}>
                                                {{ $tz->label() }} — ({{ $tz->regions() }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="text-[10px] text-[#79766F] mt-1">Sebagai Pengelola / Owner, ketika zona waktu PT ini disetel (misal: WIB), seluruh akun tim penjualan, finance, jadwal survei lokasi, dan log transaksi otomatis mengikuti zona waktu PT ini.</p>
                            @else
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                        <i class="fa-solid fa-clock text-xs"></i>
                                    </div>
                                    <input
                                        type="text"
                                        value="{{ $user->getTimezoneEnum()->label() }} ({{ $user->company->name ?? 'PT Pengembang' }})"
                                        disabled
                                        class="w-full pl-9 pr-3 py-2 bg-[#F7F6F2] border border-[#E8E4DA] rounded-lg text-xs text-[#79766F] cursor-not-allowed">
                                </div>
                                <p class="text-[10px] text-[#79766F] mt-1">Mengikuti zona waktu operasional PT ({{ $user->getTimezoneCode() }}). Seluruh tim, jadwal survei kaveling, dan transaksi tersinkronisasi di zona waktu ini.</p>
                            @endif
                            @error('timezone')
                                <p class="text-xs text-[#991B1B] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Bank Account Details for Commissions -->
                    <div class="pt-5 border-t border-[#E8E4DA]">
                        <div class="flex items-center space-x-2.5 mb-3">
                            <div class="w-7 h-7 rounded bg-[#F7F6F2] border border-[#E8E4DA] text-[#B89B5E] flex items-center justify-center text-xs">
                                <i class="fa-solid fa-credit-card"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-[#161616] uppercase tracking-wider">Rekening Bank Pencairan Komisi</h4>
                                <p class="text-[11px] text-[#79766F]">Digunakan oleh tim Finance untuk mentransfer hak komisi penjualan properti Anda</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Bank Name -->
                            <div>
                                <label for="bank_name" class="block text-xs font-semibold text-[#161616] mb-1.5">
                                    Nama Bank
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                        <i class="fa-solid fa-landmark text-xs"></i>
                                    </div>
                                    <input
                                        type="text"
                                        name="bank_name"
                                        id="bank_name"
                                        value="{{ old('bank_name', $user->bank_name) }}"
                                        placeholder="Contoh: BCA / Mandiri / BNI"
                                        class="w-full pl-9 pr-3 py-2 bg-white border border-[#E8E4DA] rounded-lg text-xs text-[#161616] placeholder-[#79766F] focus:outline-none focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                                </div>
                                @error('bank_name')
                                    <p class="text-xs text-[#991B1B] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bank Account Number -->
                            <div>
                                <label for="bank_account_number" class="block text-xs font-semibold text-[#161616] mb-1.5">
                                    Nomor Rekening
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                        <i class="fa-solid fa-money-check text-xs"></i>
                                    </div>
                                    <input
                                        type="text"
                                        name="bank_account_number"
                                        id="bank_account_number"
                                        value="{{ old('bank_account_number', $user->bank_account_number) }}"
                                        placeholder="Contoh: 1234567890"
                                        class="w-full pl-9 pr-3 py-2 bg-white border border-[#E8E4DA] rounded-lg text-xs font-mono text-[#161616] placeholder-[#79766F] focus:outline-none focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                                </div>
                                @error('bank_account_number')
                                    <p class="text-xs text-[#991B1B] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bank Account Holder Name -->
                            <div>
                                <label for="bank_account_holder" class="block text-xs font-semibold text-[#161616] mb-1.5">
                                    Nama Pemilik Rekening (A/N)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                        <i class="fa-solid fa-file-invoice text-xs"></i>
                                    </div>
                                    <input
                                        type="text"
                                        name="bank_account_holder"
                                        id="bank_account_holder"
                                        value="{{ old('bank_account_holder', $user->bank_account_holder) }}"
                                        placeholder="Sesuai buku tabungan"
                                        class="w-full pl-9 pr-3 py-2 bg-white border border-[#E8E4DA] rounded-lg text-xs text-[#161616] placeholder-[#79766F] focus:outline-none focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                                </div>
                                @error('bank_account_holder')
                                    <p class="text-xs text-[#991B1B] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[#E8E4DA] flex justify-end">
                        <x-button variant="gold" icon="fa-solid fa-floppy-disk" type="submit">
                            Simpan Perubahan Profil
                        </x-button>
                    </div>
                </form>
            </div>

            <!-- 2. Security & Password Update Card -->
            <div class="bg-white rounded-xl border border-[#E8E4DA] shadow-xs">
                <div class="px-6 py-4 border-b border-[#E8E4DA] flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded bg-[#F7F6F2] border border-[#E8E4DA] text-[#B89B5E] flex items-center justify-center text-sm">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#161616]">Keamanan & Kata Sandi</h3>
                            <p class="text-[11px] text-[#79766F]">Perbarui kata sandi akun Anda secara berkala untuk menjaga keamanan data properti</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('profile.password.update') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-[#161616] mb-1.5">
                            Kata Sandi Saat Ini <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                <i class="fa-solid fa-key text-xs"></i>
                            </div>
                            <input
                                type="password"
                                name="current_password"
                                id="current_password"
                                required
                                placeholder="Masukkan kata sandi lama Anda"
                                class="w-full pl-9 pr-3 py-2 bg-white border border-[#E8E4DA] rounded-lg text-xs text-[#161616] placeholder-[#79766F] focus:outline-none focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                        </div>
                        @error('current_password')
                            <p class="text-xs text-[#991B1B] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-[#161616] mb-1.5">
                                Kata Sandi Baru <span class="text-red-600">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                    <i class="fa-solid fa-lock text-xs"></i>
                                </div>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    required
                                    placeholder="Minimal 8 karakter"
                                    autocomplete="new-password"
                                    class="w-full pl-9 pr-3 py-2 bg-white border border-[#E8E4DA] rounded-lg text-xs text-[#161616] placeholder-[#79766F] focus:outline-none focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                            </div>
                            <p class="text-[10px] text-[#79766F] mt-1">Standar keamanan: Minimal 8 karakter.</p>
                            @error('password')
                                <p class="text-xs text-[#991B1B] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-[#161616] mb-1.5">
                                Ulangi Kata Sandi Baru <span class="text-red-600">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#79766F]">
                                    <i class="fa-solid fa-lock-open text-xs"></i>
                                </div>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    required
                                    placeholder="Ketik ulang kata sandi baru"
                                    class="w-full pl-9 pr-3 py-2 bg-white border border-[#E8E4DA] rounded-lg text-xs text-[#161616] placeholder-[#79766F] focus:outline-none focus:border-[#B89B5E] focus:ring-1 focus:ring-[#B89B5E]">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[#E8E4DA] flex justify-end">
                        <x-button variant="dark" icon="fa-solid fa-shield-check" type="submit">
                            Perbarui Kata Sandi
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
