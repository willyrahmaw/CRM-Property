<?php

namespace App\Enums;

enum LeadStatus: string
{
    case NEW = 'new';
    case CONTACTED = 'contacted';
    case QUALIFIED = 'qualified';
    case SITE_VISIT = 'site_visit';
    case NEGOTIATION = 'negotiation';
    case BOOKING = 'booking';
    case WON = 'won';
    case LOST = 'lost';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'New Lead',
            self::CONTACTED => 'Contacted',
            self::QUALIFIED => 'Qualified',
            self::SITE_VISIT => 'Site Visit',
            self::NEGOTIATION => 'Negotiation',
            self::BOOKING => 'Booking',
            self::WON => 'Won / Closing',
            self::LOST => 'Lost',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NEW => 'bg-[#262626] text-white',
            self::CONTACTED => 'bg-[#79766F] text-white',
            self::QUALIFIED => 'bg-[#D7C49E] text-[#161616]',
            self::SITE_VISIT => 'bg-[#B89B5E] text-white',
            self::NEGOTIATION => 'bg-[#D97706] text-white',
            self::BOOKING => 'bg-[#0284C7] text-white',
            self::WON => 'bg-[#15803D] text-white',
            self::LOST => 'bg-[#991B1B] text-white',
        };
    }
}
