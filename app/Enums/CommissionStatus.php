<?php

namespace App\Enums;

enum CommissionStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case PAID = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Approval',
            self::APPROVED => 'Disetujui (Ready to Pay)',
            self::PAID => 'Sudah Dibayarkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-[#D97706] text-white',
            self::APPROVED => 'bg-[#B89B5E] text-white',
            self::PAID => 'bg-[#15803D] text-white',
        };
    }
}
