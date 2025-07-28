<?php

namespace App\Exceptions;

use Exception;

class FollowNotFound extends Exception
{
    public function __construct(string $message = "Follow not found") {
        parent::__construct($message, 404);
    }
}
