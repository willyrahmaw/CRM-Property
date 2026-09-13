<?php

namespace App\Enums;

enum NegotiationApprovalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Approval',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-[#FEF3C7] text-[#92400E] border border-[#FDE68A]',
            self::APPROVED => 'bg-[#DCFCE7] text-[#15803D] border border-[#BBF7D0]',
            self::REJECTED => 'bg-[#FEE2E2] text-[#991B1B] border border-[#FECACA]',
        };
    }
}
