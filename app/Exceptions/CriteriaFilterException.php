<?php

namespace App\Exceptions;

use Throwable;

class CriteriaFilterException extends FilterException
{
    public function __construct($criteriaFilter, $code = 0, Throwable $previous = null)
    {
        $message = "The $criteriaFilter is not allowed";
        parent::__construct($message, $code, $previous);
    }
}
