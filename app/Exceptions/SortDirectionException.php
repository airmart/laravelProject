<?php

namespace App\Exceptions;

use Throwable;

class SortDirectionException extends SortingException
{
    public function __construct($sortDir, $code = 0, Throwable $previous = null)
    {
        $message = "The $sortDir sort direction is not allowed";
        parent::__construct($message, $code, $previous);
    }
}
