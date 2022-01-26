<?php

namespace Database\Factories;

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
use Illuminate\Database\Eloquent\Factories\Factory;

class FilterCriteriaFactory extends Factory
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
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }
}
