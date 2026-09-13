<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Laki-laki',
            self::FEMALE => 'Perempuan',
        };
    }

    public function salutation(): string
    {
        return match ($this) {
            self::MALE => 'Bapak',
            self::FEMALE => 'Ibu',
        };
    }
}
