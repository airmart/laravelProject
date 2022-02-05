<?php

namespace App\Models\Interfaces;

interface RelationableModelInterface
{
    /**
     * @return string[]
     */
    public static function getAvailableRelations(): array;
}
