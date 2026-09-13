<?php

namespace App\Http\Controllers\Finance;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\StorePaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    public function index(Request $request): View
    {
        $query = Payment::with(['booking.customer', 'booking.propertyUnit', 'verifier'])->latest('payment_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('payment_type', $request->type);
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('finance.payments.index', [
            'payments' => $payments,
            'statuses' => PaymentStatus::cases(),
            'types' => PaymentType::cases(),
            'filters' => $request->all(),
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $booking = Booking::findOrFail($request->booking_id);

        $payment = $this->paymentService->recordPayment(
            booking: $booking,
            type: PaymentType::from($request->payment_type),
            amount: (float) $request->amount,
            paymentDate: $request->payment_date,
            paymentMethod: $request->payment_method,
            referenceNumber: $request->reference_number,
            proofFile: $request->file('proof_file'),
            notes: $request->notes
        );

        return back()->with('success', "Bukti pembayaran {$payment->payment_number} berhasil dicatat dan menunggu verifikasi Finance.");
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        $this->paymentService->verifyPayment($payment, $request->user());

        return back()->with('success', "Pembayaran {$payment->payment_number} berhasil diverifikasi dan kwitansi diterbitkan.");
    }
}
