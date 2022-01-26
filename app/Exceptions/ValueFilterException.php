<?php

namespace App\Exceptions;

use Throwable;

class ValueFilterException extends FilterException
{
    public function __construct($valueFilter, $code = 0, Throwable $previous = null)
    {
        $message = "The $valueFilter is not allowed";
        parent::__construct($message, $code, $previous);
    }
}
