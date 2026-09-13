<?php

namespace App\Enums;

enum PropertyUnitStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case BOOKED = 'booked';
    case SOLD = 'sold';
    case BLOCKED = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Available',
            self::RESERVED => 'Reserved',
            self::BOOKED => 'Booked',
            self::SOLD => 'Sold',
            self::BLOCKED => 'Blocked',
        };
    }

    public function colorHex(): string
    {
        return match ($this) {
            self::AVAILABLE => '#15803D', // Solid Forest Green
            self::RESERVED => '#D97706',  // Solid Amber
            self::BOOKED => '#B89B5E',    // Luxury Gold
            self::SOLD => '#991B1B',      // Solid Burgundy/Red
            self::BLOCKED => '#262626',   // Solid Dark
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::AVAILABLE => 'bg-[#15803D] text-white',
            self::RESERVED => 'bg-[#D97706] text-white',
            self::BOOKED => 'bg-[#B89B5E] text-white',
            self::SOLD => 'bg-[#991B1B] text-white',
            self::BLOCKED => 'bg-[#262626] text-white',
        };
    }

    public function isAvailableForBooking(): bool
    {
        return $this === self::AVAILABLE;
    }
}
