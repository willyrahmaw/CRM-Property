<?php

namespace App\Enums;

enum LeadTemperature: string
{
    case COLD = 'cold';
    case WARM = 'warm';
    case HOT = 'hot';

    public function label(): string
    {
        return match ($this) {
            self::COLD => 'Cold (0-39)',
            self::WARM => 'Warm (40-69)',
            self::HOT => 'Hot (70-100)',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::COLD => 'bg-[#79766F] text-white',
            self::WARM => 'bg-[#D97706] text-white',
            self::HOT => 'bg-[#DC2626] text-white',
        };
    }
}
