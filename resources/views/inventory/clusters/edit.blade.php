<x-layouts.app :title="'Edit Cluster — ' . $cluster->name">
    <x-page-header
        :title="'Edit Cluster — ' . $cluster->name"
        subtitle="Perbarui data cluster hunian, kode, deskripsi, dan kelola galeri foto cluster."
        :breadcrumbs="[
            ['label' => 'Inventori', 'url' => route('inventory.clusters.index')],
            ['label' => 'Cluster Kawasan', 'url' => route('inventory.clusters.index')],
            ['label' => $cluster->name, 'url' => '#'],
            ['label' => 'Edit Cluster', 'url' => '#'],
        ]">
        <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('inventory.clusters.index')">
            Kembali
        </x-button>
    </x-page-header>

    <div class="max-w-4xl">
        <form action="{{ route('inventory.clusters.update', $cluster) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <x-card title="Informasi Cluster" subtitle="Pilih proyek induk dan perbarui data cluster">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Proyek Induk Kawasan <span class="text-[#991B1B]">*</span>
                        </label>
                        <select name="project_id" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">
                            @foreach ($projects as $prj)
                                <option value="{{ $prj->id }}" {{ old('project_id', $cluster->project_id) === $prj->id ? 'selected' : '' }}>
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
                            <input type="text" name="name" value="{{ old('name', $cluster->name) }}" required
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
                            <input type="text" name="code" value="{{ old('code', $cluster->code) }}"
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
                                  class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E]">{{ old('description', $cluster->description) }}</textarea>
                    </div>

                    <!-- Galeri Foto Saat Ini -->
                    @if (!empty($cluster->photos) && is_array($cluster->photos) && count($cluster->photos) > 0)
                        <div class="pt-3 border-t border-[#E8E4DA]">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold text-[#161616] uppercase">
                                    Foto Cluster Tersimpan ({{ count($cluster->photos) }} Foto)
                                </label>
                                <span class="text-[11px] text-[#991B1B]">Centang kotak merah di foto untuk menghapusnya</span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach ($cluster->photos as $idx => $photoPath)
                                    @php
                                        $url = \Illuminate\Support\Facades\Storage::disk('public')->exists($photoPath)
                                            ? \Illuminate\Support\Facades\Storage::url($photoPath)
                                            : asset($photoPath);
                                    @endphp
                                    <div class="relative group rounded-xl border border-[#E8E4DA] overflow-hidden bg-[#161616]">
                                        <img src="{{ $url }}" alt="Foto Cluster {{ $idx + 1 }}" class="w-full h-28 object-cover">
                                        <label class="absolute bottom-2 left-2 right-2 flex items-center justify-center gap-1.5 px-2 py-1 rounded bg-[#161616]/90 text-white text-[11px] cursor-pointer hover:bg-[#991B1B] transition-colors border border-white/20">
                                            <input type="checkbox" name="delete_photos[]" value="{{ $photoPath }}" class="rounded text-[#991B1B] focus:ring-[#991B1B]">
                                            <span>Hapus Foto</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Multiple Photos Upload (New Photos) -->
                    <div class="pt-3 border-t border-[#E8E4DA]">
                        <label class="block text-xs font-semibold text-[#161616] uppercase mb-1">
                            Tambah Foto Baru ke Galeri Cluster
                        </label>
                        <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/jpg,image/webp"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-[#E8E4DA] bg-white text-[#161616] focus:outline-none focus:border-[#B89B5E] file:mr-4 file:py-1.5 file:px-3.5 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-[#B89B5E] file:text-white hover:file:bg-[#A3884E]">
                        <div class="flex items-center gap-2 mt-1.5 text-[11px] text-[#79766F]">
                            <i class="fa-solid fa-images text-[#B89B5E]"></i>
                            <span>Bisa pilih banyak foto sekaligus. Format: JPG, PNG, WEBP (Maks. 5MB per file).</span>
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
                <x-button type="submit" variant="gold" icon="fa-solid fa-floppy-disk">
                    Simpan Perubahan Cluster
                </x-button>
            </div>
        </form>
    </div>
</x-layouts.app>
