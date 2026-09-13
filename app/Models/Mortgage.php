<?php

namespace App\Models;

use App\Enums\MortgageStatus;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mortgage extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'booking_id',
        'bank_name',
        'submission_amount',
        'approved_amount',
        'tenor_years',
        'interest_rate',
        'estimated_installment',
        'status',
        'application_date',
        'appraisal_date',
        'sp3k_date',
        'contract_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'submission_amount' => 'decimal:2',
            'approved_amount' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'estimated_installment' => 'decimal:2',
            'tenor_years' => 'integer',
            'status' => MortgageStatus::class,
            'application_date' => 'date',
            'appraisal_date' => 'date',
            'sp3k_date' => 'date',
            'contract_date' => 'date',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
