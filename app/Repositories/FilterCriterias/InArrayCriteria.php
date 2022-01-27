<?php

namespace App\Repositories\FilterCriterias;

use Illuminate\Database\Eloquent\Builder;

class InArrayCriteria extends AbstractFilterCriteria
{
    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder): Builder
    {
        return $builder->whereIn($this->column, $this->value);
    }
}
