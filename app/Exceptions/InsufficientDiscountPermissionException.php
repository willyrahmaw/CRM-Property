<?php

namespace App\Exceptions;

use Exception;

class InsufficientDiscountPermissionException extends Exception
{
    public function __construct(string $message = 'Diskon melebihi batas wewenang Anda. Pengajuan membutuhkan persetujuan Sales Manager / Owner.')
    {
        parent::__construct($message);
    }
}
