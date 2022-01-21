<?php

namespace App\Services;

use App\Structures\SortData;

class SortDataParser
{
    /**
     * @param array $sortData
     * @return SortData[]
     */
    public function parseSortData(array $sortData): array
    {
        $sortDataCollection = [];

        foreach ($sortData as $data) {
            $sortDataObj = new SortData();
            $sortDataObj->sortField = $data['sortField'];
            $sortDataObj->sortDirection = $data['sortDirection'];
            $sortDataCollection[] = $sortDataObj;
        }

        return $sortDataCollection;
    }
}
