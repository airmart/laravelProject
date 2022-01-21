<?php

namespace App\Services;

use App\Exceptions\BadSortRequestException;
use App\Exceptions\SortDirectionException;
use App\Exceptions\SortFieldException;
use App\Exceptions\SortingException;
use App\Repositories\AbstractRepository;
use App\Models\Interfaces\SortableModelInterface;
use Illuminate\Support\Facades\App;

class SortDataValidator
{
    private const ALLOWED_DIRECTIONS = ['ASC', 'DESC'];

    /**
     * @param AbstractRepository $repository
     * @param array $sortData
     * @throws SortingException
     */
    public function validateSortData(AbstractRepository $repository, array $sortData): void
    {
        $modelClass = $repository->getModelClass();
        $model = App::make($modelClass);

        if (!$model instanceof SortableModelInterface) {
            return;
        }

        $allowedSortFields = $modelClass::getAllowedSortFields();

        foreach ($sortData as $data) {
            if (!is_array($data)) {
                throw new BadSortRequestException('Sort data item must be an array');
            }

            if (!array_key_exists('sortField', $data)) {
                throw new BadSortRequestException('Sort data item must have "sortField" key');
            }

            if (!array_key_exists('sortDirection', $data)) {
                throw new BadSortRequestException('Sort data item must have "sortDirection" key');
            }

            $upperCaseSortDir = strtoupper($data['sortDirection']);

            if (!in_array($upperCaseSortDir, self::ALLOWED_DIRECTIONS)) {
                throw new SortDirectionException($data['sortDirection']);
            }

            if (!in_array($data['sortField'], $allowedSortFields)) {
                throw new SortFieldException($data['sortField']);
            }
        }
    }
}
