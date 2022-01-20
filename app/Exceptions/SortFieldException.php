<?php

namespace App\Exceptions;

use Throwable;

class SortFieldException extends SortingException
{
    public function __construct($sortField, $code = 0, Throwable $previous = null)
    {
        $message = "The $sortField sort field is not allowed";
        parent::__construct($message, $code, $previous);
    }
}
