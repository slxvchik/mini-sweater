<?php

namespace App\Exceptions;

use Exception;

class TweetNotFoundException extends Exception
{
    public function __construct() {
        parent::__construct('Tweet not found', 404);
    }
}
