<x-layouts.app title="Tambah Unit Properti Baru">
    <x-page-header
        title="Tambah Unit Properti Baru"
        subtitle="Daftarkan kaveling rumah, spesifikasi luas bangunan, dan penetapan harga jual ke sistem."
        :breadcrumbs="[
            ['label' => 'Inventori', 'url' => route('inventory.units.index')],
            ['label' => 'Property Units', 'url' => route('inventory.units.index')],
            ['label' => 'Tambah Unit', 'url' => '#'],
        ]">
        <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('inventory.units.index')">
            Kembali
        </x-button>
    </x-page-header>

    <div class="max-w-4xl">
        <form action="{{ route('inventory.units.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Lokasi & Cluster -->
            <x-card title="1. Lokasi Cluster & Kaveling" subtitle="Tentukan penempatan unit dalam cluster dan proyek perumahan">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Cluster Properti <span class="text-[#991B1B]">*</span>
                        </label>
                        <select name="cluster_id" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            <option value="">-- Pilih Cluster & Proyek --</option>
                            @foreach ($clusters as $cl)
                                <option value="{{ $cl->id }}" {{ old('cluster_id') === $cl->id ? 'selected' : '' }}>
                                    {{ $cl->name }} (Proyek: {{ $cl->project->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('cluster_id')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Tipe Desain Properti (Opsional)
                        </label>
                        <select name="property_type_id" class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            <option value="">-- Standar / Custom --</option>
                            @foreach ($propertyTypes as $pt)
                                <option value="{{ $pt->id }}" {{ old('property_type_id') === $pt->id ? 'selected' : '' }}>
                                    {{ $pt->name }} (LB: {{ $pt->building_area }}m² / LT: {{ $pt->land_area }}m²)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Nomor Unit <span class="text-[#991B1B]">*</span>
                        </label>
                        <input type="text" name="unit_number" value="{{ old('unit_number') }}" required
                               placeholder="Contoh: 05, A-12, Hook-01"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        @error('unit_number')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Blok Kaveling
                        </label>
                        <input type="text" name="block" value="{{ old('block') }}"
                               placeholder="Contoh: Blok A, Blok Boulevard"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                    </div>
            </x-card>

            <!-- Section 2: Spesifikasi Fisik & Bangunan Lengkap -->
            <x-card title="2. Spesifikasi Fisik & Bangunan" subtitle="Parameter dimensi, tata ruang, utilitas, legalitas, serta material konstruksi arsitektural">
                <div class="space-y-6">
                    <!-- Sub-section 2.1: Dimensi & Tata Ruang Utama -->
                    <div>
                        <div class="flex items-center space-x-2 pb-2 mb-3 border-b border-[#E8E4DA]">
                            <span class="w-1.5 h-4 bg-[#B89B5E] rounded-full inline-block"></span>
                            <h4 class="text-xs font-bold text-[#161616] uppercase tracking-wider">A. Dimensi Kaveling & Tata Ruang</h4>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Luas Bangunan (LB) m² <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="number" name="building_area" value="{{ old('building_area', 60) }}" min="1" step="0.5" required
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('building_area')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Luas Tanah (LT) m² <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="number" name="land_area" value="{{ old('land_area', 90) }}" min="1" step="0.5" required
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('land_area')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Dimensi Kaveling (P x L)
                                </label>
                                <input type="text" name="dimension" value="{{ old('dimension', '6 x 15 m') }}" placeholder="Contoh: 6 x 15 m"
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @error('dimension')
                                    <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Jumlah Lantai <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="number" name="floors" value="{{ old('floors', 1) }}" min="1" max="5" required
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Kamar Tidur (KT) <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="number" name="bedrooms" value="{{ old('bedrooms', 2) }}" min="1" max="20" required
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Kamar Mandi (KM) <span class="text-[#991B1B]">*</span>
                                </label>
                                <input type="number" name="bathrooms" value="{{ old('bathrooms', 1) }}" min="1" max="10" required
                                       class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Kapasitas Carport
                                </label>
                                <select name="carports" class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                    <option value="1" {{ old('carports', '1') == '1' ? 'selected' : '' }}>1 Mobil</option>
                                    <option value="2" {{ old('carports') == '2' ? 'selected' : '' }}>2 Mobil</option>
                                    <option value="3" {{ old('carports') == '3' ? 'selected' : '' }}>3 Mobil</option>
                                    <option value="0" {{ old('carports') === '0' ? 'selected' : '' }}>Tanpa Carport</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Arah Hadap Rumah
                                </label>
                                <select name="direction" class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                    <option value="Utara" {{ old('direction', 'Utara') === 'Utara' ? 'selected' : '' }}>Utara</option>
                                    <option value="Selatan" {{ old('direction') === 'Selatan' ? 'selected' : '' }}>Selatan</option>
                                    <option value="Timur" {{ old('direction') === 'Timur' ? 'selected' : '' }}>Timur</option>
                                    <option value="Barat" {{ old('direction') === 'Barat' ? 'selected' : '' }}>Barat</option>
                                    <option value="Timur Laut" {{ old('direction') === 'Timur Laut' ? 'selected' : '' }}>Timur Laut</option>
                                    <option value="Tenggara" {{ old('direction') === 'Tenggara' ? 'selected' : '' }}>Tenggara</option>
                                    <option value="Barat Daya" {{ old('direction') === 'Barat Daya' ? 'selected' : '' }}>Barat Daya</option>
                                    <option value="Barat Laut" {{ old('direction') === 'Barat Laut' ? 'selected' : '' }}>Barat Laut</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-section 2.2: Utilitas & Legalitas -->
                    <div>
                        <div class="flex items-center space-x-2 pb-2 mb-3 border-b border-[#E8E4DA]">
                            <span class="w-1.5 h-4 bg-[#B89B5E] rounded-full inline-block"></span>
                            <h4 class="text-xs font-bold text-[#161616] uppercase tracking-wider">B. Utilitas & Legalitas Dokumen</h4>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Daya Listrik (PLN)
                                </label>
                                <select name="electricity" class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                    <option value="1.300 VA" {{ old('electricity') === '1.300 VA' ? 'selected' : '' }}>1.300 VA</option>
                                    <option value="2.200 VA" {{ old('electricity', '2.200 VA') === '2.200 VA' ? 'selected' : '' }}>2.200 VA (Standar)</option>
                                    <option value="3.500 VA" {{ old('electricity') === '3.500 VA' ? 'selected' : '' }}>3.500 VA</option>
                                    <option value="4.400 VA" {{ old('electricity') === '4.400 VA' ? 'selected' : '' }}>4.400 VA</option>
                                    <option value="5.500 VA" {{ old('electricity') === '5.500 VA' ? 'selected' : '' }}>5.500 VA</option>
                                    <option value="7.700 VA+" {{ old('electricity') === '7.700 VA+' ? 'selected' : '' }}>7.700 VA+</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Sumber Air Bersih
                                </label>
                                <select name="water_source" class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                    <option value="PDAM Kota" {{ old('water_source', 'PDAM Kota') === 'PDAM Kota' ? 'selected' : '' }}>PDAM Kota</option>
                                    <option value="WTP Kawasan Mandiri" {{ old('water_source') === 'WTP Kawasan Mandiri' ? 'selected' : '' }}>WTP Kawasan Mandiri</option>
                                    <option value="Sumur Bor / Jet Pump" {{ old('water_source') === 'Sumur Bor / Jet Pump' ? 'selected' : '' }}>Sumur Bor / Jet Pump</option>
                                    <option value="PDAM + Toren Air Cadangan" {{ old('water_source') === 'PDAM + Toren Air Cadangan' ? 'selected' : '' }}>PDAM + Toren Air Cadangan</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                    Status Sertifikat / Legalitas
                                </label>
                                <select name="certificate_type" class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                    <option value="SHM (Sertifikat Hak Milik)" {{ old('certificate_type', 'SHM (Sertifikat Hak Milik)') === 'SHM (Sertifikat Hak Milik)' ? 'selected' : '' }}>SHM (Sertifikat Hak Milik)</option>
                                    <option value="HGB Murni" {{ old('certificate_type') === 'HGB Murni' ? 'selected' : '' }}>HGB Murni</option>
                                    <option value="HGB Split Siap Naik SHM" {{ old('certificate_type') === 'HGB Split Siap Naik SHM' ? 'selected' : '' }}>HGB Split (Siap Naik SHM)</option>
                                    <option value="Strata Title" {{ old('certificate_type') === 'Strata Title' ? 'selected' : '' }}>Strata Title</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-section 2.3: Material Konstruksi & Finishing Arsitektur -->
                    <div>
                        <div class="flex items-center space-x-2 pb-2 mb-3 border-b border-[#E8E4DA]">
                            <span class="w-1.5 h-4 bg-[#B89B5E] rounded-full inline-block"></span>
                            <h4 class="text-xs font-bold text-[#161616] uppercase tracking-wider">C. Material Konstruksi & Finishing Arsitektural</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1.5 flex items-center">
                                    <i class="fa-solid fa-cubes text-[#B89B5E] text-xs mr-1.5"></i>
                                    Pondasi & Struktur
                                </label>
                                <textarea name="building_specs[foundation]" rows="2"
                                          placeholder="Contoh: Batu Kali & Mini Pile, Struktur Beton Bertulang SNI K-250"
                                          class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] resize-y leading-relaxed">{{ old('building_specs.foundation', 'Batu Kali & Mini Pile, Struktur Beton Bertulang') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1.5 flex items-center">
                                    <i class="fa-solid fa-trowel-bricks text-[#B89B5E] text-xs mr-1.5"></i>
                                    Dinding & Cat
                                </label>
                                <textarea name="building_specs[wall]" rows="2"
                                          placeholder="Contoh: Pasangan Bata Merah Plester Aci, Cat Weather Shield Eksterior & Cat Interior Premium"
                                          class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] resize-y leading-relaxed">{{ old('building_specs.wall', 'Bata Merah Plester Aci, Cat Weather Shield') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1.5 flex items-center">
                                    <i class="fa-solid fa-house-chimney text-[#B89B5E] text-xs mr-1.5"></i>
                                    Rangka & Penutup Atap
                                </label>
                                <textarea name="building_specs[roof]" rows="2"
                                          placeholder="Contoh: Rangka Baja Ringan Zincalume, Genteng Beton Flat Monier / Keramik Glazur"
                                          class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] resize-y leading-relaxed">{{ old('building_specs.roof', 'Rangka Baja Ringan, Genteng Beton Flat Monier') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1.5 flex items-center">
                                    <i class="fa-solid fa-border-all text-[#B89B5E] text-xs mr-1.5"></i>
                                    Lantai Utama & Kamar
                                </label>
                                <textarea name="building_specs[floor]" rows="2"
                                          placeholder="Contoh: Homogeneous Tile 60x60 cm Glazed, Lantai Kamar Master Parket Vinyl Premium"
                                          class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] resize-y leading-relaxed">{{ old('building_specs.floor', 'Homogeneous Tile 60x60 cm, Kamar Parket Vinyl') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1.5 flex items-center">
                                    <i class="fa-solid fa-door-open text-[#B89B5E] text-xs mr-1.5"></i>
                                    Kusen, Pintu & Jendela
                                </label>
                                <textarea name="building_specs[doors_windows]" rows="2"
                                          placeholder="Contoh: Kusen Aluminium Powder Coating 4 Inch, Daun Pintu Solid Engineering Wood"
                                          class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] resize-y leading-relaxed">{{ old('building_specs.doors_windows', 'Aluminium Powder Coating, Pintu Solid Engineering Wood') }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-[#161616] uppercase mb-1.5 flex items-center">
                                    <i class="fa-solid fa-toilet text-[#B89B5E] text-xs mr-1.5"></i>
                                    Sanitair & Perlengkapan Mandi
                                </label>
                                <textarea name="building_specs[sanitary]" rows="2"
                                          placeholder="Contoh: Kloset Duduk Toto / American Standard, Shower Set, Wastafel & Kran Aerator"
                                          class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] resize-y leading-relaxed">{{ old('building_specs.sanitary', 'Kloset Duduk Toto / American Standard, Shower Set') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-section 2.4: Fitur Khusus & Smart Home -->
                    <div>
                        <div class="flex items-center space-x-2 pb-2 mb-3 border-b border-[#E8E4DA]">
                            <span class="w-1.5 h-4 bg-[#B89B5E] rounded-full inline-block"></span>
                            <h4 class="text-xs font-bold text-[#161616] uppercase tracking-wider">D. Fasilitas Unit & Fitur Tambahan</h4>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1.5 flex items-center">
                                <i class="fa-solid fa-star text-[#B89B5E] text-xs mr-1.5"></i>
                                Fitur Khusus, Smart Home, atau Bonus Perlengkapan Unit
                            </label>
                            <textarea name="building_specs[smart_features]" rows="2"
                                      placeholder="Contoh: Smart Digital Door Lock, Solar Water Heater, Meja Dapur Granit + Sink, Canopy Carport Minimalis"
                                      class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] resize-y leading-relaxed">{{ old('building_specs.smart_features', 'Smart Digital Door Lock, Canopy Carport Minimalis, Meja Dapur Granit + Sink') }}</textarea>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Section 3: Harga Jual & Ketentuan Promo -->
            <x-card title="3. Harga Jual & Promosi" subtitle="Pricelist resmi unit untuk penawaran konsumen">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Harga Jual Pricelist (Rp) <span class="text-[#991B1B]">*</span>
                        </label>
                        <input type="number" name="selling_price" value="{{ old('selling_price', 850000000) }}" min="10000000" step="1000000" required
                               class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] font-bold focus:outline-none focus:border-[#B89B5E]">
                        @error('selling_price')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Program Promo Unit (Opsional)
                        </label>
                        <input type="text" name="promo" value="{{ old('promo') }}"
                               placeholder="Contoh: Free Biaya KPR, Subsidi DP 10%, Free Smart Lock"
                               class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                        Catatan Khusus Kaveling
                    </label>
                    <textarea name="notes" rows="2"
                              placeholder="Kaveling depan taman, dekat gerbang utama, posisi hook..."
                              class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">{{ old('notes') }}</textarea>
                </div>
            </x-card>

            <!-- Section 4: Foto Fasad & Visual Unit -->
            <x-card title="4. Foto Arsitektur & Fasad Unit" subtitle="Unggah foto fasad rumah atau rendering unit kaveling">
                <div>
                    <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                        Foto Fasad Unit (Opsional)
                    </label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#B89B5E] file:text-white hover:file:bg-[#A3884E]">
                    <p class="text-[11px] text-[#79766F] mt-1">Format: JPG, PNG, WEBP (Maks. 5MB). Jika dikosongkan, sistem akan menggunakan foto default tipe properti.</p>
                    @error('image')
                        <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                    @enderror
                </div>
            </x-card>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <x-button variant="secondary" :href="route('inventory.units.index')">
                    Batal
                </x-button>
                <x-button type="submit" variant="gold" icon="fa-solid fa-plus">
                    Simpan Unit ke Inventori
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
