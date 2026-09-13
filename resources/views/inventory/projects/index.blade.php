<x-layouts.app title="Daftar Proyek Properti">
    <x-page-header
        title="Proyek Properti & Masterplan"
        subtitle="Kelola proyek kawasan, master developer, dan masterplan perumahan.">
        @if (auth()->user()->isAdminProperty() || auth()->user()->isCompanyOwner() || auth()->user()->isSuperAdmin())
            <x-button variant="gold" icon="fa-solid fa-plus" :href="route('inventory.projects.create')">
                Tambah Proyek Baru
            </x-button>
        @endif
    </x-page-header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($projects as $project)
            <div class="bg-white rounded-xl border border-[#E8E4DA] shadow-sm overflow-hidden flex flex-col hover:border-[#B89B5E] transition-all">
                <!-- Project Photo Cover -->
                <div class="relative h-48 w-full overflow-hidden bg-[#161616]">
                    <img src="{{ $project->image_url }}" alt="{{ $project->name }}" class="w-full h-full object-cover">
                    <div class="absolute top-3 left-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $project->status->badgeClass() }}">
                            {{ $project->status->label() }}
                        </span>
                    </div>
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#161616]/80 text-white border border-white/20">
                            <i class="fa-solid fa-location-dot text-[#B89B5E] mr-1"></i>{{ $project->city }}
                        </span>
                    </div>
                </div>

                <!-- Project Body -->
                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <a href="{{ route('inventory.projects.show', $project) }}" class="text-base font-bold text-[#161616] hover:text-[#B89B5E] transition-colors">
                                    {{ $project->name }}
                                </a>
                                <p class="text-xs text-[#79766F] mt-0.5">{{ $project->developer_name ?? 'Developer' }}</p>
                            </div>
                        </div>

                        <p class="text-xs text-[#79766F] line-clamp-2 leading-relaxed mt-2.5">
                            {{ $project->description ?? 'Kawasan hunian terpadu bernilai investasi tinggi dengan fasilitas lengkap.' }}
                        </p>
                    </div>

                    <div>
                        <div class="grid grid-cols-2 gap-2 pt-3 border-t border-[#E8E4DA]">
                            <div class="p-2.5 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                                <span class="text-[10px] text-[#79766F] uppercase font-bold block">Cluster</span>
                                <span class="text-sm font-bold text-[#161616]">{{ $project->clusters_count }} Cluster</span>
                            </div>

                            <div class="p-2.5 rounded-lg bg-[#F7F6F2] border border-[#E8E4DA]">
                                <span class="text-[10px] text-[#79766F] uppercase font-bold block">Total Unit</span>
                                <span class="text-sm font-bold text-[#161616]">{{ $project->property_units_count }} Unit</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 mt-3 border-t border-[#E8E4DA]">
                            <a href="{{ route('inventory.projects.show', $project) }}" class="text-xs font-semibold text-[#161616] hover:text-[#B89B5E] flex items-center">
                                Detail Proyek <i class="fa-solid fa-arrow-right ml-1 text-[10px]"></i>
                            </a>

                            <div class="flex items-center gap-3">
                                @if (auth()->user()->isAdminProperty() || auth()->user()->isCompanyOwner() || auth()->user()->isSuperAdmin())
                                    <a href="{{ route('inventory.projects.edit', $project) }}" class="text-xs font-medium text-[#79766F] hover:text-[#161616] flex items-center" title="Edit Proyek">
                                        <i class="fa-solid fa-pen-to-square mr-1 text-[11px]"></i> Edit
                                    </a>
                                @endif

                                <a href="{{ route('inventory.siteplan.show', $project) }}" class="text-xs font-bold text-[#B89B5E] hover:underline flex items-center">
                                    <i class="fa-solid fa-map-location-dot mr-1"></i> Siteplan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3">
                <x-empty-state
                    icon="fa-solid fa-city"
                    title="Belum ada proyek terdaftar"
                    message="Proyek perumahan yang ditambahkan akan muncul di sini." />
            </div>
        @endforelse
    </div>
</x-layouts.app>
