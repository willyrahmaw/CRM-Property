<?php

namespace App\Models;

use App\Enums\CommissionStatus;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'booking_id',
        'user_id',
        'beneficiary_type',
        'selling_price',
        'percentage',
        'amount',
        'status',
        'approved_by_id',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'percentage' => 'decimal:2',
            'amount' => 'decimal:2',
            'status' => CommissionStatus::class,
            'paid_at' => 'date',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }
}
