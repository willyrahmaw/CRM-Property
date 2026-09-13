<x-layouts.app title="Verifikasi Pembayaran">
    <x-page-header
        title="Pembayaran & Verifikasi Kwitansi"
        subtitle="Verifikasi bukti transfer konsumen, pencatatan termin angsuran, dan pembukuan tanda jadi.">
    </x-page-header>

    <x-card :padding="false">
        @if ($payments->isEmpty())
            <x-empty-state
                icon="fa-solid fa-receipt"
                title="Belum ada transaksi pembayaran"
                message="Setiap bukti transfer booking fee atau cicilan yang diinput sales/konsumen akan muncul di sini untuk verifikasi Finance." />
        @else
            <x-table>
                <thead>
                    <tr>
                        <th>No. Kwitansi & Tgl</th>
                        <th>Konsumen & Unit</th>
                        <th>Jenis Transaksi</th>
                        <th>Metode Bayar</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th class="action-col">Verifikasi Finance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>
                                <span class="font-bold text-[#161616] block">{{ $payment->payment_number }}</span>
                                <span class="text-[11px] text-[#79766F]">{{ $payment->payment_date->translatedFormat('d M Y') }}</span>
                            </td>
                            <td>
                                <span class="font-semibold text-[#161616] block">{{ $payment->booking->customer->name }}</span>
                                <span class="text-[11px] text-[#79766F]">
                                    Unit {{ $payment->booking->propertyUnit->unit_number }} ({{ $payment->booking->booking_number }})
                                </span>
                            </td>
                            <td>
                                <span class="px-2.5 py-1 rounded bg-[#F7F6F2] border border-[#E8E4DA] text-[#161616] text-[10px] font-medium">
                                    {{ $payment->payment_type->label() }}
                                </span>
                            </td>
                            <td class="text-[#161616] text-xs">
                                {{ $payment->payment_method }}
                            </td>
                            <td>
                                <span class="font-bold text-[#15803D]">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $payment->status->badgeClass() }}">
                                    {{ $payment->status->label() }}
                                </span>
                            </td>
                            <td class="action-col">
                                @if ($payment->status === \App\Enums\PaymentStatus::PENDING)
                                    <form action="{{ route('finance.payments.verify', $payment) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                data-confirm-action
                                                data-title="Verifikasi Pembayaran?"
                                                data-text="Dana telah dipastikan masuk ke rekening perusahaan."
                                                data-confirm-text="Ya, Verifikasi"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg bg-[#15803D] hover:bg-[#166534] text-white font-semibold text-xs transition-colors">
                                            <i class="fa-solid fa-check mr-1.5"></i> Verifikasi Dana
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-[#79766F]">
                                        Diverifikasi oleh {{ $payment->verifier?->name ?? 'Finance' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>

            @if ($payments->hasPages())
                <div class="prop-table-pagination">
                    {{ $payments->links() }}
                </div>
            @endif
        @endif
    </x-card>
</x-layouts.app>
