<?php

namespace App\Factories;

use App\Exceptions\UnsupportedFilterCriteriaException;
use App\Repositories\FilterCriterias\AbstractFilterCriteria;
use App\Repositories\FilterCriterias\ContainsStrCriteria;
use App\Repositories\FilterCriterias\EndsWithCriteria;
use App\Repositories\FilterCriterias\InArrayCriteria;
use App\Repositories\FilterCriterias\IsEqualToCriteria;
use App\Repositories\FilterCriterias\IsGreaterThenCriteria;
use App\Repositories\FilterCriterias\IsGreatOrEqualCriteria;
use App\Repositories\FilterCriterias\IsLessOrEqualCriteria;
use App\Repositories\FilterCriterias\IsLessThenCriteria;
use App\Repositories\FilterCriterias\IsNotEqualToCriteria;
use App\Repositories\FilterCriterias\IsNotNullCriteria;
use App\Repositories\FilterCriterias\IsNullCriteria;
use App\Repositories\FilterCriterias\NotInArrayCriteria;
use App\Repositories\FilterCriterias\StartsWithCriteria;
use Illuminate\Support\Facades\App;

class FilterCriteriaFactory
{
    public const FILTER_CRITERIAS = [
        'contains_str' => ContainsStrCriteria::class,
        'ends_with' => EndsWithCriteria::class,
        'in_array' => InArrayCriteria::class,
        'is_equal_to' => IsEqualToCriteria::class,
        'is_greater_then' => IsGreaterThenCriteria::class,
        'is_great_or_equal' => IsGreatOrEqualCriteria::class,
        'is_less_or_equal' => IsLessOrEqualCriteria::class,
        'is_less_then' => IsLessThenCriteria::class,
        'is_not_equal_to' => IsNotEqualToCriteria::class,
        'is_not_null' => IsNotNullCriteria::class,
        'is_null' => IsNullCriteria::class,
        'not_in_array' => NotInArrayCriteria::class,
        'starts_with' => StartsWithCriteria::class
    ];

    /**
     * @param array $filterData
     * @return AbstractFilterCriteria[]
     * @throws UnsupportedFilterCriteriaException
     */
    public static function makeCriterias(array $filterData): array
    {
        $criterias = [];

        foreach ($filterData as $column => $data) {
            $criteria = self::makeFilterCriteria($data['criteria']);
            $criteria->setValue($data['value']);
            $criteria->setColumn($column);
            $criterias[] = $criteria;
        }

        return $criterias;
    }

    /**
     * @param string $criteriaAlias
     * @return AbstractFilterCriteria
     * @throws UnsupportedFilterCriteriaException
     */
    private static function makeFilterCriteria(string $criteriaAlias): AbstractFilterCriteria
    {
        if (!array_key_exists($criteriaAlias, self::FILTER_CRITERIAS)) {
            throw new UnsupportedFilterCriteriaException($criteriaAlias);
        }

        return App::make(self::FILTER_CRITERIAS[$criteriaAlias]);
    }
}
