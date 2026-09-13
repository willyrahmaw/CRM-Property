<?php

namespace App\Enums;

enum MortgageStatus: string
{
    case DRAFT = 'draft';
    case COLLECTING_DOCS = 'collecting_docs';
    case SUBMITTED = 'submitted';
    case APPRAISAL = 'appraisal';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CONTRACT_SIGNED = 'contract_signed';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft Permohonan',
            self::COLLECTING_DOCS => 'Pengumpulan Berkas',
            self::SUBMITTED => 'Diajukan ke Bank',
            self::APPRAISAL => 'Proses Penilaian / Appraisal',
            self::APPROVED => 'Disetujui Bank (SP3K)',
            self::REJECTED => 'Ditolak Bank',
            self::CONTRACT_SIGNED => 'Akad Kredit Selesai',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-[#79766F] text-white',
            self::COLLECTING_DOCS => 'bg-[#262626] text-white',
            self::SUBMITTED => 'bg-[#0284C7] text-white',
            self::APPRAISAL => 'bg-[#D97706] text-white',
            self::APPROVED => 'bg-[#B89B5E] text-white',
            self::REJECTED => 'bg-[#991B1B] text-white',
            self::CONTRACT_SIGNED => 'bg-[#15803D] text-white',
        };
    }
}
