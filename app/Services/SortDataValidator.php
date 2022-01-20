<?php

namespace App\Services;

use App\Exceptions\SortDirectionException;
use App\Exceptions\SortFieldException;
use App\Exceptions\SortingException;
use App\Repositories\AbstractRepository;
use App\Models\Interfaces\SortableModelInterface;
use App\Structures\SortData;
use Exception;
use Illuminate\Support\Facades\App;

class SortDataValidator
{
    private const ALLOWED_DIRECTIONS = ['ASC', 'DESC'];

    /**
     * @param AbstractRepository $repository
     * @param SortData $sortData
     * @throws SortDirectionException
     * @throws SortFieldException
     */
    public function validateSortData(AbstractRepository $repository, SortData $sortData): void
    {
        $upperCaseSortDir = strtoupper($sortData->sortDirection);

        if (!in_array($upperCaseSortDir, self::ALLOWED_DIRECTIONS)) {
            throw new SortDirectionException($sortData->sortDirection);
        }

        $modelClass = $repository->getModelClass();
        $model = App::make($modelClass);

        if ($model instanceof SortableModelInterface) {
            $allowedSortFields = $modelClass::getAllowedSortFields();

            if (!in_array($sortData->sortField, $allowedSortFields)) {
                throw new SortFieldException($sortData->sortField);
            }
        }
    }
}
