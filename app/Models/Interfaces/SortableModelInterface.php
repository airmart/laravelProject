<?php

namespace App\Models\Interfaces;

interface SortableModelInterface
{
    /**
     * @return string
     */
    public static function getDefaultSortField(): string;

    /**
     * @return string
     */
    public static function getDefaultSortDirection(): string;

    /**
     * @return array
     */
    public static function getAllowedSortFields(): array;
}
