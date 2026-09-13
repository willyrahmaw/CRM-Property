<?php

namespace App\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'lead_activities';

    protected $fillable = [
        'lead_id',
        'user_id',
        'activity_type',
        'activity_date',
        'result',
        'notes',
        'next_follow_up_date',
        'attachment_path',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'datetime',
            'next_follow_up_date' => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
