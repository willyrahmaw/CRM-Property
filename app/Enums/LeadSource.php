<?php

namespace App\Enums;

enum LeadSource: string
{
    case WEBSITE = 'website';
    case WHATSAPP = 'whatsapp';
    case INSTAGRAM = 'instagram';
    case TIKTOK = 'tiktok';
    case FACEBOOK_ADS = 'facebook_ads';
    case GOOGLE_ADS = 'google_ads';
    case WALK_IN = 'walk_in';
    case REFERRAL = 'referral';
    case MARKETPLACE = 'marketplace';
    case MANUAL = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::WEBSITE => 'Website',
            self::WHATSAPP => 'WhatsApp',
            self::INSTAGRAM => 'Instagram',
            self::TIKTOK => 'TikTok',
            self::FACEBOOK_ADS => 'Facebook Ads',
            self::GOOGLE_ADS => 'Google Ads',
            self::WALK_IN => 'Walk In',
            self::REFERRAL => 'Referral',
            self::MARKETPLACE => 'Marketplace',
            self::MANUAL => 'Manual Entry',
        };
    }
}
