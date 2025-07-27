<?php

namespace App\Exceptions;

use Exception;

class ConflictException extends Exception
{
    public function __construct($message = "Conflict exception", $code = 409) {
        parent::__construct($message, $code);
    }
}
