<?php

namespace App\Services;

class PaginationHelper
{
    public const RECORDS_PER_PAGE = 10;

    /**
     * @param int $page
     * @return int
     */
    public function getOffset(int $page): int
    {
        if ($page <= 0) {
            $page = 1;
        }

        return ($page - 1) * self::RECORDS_PER_PAGE;
    }
}
