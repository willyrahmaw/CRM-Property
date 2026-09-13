<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'timezone',
        'avatar_path',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    /**
     * User's company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Leads assigned to this sales user.
     */
    public function assignedLeads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_sales_id');
    }

    /**
     * Helper methods for role checking.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SUPER_ADMIN;
    }

    public function isCompanyOwner(): bool
    {
        return $this->role === UserRole::COMPANY_OWNER;
    }

    public function isSalesManager(): bool
    {
        return $this->role === UserRole::SALES_MANAGER;
    }

    public function isTeamLeader(): bool
    {
        return $this->role === UserRole::TEAM_LEADER;
    }

    public function isSalesAgent(): bool
    {
        return $this->role === UserRole::SALES_AGENT;
    }

    public function isFinance(): bool
    {
        return $this->role === UserRole::FINANCE;
    }

    public function isAdminProperty(): bool
    {
        return $this->role === UserRole::ADMIN_PROPERTY;
    }

    public function isManagerial(): bool
    {
        return $this->role->isManagerial();
    }

    public function isSales(): bool
    {
        return $this->role->isSales();
    }

    public function hasRole(UserRole|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    /**
     * Check if current user has authority to manage/toggle status of target user.
     */
    public function canManageUser(User $targetUser): bool
    {
        // Cannot manage oneself
        if ($this->id === $targetUser->id) {
            return false;
        }

        // Multi-tenant check: must belong to the same company (unless super admin)
        if (! $this->isSuperAdmin() && $this->company_id !== $targetUser->company_id) {
            return false;
        }

        // Super Admin can manage anyone else
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Company Owner can manage anyone in their company except Super Admin and other Owners
        if ($this->isCompanyOwner()) {
            return ! in_array($targetUser->role, [UserRole::SUPER_ADMIN, UserRole::COMPANY_OWNER]);
        }

        // Sales Manager can ONLY manage subordinates in the sales team (Team Leader & Sales Agent)
        // Sales Manager CANNOT manage Owner, Super Admin, Finance, Admin Property, or fellow Sales Managers
        if ($this->isSalesManager()) {
            return in_array($targetUser->role, [UserRole::TEAM_LEADER, UserRole::SALES_AGENT]);
        }

        return false;
    }

    /**
     * Get the public URL for the user's avatar.
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar_path)) {
            return asset('storage/' . $this->avatar_path);
        }

        return null;
    }

    /**
     * Get user initials for default avatar.
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }

        return $initials ?: 'U';
    }

    /**
     * Get the active Indonesian timezone enum instance.
     * When user belongs to a company (PT), the PT's timezone is authoritative for all staff.
     */
    public function getTimezoneEnum(): \App\Enums\IndonesianTimezone
    {
        if ($this->company && $this->company->timezone) {
            return \App\Enums\IndonesianTimezone::fromOrDefault($this->company->timezone);
        }

        return \App\Enums\IndonesianTimezone::fromOrDefault($this->timezone);
    }

    /**
     * Get the short Indonesian timezone abbreviation (WIB, WITA, or WIT).
     */
    public function getTimezoneCode(): string
    {
        return $this->getTimezoneEnum()->code();
    }
}
