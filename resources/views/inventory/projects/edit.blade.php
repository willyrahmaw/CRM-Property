<x-layouts.app :title="'Edit Proyek — ' . $project->name">
    <x-page-header
        :title="'Edit Proyek Kawasan — ' . $project->name"
        subtitle="Perbarui data kawasan hunian, lokasi developer, status peluncuran, atau foto cover proyek."
        :breadcrumbs="[
            ['label' => 'Inventori', 'url' => route('inventory.projects.index')],
            ['label' => 'Proyek Properti', 'url' => route('inventory.projects.index')],
            ['label' => $project->name, 'url' => route('inventory.projects.show', $project)],
            ['label' => 'Edit Proyek', 'url' => '#'],
        ]">
        <div class="flex items-center gap-3">
            <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('inventory.projects.show', $project)">
                Kembali ke Detail
            </x-button>
        </div>
    </x-page-header>

    <div class="max-w-3xl">
        <form action="{{ route('inventory.projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <x-card title="Informasi Kawasan Proyek" subtitle="Data utama proyek perumahan atau apartemen komersial">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Nama Proyek Kawasan <span class="text-[#991B1B]">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $project->name) }}" required
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
                            <input type="text" name="developer_name" value="{{ old('developer_name', $project->developer_name) }}"
                                   placeholder="Contoh: PT Grand Harmony Land"
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Kota / Wilayah <span class="text-[#991B1B]">*</span>
                            </label>
                            <input type="text" name="city" value="{{ old('city', $project->city) }}" required
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
                                    <option value="{{ $st->value }}" {{ old('status', $project->status->value) === $st->value ? 'selected' : '' }}>
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
                                  class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">{{ old('address', $project->address) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Deskripsi / Konsep Kawasan
                        </label>
                        <textarea name="description" rows="3"
                                  placeholder="Jelaskan konsep hunian, keunggulan arsitektur, atau fasilitas kawasan..."
                                  class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Foto / Cover Proyek Kawasan
                        </label>

                        @if ($project->image)
                            <div class="mb-3 flex items-center gap-3 p-2.5 rounded-lg border border-[#E8E4DA] bg-[#F7F6F2]">
                                <img src="{{ $project->image_url }}" alt="{{ $project->name }}" class="w-16 h-12 object-cover rounded border border-[#E8E4DA]">
                                <div class="text-xs text-[#79766F]">
                                    <span class="font-semibold text-[#161616] block">Foto Cover Saat Ini</span>
                                    <span>Unggah file baru di bawah ini hanya jika ingin menggantinya.</span>
                                </div>
                            </div>
                        @endif

                        <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#B89B5E] file:text-white hover:file:bg-[#A3884E]">
                        <p class="text-[11px] text-[#79766F] mt-1">Format: JPG, PNG, WEBP (Maks. 5MB). Kosongkan jika tidak ingin mengubah foto.</p>
                        @error('image')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <x-button variant="secondary" :href="route('inventory.projects.show', $project)">
                    Batal
                </x-button>
                <x-button type="submit" variant="gold" icon="fa-solid fa-floppy-disk">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
