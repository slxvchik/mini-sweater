<?php

namespace App\Exceptions;

use Exception;

class LikeNotFoundException extends Exception
{
    public function __construct() {
        parent::__construct('Like not found', 404);
    }
}
