<?php

namespace App\Http\Controllers\Sales;

use App\Enums\BookingStatus;
use App\Enums\PaymentScheme;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\PropertyUnit;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function index(Request $request): View
    {
        $query = Booking::with(['customer', 'propertyUnit.cluster.project', 'sales'])->latest('booking_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%"))
                    ->orWhereHas('propertyUnit', fn ($uq) => $uq->where('unit_number', 'like', "%{$search}%"));
            });
        }

        $bookings = $query->paginate(15)->withQueryString();

        $metrics = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', BookingStatus::PENDING)->count(),
            'approved' => Booking::where('status', BookingStatus::APPROVED)->count(),
            'total_value' => Booking::whereIn('status', [BookingStatus::PENDING, BookingStatus::APPROVED])->sum('final_price'),
        ];

        return view('sales.bookings.index', [
            'bookings' => $bookings,
            'statuses' => BookingStatus::cases(),
            'currentStatus' => $request->status,
            'filters' => $request->only(['search', 'status']),
            'metrics' => $metrics,
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $availableUnits = PropertyUnit::available()->with(['cluster.project', 'propertyType'])->get();
        $customers = Customer::where('company_id', $user->company_id)->orderBy('name')->get();
        $leads = \App\Models\Lead::where('company_id', $user->company_id)
            ->whereNotIn('status', [\App\Enums\LeadStatus::LOST, \App\Enums\LeadStatus::WON])
            ->orderBy('name')
            ->get();

        $selectedUnitId = $request->query('unit_id');
        $selectedCustomerId = $request->query('customer_id');
        $selectedLeadId = $request->query('lead_id');

        return view('sales.bookings.create', [
            'availableUnits' => $availableUnits,
            'customers' => $customers,
            'leads' => $leads,
            'selectedUnitId' => $selectedUnitId,
            'selectedCustomerId' => $selectedCustomerId,
            'selectedLeadId' => $selectedLeadId,
            'schemes' => PaymentScheme::cases(),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Resolve customer from existing customer, existing lead, or create new
        if ($request->filled('customer_id')) {
            $customer = Customer::where('company_id', $user->company_id)->findOrFail($request->customer_id);
        } elseif ($request->filled('lead_id')) {
            $lead = \App\Models\Lead::where('company_id', $user->company_id)->findOrFail($request->lead_id);
            $customer = Customer::firstOrCreate(
                ['lead_id' => $lead->id],
                [
                    'company_id' => $user->company_id,
                    'name' => $request->customer_name ?: $lead->name,
                    'phone' => $request->customer_phone ?: $lead->phone,
                    'email' => $request->customer_email ?: $lead->email,
                    'nik' => $request->customer_nik,
                ]
            );
        } else {
            $customer = Customer::create([
                'company_id' => $user->company_id,
                'lead_id' => null,
                'nik' => $request->customer_nik,
                'name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'email' => $request->customer_email,
            ]);
        }

        $booking = $this->bookingService->createBooking(
            customer: $customer,
            unitId: $request->property_unit_id,
            sales: $user,
            bookingFee: (float) $request->booking_fee,
            discountAmount: (float) ($request->discount_amount ?? 0),
            paymentScheme: PaymentScheme::from($request->payment_scheme),
            notes: $request->notes
        );

        return redirect()
            ->route('sales.bookings.show', $booking)
            ->with('success', "Booking dengan nomor {$booking->booking_number} berhasil dibuat dan menunggu verifikasi.");
    }

    public function show(Booking $booking): View
    {
        $booking->load(['customer', 'propertyUnit.cluster.project', 'sales', 'payments.verifier', 'mortgage', 'commissions.user', 'commissions.approver']);

        return view('sales.bookings.show', [
            'booking' => $booking,
        ]);
    }

    public function approve(Request $request, Booking $booking): RedirectResponse
    {
        $this->bookingService->approveBooking($booking, $request->user());

        return back()->with('success', "Booking {$booking->booking_number} berhasil disetujui. Unit resmi berstatus BOOKED.");
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate(['reason' => ['required', 'string', 'max:255']]);

        $this->bookingService->cancelBooking($booking, $request->reason, $request->user());

        return back()->with('success', "Booking {$booking->booking_number} telah dibatalkan dan unit telah dilepas kembali menjadi AVAILABLE.");
    }
}
