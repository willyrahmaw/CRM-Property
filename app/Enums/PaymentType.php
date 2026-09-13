<?php

namespace App\Enums;

enum PaymentType: string
{
    case BOOKING_FEE = 'booking_fee';
    case DOWN_PAYMENT = 'down_payment';
    case INSTALLMENT = 'installment';
    case MORTGAGE_DISBURSEMENT = 'mortgage_disbursement';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::BOOKING_FEE => 'Booking Fee / UTJ',
            self::DOWN_PAYMENT => 'Uang Muka (DP)',
            self::INSTALLMENT => 'Cicilan Bertahap',
            self::MORTGAGE_DISBURSEMENT => 'Pencairan KPR Bank',
            self::OTHER => 'Biaya Lain-lain',
        };
    }
}
