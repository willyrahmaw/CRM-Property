<x-layouts.app title="Daftar Cluster Kawasan">
    <x-page-header
        title="Manajemen Cluster & Kawasan"
        subtitle="Kelola zonasi cluster perumahan, kaveling unit, dan integrasi masterplan proyek.">
        @if (auth()->user()->isAdminProperty() || auth()->user()->isCompanyOwner() || auth()->user()->isSuperAdmin())
            <x-button variant="gold" icon="fa-solid fa-plus" :href="route('inventory.clusters.create')">
                Tambah Cluster Baru
            </x-button>
        @endif
        <x-button variant="primary" icon="fa-solid fa-map-location-dot" :href="route('inventory.siteplan.index')">
            Interactive Siteplan
        </x-button>
        <x-button variant="secondary" icon="fa-solid fa-house-chimney" :href="route('inventory.units.index')">
            Kelola Seluruh Unit
        </x-button>
    </x-page-header>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card
            title="Total Cluster Aktif"
            :value="($metrics['total_clusters'] ?? 0) . ' Cluster'"
            icon="fa-solid fa-sitemap"
            helper="terdaftar di seluruh proyek" />

        <x-stat-card
            title="Total Kaveling Unit"
            :value="($metrics['total_units'] ?? 0) . ' Unit'"
            icon="fa-solid fa-layer-group"
            helper="akumulasi kaveling bangunan" />

        <x-stat-card
            title="Unit Tersedia (Ready)"
            :value="($metrics['available_units'] ?? 0) . ' Unit'"
            icon="fa-solid fa-circle-check"
            changeType="positive"
            helper="siap dipasarkan sales" />

        <x-stat-card
            title="Terkunci & Terjual"
            :value="($metrics['sold_booked_units'] ?? 0) . ' Unit'"
            icon="fa-solid fa-handshake"
            changeType="positive"
            helper="tahap booking fee & akad" />
    </div>

    <!-- Filters Bar -->
    <x-card class="mb-6">
        <form action="{{ route('inventory.clusters.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-[#79766F] uppercase mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Nama cluster, kode..."
                       class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] text-[#161616] focus:bg-white focus:outline-none focus:border-[#B89B5E]">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-[#79766F] uppercase mb-1">Proyek Induk</label>
                <select name="project_id" class="w-full px-3 py-2 text-xs rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] text-[#161616] focus:bg-white focus:outline-none focus:border-[#B89B5E]">
                    <option value="">Semua Proyek</option>
                    @foreach ($projects as $prj)
                        <option value="{{ $prj->id }}" {{ ($filters['project_id'] ?? '') === $prj->id ? 'selected' : '' }}>
                            {{ $prj->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <x-button type="submit" variant="primary" size="md" class="w-full">
                    Filter
                </x-button>
                <a href="{{ route('inventory.clusters.index') }}" class="px-3 py-2 text-xs font-medium rounded-lg border border-[#E8E4DA] bg-[#F7F6F2] hover:bg-[#E8E4DA] text-[#161616] transition-colors" title="Reset Filter">
                    <i class="fa-solid fa-arrow-rotate-right"></i>
                </a>
            </div>
        </form>
    </x-card>

    <!-- Clusters Table -->
    <x-card :padding="false">
        @if ($clusters->isEmpty())
            <x-empty-state
                icon="fa-solid fa-sitemap"
                title="Belum ada cluster terdaftar"
                message="Data cluster properti belum ditemukan atau tidak sesuai filter pencarian." />
        @else
            <x-table>
                <thead>
                    <tr>
                        <th class="cell-fit">Cluster & Galeri Foto</th>
                        <th class="cell-wrap">Proyek Induk</th>
                        <th class="cell-fit">Total Unit</th>
                        <th class="cell-fit">Distribusi Status Unit</th>
                        <th class="cell-wrap">Deskripsi Kawasan</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clusters as $cluster)
                        <tr>
                            <td class="cell-fit">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-12 h-12 rounded-lg overflow-hidden border border-[#E8E4DA] bg-[#161616] shrink-0">
                                        <img src="{{ $cluster->cover_url }}" alt="{{ $cluster->name }}" class="w-full h-full object-cover">
                                        @if (!empty($cluster->photos) && count($cluster->photos) > 1)
                                            <span class="absolute bottom-0 right-0 px-1 rounded-tl bg-[#161616]/90 text-[9px] font-bold text-white">
                                                +{{ count($cluster->photos) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-bold text-[#161616] text-sm block">{{ $cluster->name }}</span>
                                        <span class="text-[10px] font-mono text-[#79766F] uppercase">Kode: {{ $cluster->code ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="cell-wrap">
                                <a href="{{ route('inventory.projects.show', $cluster->project) }}" class="font-semibold text-[#161616] hover:text-[#B89B5E] transition-colors block">
                                    {{ $cluster->project->name }}
                                </a>
                                <span class="text-[11px] text-[#79766F] block">{{ $cluster->project->city ?? 'Lokasi Proyek' }}</span>
                            </td>
                            <td class="cell-fit">
                                <span class="font-bold text-[#161616] text-sm">{{ $cluster->property_units_count }}</span>
                                <span class="text-[10px] text-[#79766F] block">Kaveling</span>
                            </td>
                            <td class="cell-fit">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-[#DCFCE7] text-[#166534] border border-[#BBF7D0]" title="Available / Siap Jual">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                                        {{ $cluster->available_units_count }} Ready
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A]" title="Booked / Dipesan">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#D97706]"></span>
                                        {{ $cluster->booked_units_count }} Booked
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-[#F3F4F6] text-[#4B5563] border border-[#E5E7EB]" title="Sold / Terjual">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#161616]"></span>
                                        {{ $cluster->sold_units_count }} Sold
                                    </span>
                                </div>
                            </td>
                            <td class="cell-wrap text-xs text-[#79766F]">
                                {{ $cluster->description ?? 'Tidak ada catatan deskripsi kawasan.' }}
                            </td>
                            <td class="action-col">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('inventory.units.index', ['cluster_id' => $cluster->id]) }}"
                                       class="inline-flex items-center px-2.5 py-1.5 rounded-lg border border-[#E8E4DA] bg-white hover:bg-[#F7F6F2] text-[#161616] font-medium text-xs transition-colors"
                                       title="Lihat unit kaveling cluster ini">
                                        <i class="fa-solid fa-house-chimney mr-1 text-xs text-[#79766F]"></i> Unit
                                    </a>

                                    <a href="{{ route('inventory.siteplan.show', $cluster->project_id) }}"
                                       class="inline-flex items-center px-2 py-1.5 rounded-lg bg-[#B89B5E] hover:bg-[#A3884E] text-white font-medium text-xs transition-colors"
                                       title="Buka denah siteplan interaktif">
                                        <i class="fa-solid fa-map-location-dot text-xs"></i>
                                    </a>

                                    @if (auth()->user()->isAdminProperty() || auth()->user()->isCompanyOwner() || auth()->user()->isSuperAdmin())
                                        <a href="{{ route('inventory.clusters.edit', $cluster) }}"
                                           class="inline-flex items-center px-2 py-1.5 rounded-lg border border-[#E8E4DA] bg-white hover:bg-[#F7F6F2] text-[#161616] text-xs font-medium transition-colors"
                                           title="Edit Cluster & Kelola Foto">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <form action="{{ route('inventory.clusters.destroy', $cluster) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    data-confirm-delete
                                                    data-title="Hapus Cluster?"
                                                    data-text="Cluster {{ $cluster->name }} akan dihapus. Pastikan tidak ada transaksi unit aktif di dalamnya."
                                                    data-confirm-text="Ya, Hapus"
                                                    class="inline-flex items-center px-2 py-1.5 rounded-lg border border-[#FCA5A5] bg-white hover:bg-[#FEE2E2] text-[#991B1B] text-xs font-semibold transition-colors"
                                                    title="Hapus Cluster">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>

            @if ($clusters->hasPages())
                <div class="prop-table-pagination">
                    {{ $clusters->links() }}
                </div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
