<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public function __construct($message = "Validation exception", $code = 400) {
        parent::__construct($message, $code);
    }
}
