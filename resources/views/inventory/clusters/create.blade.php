<x-layouts.app title="Tambah Cluster Baru">
    <x-page-header
        title="Tambah Cluster Properti Baru"
        subtitle="Daftarkan zonasi cluster hunian baru pada proyek kawasan dan unggah galeri foto cluster."
        :breadcrumbs="[
            ['label' => 'Inventori', 'url' => route('inventory.clusters.index')],
            ['label' => 'Cluster Kawasan', 'url' => route('inventory.clusters.index')],
            ['label' => 'Tambah Cluster', 'url' => '#'],
        ]">
        <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('inventory.clusters.index')">
            Kembali
        </x-button>
    </x-page-header>

    <div class="max-w-3xl">
        <form action="{{ route('inventory.clusters.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <x-card title="Informasi Cluster" subtitle="Pilih proyek induk dan tentukan nama serta kode cluster">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Proyek Induk Kawasan <span class="text-[#991B1B]">*</span>
                        </label>
                        <select name="project_id" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            <option value="">-- Pilih Proyek Induk --</option>
                            @foreach ($projects as $prj)
                                <option value="{{ $prj->id }}" {{ old('project_id', $selectedProjectId) === $prj->id ? 'selected' : '' }}>
                                    {{ $prj->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Nama Cluster <span class="text-[#991B1B]">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="Contoh: Cluster Magnolia"
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            @error('name')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                                Kode Singkatan Cluster
                            </label>
                            <input type="text" name="code" value="{{ old('code') }}"
                                   placeholder="Contoh: MAG, JAS, PIN"
                                   class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            @error('code')
                                <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Deskripsi Konsep Cluster
                        </label>
                        <textarea name="description" rows="3"
                                  placeholder="Jelaskan konsep arsitektur, lanskap taman, atau keistimewaan cluster ini..."
                                  class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">{{ old('description') }}</textarea>
                    </div>

                    <!-- Multiple Photos Upload -->
                    <div class="pt-3 border-t border-[#E8E4DA]">
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Foto & Galeri Visual Cluster (Bisa Unggah Banyak Sekaligus)
                        </label>
                        <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] file:mr-4 file:py-1.5 file:px-3.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#B89B5E] file:text-white hover:file:bg-[#A3884E]">
                        <div class="flex items-center gap-2 mt-1.5 text-[11px] text-[#79766F]">
                            <i class="fa-solid fa-images text-[#B89B5E]"></i>
                            <span>Pilih beberapa foto sekaligus (gate cluster, rumah contoh, fasilitas taman, clubhouse). Format: JPG, PNG, WEBP. Maks. 10 foto.</span>
                        </div>
                        @error('photos')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                        @error('photos.*')
                            <p class="mt-1 text-xs text-[#991B1B]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </x-card>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <x-button variant="secondary" :href="route('inventory.clusters.index')">
                    Batal
                </x-button>
                <x-button type="submit" variant="gold" icon="fa-solid fa-plus">
                    Simpan Cluster Baru
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
