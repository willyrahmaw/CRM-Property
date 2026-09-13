<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\PaymentScheme;
use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory, HasUuid, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'customer_id',
        'property_unit_id',
        'sales_id',
        'booking_number',
        'booking_date',
        'booking_fee',
        'unit_price',
        'discount_amount',
        'final_price',
        'payment_scheme',
        'status',
        'approved_by_id',
        'cancellation_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'booking_fee' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_price' => 'decimal:2',
            'payment_scheme' => PaymentScheme::class,
            'status' => BookingStatus::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function propertyUnit(): BelongsTo
    {
        return $this->belongsTo(PropertyUnit::class, 'property_unit_id');
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    public function mortgage(): HasOne
    {
        return $this->hasOne(Mortgage::class, 'booking_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'booking_id');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::APPROVED);
    }
}
