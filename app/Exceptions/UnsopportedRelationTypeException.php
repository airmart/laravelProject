<?php

namespace App\Exceptions;

use Throwable;

class UnsopportedRelationTypeException extends RelationException
{
    public function __construct($message = "Relation must be type of string", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
