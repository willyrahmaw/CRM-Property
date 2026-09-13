<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, HasUuid, BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'developer_name',
        'address',
        'city',
        'latitude',
        'longitude',
        'description',
        'facilities',
        'image',
        'siteplan_image',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'facilities' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'status' => ProjectStatus::class,
        ];
    }

    public function clusters(): HasMany
    {
        return $this->hasMany(Cluster::class, 'project_id');
    }

    public function propertyTypes(): HasMany
    {
        return $this->hasMany(PropertyType::class, 'project_id');
    }

    public function propertyUnits(): HasManyThrough
    {
        return $this->hasManyThrough(PropertyUnit::class, Cluster::class, 'project_id', 'cluster_id');
    }

    public function siteVisits(): HasMany
    {
        return $this->hasMany(SiteVisit::class, 'project_id');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->image)) {
            return \Illuminate\Support\Facades\Storage::url($this->image);
        }
        if ($this->image && file_exists(public_path($this->image))) {
            return asset($this->image);
        }
        return asset('images/properties/project_grand_harmony.jpg');
    }
}
