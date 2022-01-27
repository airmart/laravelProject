<?php

namespace App\Models\Interfaces;

interface FilterableModelInterface
{
    /**
     * @return array
     */
    public static function getFilterableColumns(): array;
}
