<?php

namespace App\Models;

use App\Enums\SiteVisitStatus;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteVisit extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'lead_id',
        'user_id',
        'project_id',
        'property_unit_id',
        'visit_date',
        'status',
        'result',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'datetime',
            'status' => SiteVisitStatus::class,
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function propertyUnit(): BelongsTo
    {
        return $this->belongsTo(PropertyUnit::class, 'property_unit_id');
    }
}
