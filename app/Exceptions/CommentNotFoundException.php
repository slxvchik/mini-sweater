<?php

namespace App\Exceptions;

use Exception;

class CommentNotFoundException extends Exception
{
    public function __construct(string $message = 'Comment not found') {
        parent::__construct($message, 404);
    }
}
