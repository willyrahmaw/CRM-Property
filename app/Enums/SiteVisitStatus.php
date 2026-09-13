<?php

namespace App\Enums;

enum SiteVisitStatus: string
{
    case SCHEDULED = 'scheduled';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case NO_SHOW = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Terjadwal',
            self::COMPLETED => 'Selesai / Hadir',
            self::CANCELLED => 'Dibatalkan',
            self::NO_SHOW => 'Tidak Hadir',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::SCHEDULED => 'bg-[#B89B5E] text-white',
            self::COMPLETED => 'bg-[#15803D] text-white',
            self::CANCELLED => 'bg-[#79766F] text-white',
            self::NO_SHOW => 'bg-[#991B1B] text-white',
        };
    }
}
