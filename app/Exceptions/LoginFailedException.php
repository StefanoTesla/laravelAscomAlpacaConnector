<?php

namespace App\Exceptions;

use Exception;

class LoginFailedException extends Exception
{
    public function __construct($message = "Login failed.", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
