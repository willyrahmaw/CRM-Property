<?php

namespace App\Exceptions;

use Exception;

class PropertyUnitUnavailableException extends Exception
{
    public function __construct(string $message = 'Unit properti tidak tersedia untuk di-booking atau sedang diproses oleh pemesan lain.')
    {
        parent::__construct($message);
    }
}
