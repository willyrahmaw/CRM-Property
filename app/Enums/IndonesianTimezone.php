<?php

namespace App\Enums;

enum IndonesianTimezone: string
{
    case WIB = 'Asia/Jakarta';
    case WITA = 'Asia/Makassar';
    case WIT = 'Asia/Jayapura';

    /**
     * Label in Indonesian with UTC offset and coverage.
     */
    public function label(): string
    {
        return match ($this) {
            self::WIB => 'WIB — Waktu Indonesia Barat (UTC+7)',
            self::WITA => 'WITA — Waktu Indonesia Tengah (UTC+8)',
            self::WIT => 'WIT — Waktu Indonesia Timur (UTC+9)',
        };
    }

    /**
     * Short timezone abbreviation.
     */
    public function code(): string
    {
        return match ($this) {
            self::WIB => 'WIB',
            self::WITA => 'WITA',
            self::WIT => 'WIT',
        };
    }

    /**
     * UTC Offset string.
     */
    public function utcOffset(): string
    {
        return match ($this) {
            self::WIB => '+07:00',
            self::WITA => '+08:00',
            self::WIT => '+09:00',
        };
    }

    /**
     * Region coverage in Indonesia.
     */
    public function regions(): string
    {
        return match ($this) {
            self::WIB => 'Sumatera, Jawa, Madura, Kalbar, Kalteng',
            self::WITA => 'Bali, Nusa Tenggara, Kalsel, Kaltim, Kaltara, Sulawesi',
            self::WIT => 'Maluku, Maluku Utara, Papua',
        };
    }

    /**
     * All allowed timezone identifier values.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Resolve timezone enum from value or fallback to WIB.
     */
    public static function fromOrDefault(?string $value): self
    {
        return self::tryFrom($value ?? '') ?? self::WIB;
    }
}
