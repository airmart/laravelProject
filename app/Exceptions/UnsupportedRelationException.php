<?php

namespace App\Exceptions;

use Throwable;

class UnsupportedRelationException extends RelationException
{
    public function __construct($relation, $code = 0, Throwable $previous = null)
    {
        $message = "The $relation is not allowed";
        parent::__construct($message, $code, $previous);
    }
}
