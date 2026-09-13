<?php

namespace App\Exceptions;

use Exception;

class InvalidBookingStateException extends Exception
{
    public function __construct(string $message = 'Status booking tidak valid untuk melakukan operasi ini.')
    {
        parent::__construct($message);
    }
}
