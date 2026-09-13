<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Enums\LeadTemperature;
use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, HasUuid, BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id',
        'assigned_sales_id',
        'code',
        'name',
        'phone',
        'email',
        'source',
        'campaign',
        'budget_min',
        'budget_max',
        'interested_project_id',
        'property_type_interest',
        'status',
        'temperature',
        'score',
        'purchase_target_days',
        'lost_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'score' => 'integer',
            'purchase_target_days' => 'integer',
            'source' => LeadSource::class,
            'status' => LeadStatus::class,
            'temperature' => LeadTemperature::class,
        ];
    }

    public function assignedSales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_sales_id');
    }

    public function interestedProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'interested_project_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class, 'lead_id')->orderBy('activity_date', 'desc');
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class, 'lead_id')->orderBy('visit_date', 'desc');
    }

    public function negotiations(): HasMany
    {
        return $this->hasMany(Negotiation::class, 'lead_id')->orderBy('created_at', 'desc');
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'lead_id');
    }

    public function scopeAssignedTo(Builder $query, string $userId): Builder
    {
        return $query->where('assigned_sales_id', $userId);
    }

    public function scopeHot(Builder $query): Builder
    {
        return $query->where('temperature', LeadTemperature::HOT);
    }
}
