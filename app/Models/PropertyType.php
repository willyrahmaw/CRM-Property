<?php

namespace App\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyType extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'project_id',
        'name',
        'code',
        'building_area',
        'land_area',
        'bedrooms',
        'bathrooms',
        'floors',
        'image',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'building_area' => 'decimal:2',
            'land_area' => 'decimal:2',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'floors' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function propertyUnits(): HasMany
    {
        return $this->hasMany(PropertyUnit::class, 'property_type_id');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
            return \Illuminate\Support\Facades\Storage::url($this->image);
        }
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }
        return asset('images/properties/unit_villa_aster.jpg');
    }
}
