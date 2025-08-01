<?php

namespace App\Exceptions;

use Exception;

class AuthException extends Exception
{
    public function __construct($message = "Auth exception", $code = 401) {
        parent::__construct($message, $code);
    }
}
