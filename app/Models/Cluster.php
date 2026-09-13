<?php

namespace App\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cluster extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'code',
        'description',
        'photos',
    ];

    protected function casts(): array
    {
        return [
            'photos' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function propertyUnits(): HasMany
    {
        return $this->hasMany(PropertyUnit::class, 'cluster_id');
    }

    /**
     * Get resolved URLs for all cluster photos.
     *
     * @return array<int, string>
     */
    public function getPhotoUrlsAttribute(): array
    {
        if (empty($this->photos) || !is_array($this->photos)) {
            return [];
        }

        return array_map(function ($photo) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($photo)) {
                return \Illuminate\Support\Facades\Storage::url($photo);
            }
            if (file_exists(public_path($photo))) {
                return asset($photo);
            }
            return asset($photo);
        }, $this->photos);
    }

    /**
     * Get the primary cover photo URL (first photo or fallback).
     */
    public function getCoverUrlAttribute(): string
    {
        $urls = $this->photo_urls;
        if (!empty($urls)) {
            return $urls[0];
        }

        // Fallback to project image or default asset
        if ($this->project && $this->project->image_url) {
            return $this->project->image_url;
        }

        return asset('images/properties/project_grand_harmony.jpg');
    }
}

