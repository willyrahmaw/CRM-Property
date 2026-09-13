<?php

namespace App\Models;

use App\Enums\NegotiationApprovalStatus;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Negotiation extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'lead_id',
        'property_unit_id',
        'sales_id',
        'initial_price',
        'customer_offer_price',
        'final_price',
        'discount_amount',
        'discount_percentage',
        'promo_description',
        'approval_status',
        'approved_by_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'initial_price' => 'decimal:2',
            'customer_offer_price' => 'decimal:2',
            'final_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'approval_status' => NegotiationApprovalStatus::class,
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
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

    public function isPending(): bool
    {
        return $this->approval_status === NegotiationApprovalStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->approval_status === NegotiationApprovalStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->approval_status === NegotiationApprovalStatus::REJECTED;
    }
}
