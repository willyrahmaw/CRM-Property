<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::VERIFIED => 'Terverifikasi (Lunas)',
            self::REJECTED => 'Ditolak / Invalid',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-[#D97706] text-white',
            self::VERIFIED => 'bg-[#15803D] text-white',
            self::REJECTED => 'bg-[#991B1B] text-white',
        };
    }
}
