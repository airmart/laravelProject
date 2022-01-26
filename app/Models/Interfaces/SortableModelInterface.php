<?php

namespace App\Models\Interfaces;

interface SortableModelInterface
{
    static function getDefaultSortField(): string;
    static function getDefaultSortDirection(): string;
    static function getAllowedSortFields(): array;
    static function getFiltrableColumns(): array;
}
