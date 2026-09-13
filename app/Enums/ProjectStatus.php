<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case PLANNING = 'planning';
    case PRE_LAUNCH = 'pre_launch';
    case ACTIVE = 'active';
    case SOLD_OUT = 'sold_out';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::PLANNING => 'Perencanaan',
            self::PRE_LAUNCH => 'Pre-Launch',
            self::ACTIVE => 'Aktif / Pemasaran',
            self::SOLD_OUT => 'Sold Out',
            self::ARCHIVED => 'Diarsipkan',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PLANNING => 'bg-[#262626] text-white',
            self::PRE_LAUNCH => 'bg-[#B89B5E] text-white',
            self::ACTIVE => 'bg-[#15803D] text-white',
            self::SOLD_OUT => 'bg-[#991B1B] text-white',
            self::ARCHIVED => 'bg-[#79766F] text-white',
        };
    }
}
