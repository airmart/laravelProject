<?php

namespace App\Repositories\FilterCriterias;

use Illuminate\Database\Eloquent\Builder;

class ContainsStrCriteria extends AbstractFilterCriteria
{
    /**
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder): Builder
    {
        return $builder->where($this->column, 'like', "%{$this->value}%");
    }
}
