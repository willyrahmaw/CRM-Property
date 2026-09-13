<x-layouts.app title="Tambah Proyek Kawasan Baru">
    <x-page-header
        title="Tambah Proyek Properti Baru"
        subtitle="Daftarkan master proyek kawasan hunian, lokasi developer, dan perencanaan pengembangan."
        :breadcrumbs="[
            ['label' => 'Inventori', 'url' => route('inventory.projects.index')],
            ['label' => 'Proyek Properti', 'url' => route('inventory.projects.index')],
            ['label' => 'Tambah Proyek', 'url' => '#'],
        ]">
        <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('inventory.projects.index')">
            Kembali
        </x-button>
    </x-page-header>

    <div class="max-w-3xl">
        <form action="{{ route('inventory.projects.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <x-card title="Informasi Kawasan Proyek" subtitle="Data utama proyek perumahan atau apartemen komersial">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Nama Proyek Kawasan <span class="text-[#991B1B]">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="Contoh: Grand Harmony Residence"
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            @error('name')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Developer Pelaksana
                            </label>
                            <input type="text" name="developer_name" value="{{ old('developer_name') }}"
                                   placeholder="Contoh: PT Grand Harmony Land"
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Kota / Wilayah <span class="text-[#991B1B]">*</span>
                            </label>
                            <input type="text" name="city" value="{{ old('city') }}" required
                                   placeholder="Contoh: Tangerang Selatan, Bogor, Surabaya"
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            @error('city')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Status Peluncuran Proyek <span class="text-[#991B1B]">*</span>
                            </label>
                            <select name="status" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                                @foreach ($statuses as $st)
                                    <option value="{{ $st->value }}" {{ old('status') === $st->value ? 'selected' : '' }}>
                                        {{ $st->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Alamat Lengkap Proyek
                        </label>
                        <textarea name="address" rows="2"
                                  placeholder="Jl. Boulevard Barat No. 88, BSD City..."
                                  class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">{{ old('address') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Foto / Cover Proyek Kawasan
                        </label>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#B89B5E] file:text-white hover:file:bg-[#A3884E]">
                        <p class="text-[11px] text-[#79766F] mt-1">Format: JPG, PNG, WEBP (Maks. 5MB). Foto masterplan atau gerbang kawasan.</p>
                        @error('image')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <x-button variant="secondary" :href="route('inventory.projects.index')">
                    Batal
                </x-button>
                <x-button type="submit" variant="gold" icon="fa-solid fa-plus">
                    Simpan Proyek Kawasan
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
