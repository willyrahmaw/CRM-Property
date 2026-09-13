<x-layouts.app title="Komisi Penjualan">
    <x-page-header
        title="Distribusi Komisi Penjualan"
        subtitle="Perhitungan transparan komisi berjenjang untuk Sales Agent, Team Leader, dan Agency.">
        @if (auth()->user()->isCompanyOwner() || auth()->user()->isSuperAdmin())
            <x-button variant="outline" icon="fa-solid fa-sliders" :href="route('settings.commissions.index')">
                Pengaturan Skema Komisi
            </x-button>
        @endif
    </x-page-header>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <x-stat-card
            title="Total Komisi Pending"
            value="Rp {{ number_format($totalPending, 0, ',', '.') }}"
            icon="fa-solid fa-clock"
            helper="menunggu approval finance" />

        <x-stat-card
            title="Total Komisi Siap Cair (Approved)"
            value="Rp {{ number_format($totalApproved, 0, ',', '.') }}"
            icon="fa-solid fa-circle-check"
            changeType="positive"
            helper="dapat dicairkan segera" />

        <x-stat-card
            title="Total Komisi Sudah Dibayarkan"
            value="Rp {{ number_format($totalPaid, 0, ',', '.') }}"
            icon="fa-solid fa-coins"
            helper="akumulasi tahun ini" />
    </div>

    <x-card :padding="false">
        @if ($commissions->isEmpty())
            <x-empty-state
                icon="fa-solid fa-coins"
                title="Belum ada komisi yang dihitung"
                message="Komisi akan ter-generate otomatis saat booking telah lunas atau disetujui." />
        @else
            <x-table>
                <thead>
                    <tr>
                        <th>Penerima Komisi</th>
                        <th>Kategori</th>
                        <th>Booking Terkait</th>
                        <th>Harga Unit</th>
                        <th>Rate (%)</th>
                        <th>Nominal Komisi</th>
                        <th>Status</th>
                        <th class="action-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($commissions as $comm)
                        <tr>
                            <td>
                                <span class="font-bold text-[#161616]">{{ $comm->user->name }}</span>
                                @if ($comm->user->bank_account_number)
                                    <span class="text-[10px] text-[#79766F] block mt-0.5">
                                        <i class="fa-solid fa-credit-card text-[9px] mr-1 text-[#B89B5E]"></i>
                                        {{ $comm->user->bank_name }} {{ $comm->user->bank_account_number }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="px-2.5 py-1 rounded bg-[#F7F6F2] border border-[#E8E4DA] text-[#161616] text-[10px] font-semibold uppercase">
                                    {{ str_replace('_', ' ', $comm->beneficiary_type) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('sales.bookings.show', $comm->booking) }}" class="font-semibold text-[#161616] hover:text-[#B89B5E] transition-colors">
                                    {{ $comm->booking->booking_number }}
                                </a>
                                <span class="text-[11px] text-[#79766F] block">Unit {{ $comm->booking->propertyUnit->unit_number }}</span>
                            </td>
                            <td class="font-medium text-[#161616]">
                                Rp {{ number_format($comm->selling_price, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="font-bold text-[#161616]">{{ $comm->percentage }}%</span>
                            </td>
                            <td>
                                <span class="font-bold text-[#15803D]">Rp {{ number_format($comm->amount, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $comm->status->badgeClass() }}">
                                    {{ $comm->status->label() }}
                                </span>
                            </td>
                            <td class="action-col space-x-1">
                                @if ($comm->status === \App\Enums\CommissionStatus::PENDING && (auth()->user()->isFinance() || auth()->user()->isManagerial()))
                                    <form action="{{ route('finance.commissions.approve', $comm) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                data-confirm-action
                                                data-title="Setujui Komisi?"
                                                data-text="Komisi ini akan berstatus Approved dan siap dicairkan."
                                                data-confirm-text="Ya, Setujui"
                                                class="px-2.5 py-1 rounded-lg bg-[#B89B5E] hover:bg-[#A3884E] text-white text-xs font-semibold transition-colors">
                                            Approve
                                        </button>
                                    </form>
                                @endif

                                @if ($comm->status === \App\Enums\CommissionStatus::APPROVED && auth()->user()->isFinance())
                                    <form action="{{ route('finance.commissions.pay', $comm) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                data-confirm-action
                                                data-title="Tandai Sudah Dibayar?"
                                                data-text="Dana komisi telah berhasil ditransfer ke rekening sales."
                                                data-confirm-text="Ya, Tandai Dibayar"
                                                class="px-2.5 py-1 rounded-lg bg-[#15803D] hover:bg-[#166534] text-white text-xs font-semibold transition-colors">
                                            Cairkan
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>

            @if ($commissions->hasPages())
                <div class="prop-table-pagination">
                    {{ $commissions->links() }}
                </div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
