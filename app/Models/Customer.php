<?php

namespace App\Models;

use App\Support\Traits\BelongsToCompany;
use App\Support\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, HasUuid, BelongsToCompany, SoftDeletes;

    protected $fillable = [
        'company_id',
        'lead_id',
        'nik',
        'name',
        'phone',
        'email',
        'address',
        'occupation',
        'npwp',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class, 'customer_id');
    }

    /**
     * Get direct WhatsApp URL with sanitized international number.
     */
    public function getWhatsAppUrl(?string $message = null): ?string
    {
        return \App\Support\WhatsAppHelper::buildUrl(
            $this->phone,
            $message ?? "Halo Bapak/Ibu {$this->name}, salam hangat dari pengembang properti."
        );
    }

    /**
     * Get pre-crafted WhatsApp message templates for this customer.
     */
    public function getWhatsAppTemplates(?User $sales = null): array
    {
        return \App\Support\WhatsAppHelper::getCustomerTemplates($this, $sales);
    }
}
