<?php

namespace App\Exceptions;

use Throwable;

class UnsupportedFilterColumnExceptions extends FilterException
{
    public function __construct($column, $code = 0, Throwable $previous = null)
    {
        $message = "The $column column is not allowed for filtering";
        parent::__construct($message, $code, $previous);
    }
}
