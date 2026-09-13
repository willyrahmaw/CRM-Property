<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A]',
            self::APPROVED => 'bg-[#DCFCE7] text-[#166534] border border-[#BBF7D0]',
            self::REJECTED => 'bg-[#FEE2E2] text-[#991B1B] border border-[#FECACA]',
            self::CANCELLED => 'bg-[#F3F4F6] text-[#4B5563] border border-[#E5E7EB]',
        };
    }

    public function dotClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-[#D97706]',
            self::APPROVED => 'bg-[#15803D]',
            self::REJECTED => 'bg-[#DC2626]',
            self::CANCELLED => 'bg-[#9CA3AF]',
        };
    }
}
