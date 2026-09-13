<?php

namespace App\Enums;

enum PaymentScheme: string
{
    case CASH_KERAS = 'cash_keras';
    case CASH_BERTAHAP = 'cash_bertahap';
    case KPR = 'kpr';

    public function label(): string
    {
        return match ($this) {
            self::CASH_KERAS => 'Cash Keras (Hard Cash)',
            self::CASH_BERTAHAP => 'Cash Bertahap (Installment)',
            self::KPR => 'KPR (Mortgage)',
        };
    }

    public function shortLabel(): string
    {
        return match ($this) {
            self::CASH_KERAS => 'Hard Cash',
            self::CASH_BERTAHAP => 'Cash Bertahap',
            self::KPR => 'KPR Bank',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::CASH_KERAS => 'bg-[#F7F6F2] text-[#161616] border border-[#E8E4DA]',
            self::CASH_BERTAHAP => 'bg-[#FFFBEB] text-[#B45309] border border-[#FDE68A]',
            self::KPR => 'bg-[#EFF6FF] text-[#1E40AF] border border-[#BFDBFE]',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CASH_KERAS => 'fa-solid fa-money-bill-wave text-[#15803D]',
            self::CASH_BERTAHAP => 'fa-solid fa-calendar-days text-[#B89B5E]',
            self::KPR => 'fa-solid fa-landmark text-[#2563EB]',
        };
    }
}
