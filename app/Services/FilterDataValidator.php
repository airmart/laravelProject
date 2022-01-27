<?php

namespace App\Services;

use App\Exceptions\BadFilterRequestException;
use App\Exceptions\FilterException;
use App\Exceptions\UnsupportedFilterColumnExceptions;
use App\Exceptions\UnsupportedFilterCriteriaException;
use App\Factories\FilterCriteriaFactory;
use App\Repositories\AbstractRepository;
use App\Models\Interfaces\FilterableModelInterface;
use Illuminate\Support\Facades\App;

class FilterDataValidator
{
    /**
     * @param AbstractRepository $repository
     * @param array $filterData
     * @throws FilterException
     */
    public function validateFilterData(AbstractRepository $repository, array $filterData): void
    {
        $modelClass = $repository->getModelClass();
        $model = App::make($modelClass);

        if (!$model instanceof FilterableModelInterface) {
            return;
        }

        $allowedFilterFields = $modelClass::getFilterableColumns();

        foreach ($filterData as $column => $data) {
            if (!is_array($data)) {
                throw new BadFilterRequestException('Filter data must be an array');
            }

            if (!array_key_exists('criteria', $data)) {
                throw new BadFilterRequestException('Filter data item must have "criteria" key');
            }

            if (!array_key_exists('value', $data)) {
                throw new BadFilterRequestException('Filter data item must have "value" key');
            }

            if (!array_key_exists($data['criteria'], FilterCriteriaFactory::FILTER_CRITERIAS)) {
                throw new UnsupportedFilterCriteriaException($data['criteria']);
            }

            if (!in_array($column, $allowedFilterFields)) {
                throw new UnsupportedFilterColumnExceptions($column);
            }
        }
    }
}
