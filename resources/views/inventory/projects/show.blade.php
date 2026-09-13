<x-layouts.app :title="$project->name . ' — Detail Proyek'">
    <x-page-header
        :title="$project->name"
        :subtitle="$project->developer_name . ' • ' . $project->city"
        :breadcrumbs="[
            ['label' => 'Inventori', 'url' => route('inventory.projects.index')],
            ['label' => 'Proyek Properti', 'url' => route('inventory.projects.index')],
            ['label' => $project->name, 'url' => '#'],
        ]">
        <div class="flex items-center gap-2">
            <x-button variant="outline" icon="fa-solid fa-arrow-left" :href="route('inventory.projects.index')">
                Kembali
            </x-button>
            <x-button variant="gold" icon="fa-solid fa-map-location-dot" :href="route('inventory.siteplan.show', $project)">
                Interactive Siteplan
            </x-button>

            @if (auth()->user()->isAdminProperty() || auth()->user()->isCompanyOwner() || auth()->user()->isSuperAdmin())
                <x-button variant="secondary" icon="fa-solid fa-pen-to-square" :href="route('inventory.projects.edit', $project)">
                    Edit Proyek
                </x-button>

                <form action="{{ route('inventory.projects.destroy', $project) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            data-confirm-delete
                            data-title="Hapus Proyek Kawasan?"
                            data-text="Proyek {{ $project->name }} akan dihapus dari sistem. Pastikan tidak ada transaksi aktif yang bergantung pada proyek ini."
                            data-confirm-text="Ya, Hapus Proyek"
                            class="inline-flex items-center px-3 py-2 rounded-lg border border-[#FCA5A5] bg-white text-[#991B1B] hover:bg-[#FEE2E2] text-xs font-semibold transition-colors">
                        <i class="fa-solid fa-trash-can mr-1.5"></i> Hapus
                    </button>
                </form>
            @endif
        </div>
    </x-page-header>

    <div class="space-y-6">
        <!-- Hero Photo & Quick Highlight Banner -->
        <div class="bg-white rounded-xl border border-[#E8E4DA] overflow-hidden shadow-sm">
            <div class="relative h-72 sm:h-96 w-full bg-[#161616] overflow-hidden">
                <img src="{{ $project->image_url }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold {{ $project->status->badgeClass() }} shadow-sm">
                        {{ $project->status->label() }}
                    </span>
                    <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-[#161616]/90 text-white border border-white/20 shadow-sm">
                        <i class="fa-solid fa-city text-[#B89B5E] mr-1.5"></i>Master Development
                    </span>
                </div>
                <div class="absolute bottom-4 left-4 right-4 bg-[#161616]/90 backdrop-blur-sm border border-white/10 p-4 rounded-xl text-white flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold">{{ $project->name }}</h2>
                        <p class="text-xs text-[#E8E4DA] mt-0.5">
                            <i class="fa-solid fa-location-dot text-[#B89B5E] mr-1"></i>{{ $project->address ?? $project->city }}
                        </p>
                    </div>
                    <div class="flex items-center gap-6 text-xs">
                        <div>
                            <span class="text-[#79766F] text-[10px] block uppercase font-bold">Total Cluster</span>
                            <span class="font-bold text-white text-base">{{ $project->clusters->count() }} Cluster</span>
                        </div>
                        <div class="w-px h-8 bg-white/10"></div>
                        <div>
                            <span class="text-[#79766F] text-[10px] block uppercase font-bold">Total Unit</span>
                            <span class="font-bold text-white text-base">{{ $project->clusters->sum(fn($c) => $c->propertyUnits->count()) }} Unit</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description & Facilities -->
            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-[#161616] uppercase tracking-wider mb-2">Tentang Kawasan Proyek</h3>
                        <p class="text-sm text-[#79766F] leading-relaxed">
                            {{ $project->description ?? 'Kawasan perumahan premium yang dikembangkan dengan standar arsitektur modern berkelas tinggi, fasilitas lengkap ramah keluarga, serta akses infrastruktur prima.' }}
                        </p>
                    </div>

                    @if(!empty($project->facilities))
                        <div class="pt-4 border-t border-[#E8E4DA]">
                            <h3 class="text-xs font-bold text-[#161616] uppercase tracking-wider mb-3">Fasilitas Kawasan Unggulan</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($project->facilities as $facility)
                                    <span class="px-3 py-1.5 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA] text-xs font-medium text-[#161616] flex items-center">
                                        <i class="fa-solid fa-check text-[#B89B5E] text-xs mr-2"></i>{{ $facility }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-4 p-5 bg-[#F7F6F2] rounded-xl border border-[#E8E4DA]">
                    <h3 class="text-xs font-bold text-[#161616] uppercase tracking-wider">Ringkasan Kawasan</h3>
                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                            <span class="text-[#79766F]">Developer</span>
                            <span class="font-bold text-[#161616]">{{ $project->developer_name ?? 'Developer' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                            <span class="text-[#79766F]">Kota / Lokasi</span>
                            <span class="font-bold text-[#161616]">{{ $project->city }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-[#E8E4DA]">
                            <span class="text-[#79766F]">Status Proyek</span>
                            <span class="font-bold {{ $project->status->badgeClass() }} px-2 py-0.5 rounded text-[10px]">
                                {{ $project->status->label() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clusters & Property Types -->
        <!-- Clusters & Property Types -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-[#161616]">Daftar Cluster dalam Kawasan</h3>
                @if(auth()->user()->isAdminProperty() || auth()->user()->isCompanyOwner() || auth()->user()->isSuperAdmin())
                    <a href="{{ route('inventory.clusters.create', ['project_id' => $project->id]) }}" class="px-3 py-1.5 bg-[#B89B5E] text-white rounded-lg text-xs font-bold hover:bg-[#A3874B] transition-colors flex items-center space-x-1">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Tambah Cluster</span>
                    </a>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($project->clusters as $cluster)
                    <div class="bg-white rounded-xl border border-[#E8E4DA] overflow-hidden shadow-sm flex flex-col justify-between">
                        <div>
                            @if(!empty($cluster->photos) && count($cluster->photos) > 0)
                                <div class="relative h-40 w-full bg-[#161616] overflow-hidden group">
                                    <img src="{{ $cluster->cover_url }}" alt="{{ $cluster->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <span class="absolute bottom-2 right-2 bg-[#161616]/80 text-white text-[10px] font-bold px-2 py-0.5 rounded backdrop-blur-sm flex items-center gap-1">
                                        <i class="fa-solid fa-camera"></i> {{ count($cluster->photos) }} Foto
                                    </span>
                                </div>
                            @endif
                            <div class="p-5 space-y-3">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#F7F6F2] text-[#79766F] border border-[#E8E4DA]">
                                            KODE: {{ $cluster->code ?? 'CLS' }}
                                        </span>
                                        <h4 class="text-base font-bold text-[#161616] mt-2">{{ $cluster->name }}</h4>
                                    </div>
                                    <span class="text-xs font-bold px-2 py-1 rounded bg-[#E8E4DA] text-[#161616]">
                                        {{ $cluster->propertyUnits->count() }} Unit
                                    </span>
                                </div>

                                <p class="text-xs text-[#79766F] line-clamp-2">
                                    {{ $cluster->description ?? 'Cluster hunian eksklusif dengan kaveling siap bangun.' }}
                                </p>
                            </div>
                        </div>

                        <div class="p-5 pt-0">
                            <div class="pt-3 border-t border-[#E8E4DA] flex items-center justify-between text-xs">
                                <span class="text-[#15803D] font-bold">
                                    {{ $cluster->propertyUnits->where('status', \App\Enums\PropertyUnitStatus::AVAILABLE)->count() }} Unit Tersedia
                                </span>
                                <div class="flex items-center space-x-2">
                                    @if(auth()->user()->isAdminProperty() || auth()->user()->isCompanyOwner() || auth()->user()->isSuperAdmin())
                                        <a href="{{ route('inventory.clusters.edit', $cluster) }}" class="text-[#79766F] hover:text-[#161616] font-bold p-1" title="Edit Cluster & Foto">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('inventory.units.index', ['cluster_id' => $cluster->id]) }}" class="text-[#B89B5E] font-bold hover:underline flex items-center">
                                        Lihat Unit <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3">
                        <x-empty-state icon="fa-solid fa-layer-group" title="Belum Ada Cluster" message="Cluster perumahan belum terdaftar untuk proyek ini." />
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
