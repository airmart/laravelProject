<?php

namespace App\Repositories\FilterCriterias;

use Illuminate\Database\Eloquent\Builder;

abstract class AbstractFilterCriteria
{
    /** @var mixed */
    protected $value;

    /** @var string */
    protected string $column;

    /**
     * @param $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @param string $column
     */
    public function setColumn(string $column): void
    {
        $this->column = $column;
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    abstract public function apply(Builder $builder): Builder;
}
