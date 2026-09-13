<?php

namespace App\Models;

use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, HasUuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'address',
        'logo_path',
        'website_settings',
        'commission_settings',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'website_settings' => 'array',
            'commission_settings' => 'array',
        ];
    }

    /**
     * Retrieve a specific website setting with optional default.
     */
    public function getWebsiteSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->website_settings ?? [];
        return data_get($settings, $key, $default);
    }

    /**
     * Retrieve a specific commission setting with optional default.
     */
    public function getCommissionSetting(string $key, mixed $default = null): mixed
    {
        $settings = $this->commission_settings ?? [];
        return data_get($settings, $key, $default);
    }

    /**
     * Logo URL helper.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->logo_path)) {
            return \Illuminate\Support\Facades\Storage::url($this->logo_path);
        }
        if ($this->logo_path && file_exists(public_path($this->logo_path))) {
            return asset($this->logo_path);
        }
        return null;
    }

    /**
     * Company users.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'company_id');
    }
}
