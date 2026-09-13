<?php

namespace App\Models;

use App\Enums\PropertyUnitStatus;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyUnit extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'cluster_id',
        'property_type_id',
        'unit_number',
        'block',
        'land_area',
        'dimension',
        'building_area',
        'bedrooms',
        'bathrooms',
        'floors',
        'carports',
        'direction',
        'electricity',
        'water_source',
        'certificate_type',
        'building_specs',
        'base_price',
        'selling_price',
        'status',
        'image',
        'siteplan_coordinates',
        'promo',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'land_area' => 'decimal:2',
            'building_area' => 'decimal:2',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'floors' => 'integer',
            'carports' => 'integer',
            'building_specs' => 'array',
            'base_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'status' => PropertyUnitStatus::class,
            'siteplan_coordinates' => 'array',
        ];
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class, 'cluster_id');
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class, 'property_type_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'property_unit_id');
    }

    public function negotiations(): HasMany
    {
        return $this->hasMany(Negotiation::class, 'property_unit_id');
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class, 'property_unit_id');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', PropertyUnitStatus::AVAILABLE);
    }

    public function isAvailable(): bool
    {
        return $this->status === PropertyUnitStatus::AVAILABLE;
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
            return \Illuminate\Support\Facades\Storage::url($this->image);
        }
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }
        if ($this->propertyType && $this->propertyType->image) {
            return $this->propertyType->image_url;
        }
        return asset('images/properties/unit_townhouse.jpg');
    }
}
