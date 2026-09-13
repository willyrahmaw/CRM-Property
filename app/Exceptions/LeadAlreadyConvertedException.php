<?php

namespace App\Exceptions;

use Exception;

class LeadAlreadyConvertedException extends Exception
{
    public function __construct(string $message = 'Lead ini telah dikonversi menjadi Customer dan tidak dapat dikonversi ulang.')
    {
        parent::__construct($message);
    }
}
