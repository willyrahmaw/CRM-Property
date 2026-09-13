<?php

namespace App\Support\Traits;

use App\Enums\UserRole;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Trait for models belonging to a company.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 *
 * @method static void creating(\Closure $callback)
 * @method static void addGlobalScope(string|\Closure $scope, ?\Closure $implementation = null)
 */
trait BelongsToCompany
{
    /**
     * Boot the trait to attach company_id and tenant scope.
     */
    protected static function bootBelongsToCompany(): void
    {
        static::creating(function ($model) {
            if (empty($model->company_id) && Auth::check() && Auth::user()->company_id) {
                $model->company_id = Auth::user()->company_id;
            }
        });

        static::addGlobalScope('company_tenant', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();
                // Super Admin can view across companies if explicitly desired, otherwise restrict to company
                if ($user->role !== UserRole::SUPER_ADMIN && $user->company_id) {
                    $builder->where($builder->getModel()->getTable() . '.company_id', $user->company_id);
                }
            }
        });
    }

    /**
     * Relationship to Company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Local scope to query specific company.
     */
    public function scopeForCompany(Builder $query, string $companyId): Builder
    {
        return $query->where($this->getTable() . '.company_id', $companyId);
    }
}
