<x-layouts.app :title="'Pengaturan Website — ' . $company->name">
    <x-page-header
        title="Pengaturan Website & Portal Publik"
        :subtitle="'Konfigurasi identitas branding ' . $company->name . ', kontak marketing, media sosial, dan optimasi SEO portal publik.'"
        :breadcrumbs="[
            ['label' => 'Administrasi', 'url' => '#'],
            ['label' => 'Pengaturan Website', 'url' => '#'],
        ]">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-[#161616] text-[#D7C49E] border border-[#E8E4DA]">
                <i class="fa-solid fa-crown text-[10px] mr-1.5 text-[#B89B5E]"></i>
                Akses Eksklusif Owner
            </span>
        </div>
    </x-page-header>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-white border-l-4 border-[#15803D] border border-[#E8E4DA] shadow-sm flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full bg-[#DCFCE7] text-[#15803D] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-[#161616]">Pengaturan Berhasil Disimpan</h4>
                    <p class="text-xs text-[#79766F]">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('settings.website.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Columns: Settings Form Groups -->
            <div class="lg:col-span-2 space-y-6">
                <!-- 1. Identitas & Branding Utama -->
                <x-card title="1. Identitas & Branding Portal" subtitle="Informasi nama situs, slogan, logo perusahaan, dan profil pengembang">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Judul Portal / Nama Situs <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="text" name="site_title" value="{{ old('site_title', $settings['site_title'] ?? '') }}" required
                                       placeholder="Contoh: Grand Emerald Residence Official"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('site_title')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Tagline / Slogan Brand
                                </label>
                                <input type="text" name="tagline" value="{{ old('tagline', $settings['tagline'] ?? '') }}"
                                       placeholder="Contoh: Luxury Living & Modern Harmony Redefined"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('tagline')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Deskripsi Profil Pengembang / Tentang Kami
                            </label>
                            <textarea name="about_text" rows="3"
                                      placeholder="Ringkasan rekam jejak, visi misi, dan komitmen developer kepada konsumen..."
                                      class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] resize-y leading-relaxed">{{ old('about_text', $settings['about_text'] ?? '') }}</textarea>
                            @error('about_text')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Logo & Favicon Upload -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-[#E8E4DA]">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Logo Resmi Portal (PNG, SVG, JPG)
                                </label>
                                <input type="file" name="logo" accept="image/*"
                                       class="w-full px-3 py-1.5 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] file:mr-3 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-[#B89B5E] file:text-white hover:file:bg-[#A3884E]">
                                <p class="text-[11px] text-[#79766F] mt-1">Format horizontal resolusi tinggi (Rekomendasi rasio 4:1 atau 3:1).</p>
                                @if(!empty($settings['logo_path']))
                                    <div class="mt-2 flex items-center gap-2 p-2 bg-[#F7F6F2] rounded-lg border border-[#E8E4DA]">
                                        <span class="text-[10px] text-[#79766F]">Logo Aktif:</span>
                                        <span class="text-[11px] font-bold text-[#161616] truncate">{{ basename($settings['logo_path']) }}</span>
                                    </div>
                                @endif
                                @error('logo')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Favicon Tab Browser (ICO, PNG)
                                </label>
                                <input type="file" name="favicon" accept=".ico,.png,.jpg,.svg"
                                       class="w-full px-3 py-1.5 text-xs rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] file:mr-3 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-[#B89B5E] file:text-white hover:file:bg-[#A3884E]">
                                <p class="text-[11px] text-[#79766F] mt-1">Ikon kecil tab browser (Rekomendasi rasio 1:1, 64x64 px).</p>
                                @if(!empty($settings['favicon_path']))
                                    <div class="mt-2 flex items-center gap-2 p-2 bg-[#F7F6F2] rounded-lg border border-[#E8E4DA]">
                                        <span class="text-[10px] text-[#79766F]">Favicon Aktif:</span>
                                        <span class="text-[11px] font-bold text-[#161616] truncate">{{ basename($settings['favicon_path']) }}</span>
                                    </div>
                                @endif
                                @error('favicon')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </x-card>

                <!-- 2. Kontak & Lokasi Marketing Gallery -->
                <x-card title="2. Kontak Resmi & Lokasi Marketing Gallery" subtitle="Akses komunikasi konsumen, hotline WhatsApp, dan jam layanan kantor pemasaran">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Email Resmi CS / Inquiry
                                </label>
                                <input type="email" name="email" value="{{ old('email', $settings['email'] ?? '') }}"
                                       placeholder="info@developer.co.id"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('email')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Hotline Telepon Kantor
                                </label>
                                <input type="text" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}"
                                       placeholder="021-88997700"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('phone')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    WhatsApp Official Center
                                </label>
                                <input type="text" name="whatsapp" value="{{ old('whatsapp', $settings['whatsapp'] ?? '') }}"
                                       placeholder="Contoh: 081199887766"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('whatsapp')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Alamat Kantor Marketing Gallery
                                </label>
                                <textarea name="address" rows="2"
                                          placeholder="Alamat lengkap gedung pemasaran atau kantor proyek..."
                                          class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] resize-y">{{ old('address', $settings['address'] ?? '') }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Jam Operasional Layanan
                                </label>
                                <input type="text" name="operational_hours" value="{{ old('operational_hours', $settings['operational_hours'] ?? '') }}"
                                       placeholder="Contoh: Setiap Hari 08:30 - 18:00 WIB"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] mb-2">

                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Tautan Google Maps / Share Location
                                </label>
                                <input type="url" name="google_maps_url" value="{{ old('google_maps_url', $settings['google_maps_url'] ?? '') }}"
                                       placeholder="https://maps.app.goo.gl/..."
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] mb-2">

                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Zona Waktu Operasional PT (3 Waktu Indonesia)
                                </label>
                                <select name="timezone" class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                    @foreach(\App\Enums\IndonesianTimezone::cases() as $tz)
                                        <option value="{{ $tz->value }}" {{ old('timezone', $company->timezone ?? 'Asia/Jakarta') === $tz->value ? 'selected' : '' }}>
                                            {{ $tz->label() }} — ({{ $tz->regions() }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-[#79766F] mt-1">Ketika zona waktu PT ini disetel, seluruh tim penjualan, finance, dan jadwal survei lokasi PT ini otomatis mengikuti zona waktu ini.</p>
                            </div>
                        </div>
                    </div>
                </x-card>

                <!-- 3. Banner Pengumuman & Promo Berjalan -->
                <x-card title="3. Banner Pengumuman & Promo Berjalan" subtitle="Pesan berjalan di bagian atas halaman portal untuk penawaran khusus konsumen">
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 p-3 bg-[#F7F6F2] rounded-lg border border-[#E8E4DA]">
                            <input type="checkbox" id="announcement_active" name="announcement_active" value="1"
                                   {{ old('announcement_active', $settings['announcement_active'] ?? false) ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#B89B5E] rounded border-[#E8E4DA] focus:ring-[#B89B5E]">
                            <label for="announcement_active" class="text-xs font-bold text-[#161616] cursor-pointer">
                                Aktifkan Pita Pengumuman / Promo di Atas Halaman Publik
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Teks Pengumuman Promo Berjalan
                            </label>
                            <input type="text" name="announcement_text" value="{{ old('announcement_text', $settings['announcement_text'] ?? '') }}"
                                   placeholder="Contoh: Promo Spesial: Subsidi DP 10%, Bebas Biaya KPR & Hadiah Langsung Tanpa Diundi!"
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            <p class="text-[11px] text-[#79766F] mt-1">Pesan ini akan tampil di bagian header situs dengan sorotan warna gold.</p>
                        </div>
                    </div>
                </x-card>

                <!-- 4. Akun Media Sosial Resmi -->
                <x-card title="4. Saluran Media Sosial Resmi" subtitle="Tautan profil media sosial pengembang untuk meningkatkan kepercayaan calon pembeli">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1 flex items-center">
                                <i class="fa-brands fa-instagram text-[#B89B5E] mr-1.5 text-sm"></i>
                                Instagram Resmi
                            </label>
                            <input type="text" name="instagram" value="{{ old('instagram', $settings['instagram'] ?? '') }}"
                                   placeholder="@nama_perusahaan"
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1 flex items-center">
                                <i class="fa-brands fa-facebook text-[#B89B5E] mr-1.5 text-sm"></i>
                                Halaman Facebook
                            </label>
                            <input type="text" name="facebook" value="{{ old('facebook', $settings['facebook'] ?? '') }}"
                                   placeholder="https://facebook.com/..."
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1 flex items-center">
                                <i class="fa-brands fa-youtube text-[#B89B5E] mr-1.5 text-sm"></i>
                                Channel YouTube
                            </label>
                            <input type="text" name="youtube" value="{{ old('youtube', $settings['youtube'] ?? '') }}"
                                   placeholder="https://youtube.com/@..."
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1 flex items-center">
                                <i class="fa-brands fa-tiktok text-[#B89B5E] mr-1.5 text-sm"></i>
                                Akun TikTok
                            </label>
                            <input type="text" name="tiktok" value="{{ old('tiktok', $settings['tiktok'] ?? '') }}"
                                   placeholder="@tiktok_official"
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        </div>
                    </div>
                </x-card>

                <!-- 5. Optimasi Mesin Pencari (SEO) & Analytics -->
                <x-card title="5. Optimasi Mesin Pencari (SEO) & Pelacakan Web" subtitle="Pengaturan metadata mesin pencari (Google) serta integrasi ID Analytics">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Meta Title (Judul Pencarian Google)
                            </label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', $settings['meta_title'] ?? '') }}"
                                   placeholder="Contoh: Grand Emerald Residence — Perumahan Mewah BSD Serpong"
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Meta Description
                            </label>
                            <textarea name="meta_description" rows="2"
                                      placeholder="Deskripsi singkat yang muncul di halaman pencarian Google (Maks. 160 karakter disarankan)..."
                                      class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] resize-y">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Kata Kunci Pencarian (Meta Keywords)
                            </label>
                            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}"
                                   placeholder="Contoh: rumah mewah bsd, perumahan tangerang, kpr properti murah"
                                   class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-[#E8E4DA]">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Google Analytics Measurement ID
                                </label>
                                <input type="text" name="google_analytics_id" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}"
                                       placeholder="G-XXXXXXXXXX"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Meta / Facebook Pixel ID
                                </label>
                                <input type="text" name="facebook_pixel_id" value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id'] ?? '') }}"
                                       placeholder="123456789012345"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Right Column: Live Branding Simulation & Fast Actions -->
            <div class="space-y-6">
                <!-- Action Sticky Box -->
                <x-card title="Publikasi & Penyimpanan" subtitle="Perbarui identitas publik perusahaan">
                    <div class="space-y-3 text-xs">
                        <p class="text-[#79766F] leading-relaxed">
                            Perubahan konfigurasi ini akan diterapkan langsung ke katalog publik dan portal pemasaran properti.
                        </p>

                        <div class="pt-2">
                            <x-button type="submit" variant="gold" icon="fa-solid fa-floppy-disk" class="w-full justify-center py-3 text-sm">
                                Simpan Pengaturan Website
                            </x-button>
                        </div>
                    </div>
                </x-card>

                <!-- Live Preview Simulation Card -->
                <x-card title="Pratinjau Identitas Publik" subtitle="Simulasi tampilan brand di mata konsumen">
                    <div class="rounded-xl border border-[#E8E4DA] overflow-hidden bg-[#F7F6F2] shadow-sm text-xs">
                        <!-- Simulated Top Banner -->
                        @if(!empty($settings['announcement_active']))
                            <div class="bg-[#B89B5E] text-white px-3 py-1.5 text-[10px] font-bold text-center truncate">
                                <i class="fa-solid fa-bullhorn mr-1 text-[9px]"></i>
                                {{ $settings['announcement_text'] ?? 'Promo Eksklusif Aktif' }}
                            </div>
                        @endif

                        <!-- Simulated Brand Header -->
                        <div class="p-4 bg-white border-b border-[#E8E4DA]">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-[#161616] text-[#B89B5E] flex items-center justify-center font-black text-sm">
                                    {{ strtoupper(substr($settings['site_title'] ?? $company->name, 0, 2)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <h4 class="font-bold text-[#161616] text-xs truncate">{{ $settings['site_title'] ?? $company->name }}</h4>
                                    <p class="text-[10px] text-[#79766F] truncate">{{ $settings['tagline'] ?? 'Luxury Residence' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Simulated Info Box -->
                        <div class="p-4 space-y-2.5">
                            <div class="flex items-start gap-2 text-[#161616]">
                                <i class="fa-solid fa-location-dot text-[#B89B5E] text-xs mt-0.5"></i>
                                <span class="text-[11px] leading-tight">{{ $settings['address'] ?? $company->address ?? 'Alamat kantor pemasaran' }}</span>
                            </div>

                            <div class="flex items-center gap-2 text-[#161616]">
                                <i class="fa-solid fa-phone text-[#B89B5E] text-xs"></i>
                                <span class="text-[11px] font-semibold">{{ $settings['phone'] ?? $company->phone ?? '-' }}</span>
                            </div>

                            <div class="flex items-center gap-2 text-[#161616]">
                                <i class="fa-brands fa-whatsapp text-[#15803D] text-xs"></i>
                                <span class="text-[11px] font-bold text-[#15803D]">{{ $settings['whatsapp'] ?? '-' }}</span>
                            </div>

                            <div class="flex items-center gap-2 text-[#79766F] pt-2 border-t border-[#E8E4DA]">
                                <i class="fa-solid fa-clock text-xs"></i>
                                <span class="text-[10px]">{{ $settings['operational_hours'] ?? 'Buka Setiap Hari' }}</span>
                            </div>
                        </div>

                        <!-- Social Media Strip -->
                        <div class="p-3 bg-white border-t border-[#E8E4DA] flex items-center justify-around text-[#79766F]">
                            <i class="fa-brands fa-instagram hover:text-[#B89B5E] cursor-pointer"></i>
                            <i class="fa-brands fa-facebook hover:text-[#B89B5E] cursor-pointer"></i>
                            <i class="fa-brands fa-youtube hover:text-[#B89B5E] cursor-pointer"></i>
                            <i class="fa-brands fa-tiktok hover:text-[#B89B5E] cursor-pointer"></i>
                        </div>
                    </div>
                </x-card>

                <!-- Tenant Company Context -->
                <x-card title="Konteks Perusahaan Developer" subtitle="Data entitas bisnis terdaftar">
                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-[#E8E4DA]">
                            <span class="text-[#79766F]">Kode Developer:</span>
                            <span class="font-bold text-[#161616]">{{ $company->code }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-[#E8E4DA]">
                            <span class="text-[#79766F]">Badan Usaha:</span>
                            <span class="font-bold text-[#161616]">{{ $company->name }}</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-[#E8E4DA]">
                            <span class="text-[#79766F]">Status Akun:</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#DCFCE7] text-[#15803D]">
                                Aktif & Terverifikasi
                            </span>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </form>
</x-layouts.app>
