<?php

namespace App\Services;

use App\Exceptions\BadFilterRequestException;
use App\Exceptions\CriteriaFilterException;
use App\Exceptions\ValueFilterException;
use App\Models\Interfaces\SortableModelInterface;
use App\Repositories\AbstractRepository;
use Database\Factories\FilterCriteriaFactory;
use Illuminate\Support\Facades\App;

class FilterDataValidator
{
    public function validateFilterData(AbstractRepository $repository, array $filterData)
    {
        $modelClass = $repository->getModelClass();
        $model = App::make($modelClass);

        if (!$model instanceof SortableModelInterface) {
            return;
        }

        $allowedFilterFields = $modelClass::getFiltrableColumns();

        foreach ($filterData as $data) {
            if (!is_array($data[])) {
                throw new BadFilterRequestException('Filter data columns must be an array');
            }

            if (!is_array($data[][])) {
                throw new BadFilterRequestException('Filter data keys must be an array');
            }

            if (!array_key_exists('criteria', $data[]['criteria'])) {
                throw new BadFilterRequestException('Filter data item must have "criteria" key');
            }

            if (!in_array($data[], $allowedFilterFields)) {
                throw new BadFilterRequestException('Filter data columns must be allowed');
            }

            if (!array_key_exists('value', $data[]['value'])) {
                throw new BadFilterRequestException('Filter data item must have "value" key');
            }

//            $lowerCaseCriteriaFilter = strtolower($data['criteria']);

            if (!in_array($data[]['criteria'], FilterCriteriaFactory::FILTER_CRITERIAS)) {
                throw new CriteriaFilterException($data[]['criteria']);
            }

//            if (!in_array($data[]['value'])) {
//                throw new ValueFilterException($data[]['value']);
//            }
        }
    }
}
