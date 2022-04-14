<?php

namespace App\Repositories;

use App\Repositories\FilterCriterias\AbstractFilterCriteria;
use App\Services\PaginationHelper;
use App\Structures\SortData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

abstract class AbstractRepository
{
    /** @var Builder */
    public Builder $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = $this->makeQueryBuilder();
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        /** @var Model $model */
        $model = App::make($this->getModelClass());

        return $model;
    }

    /**
     * @param int $id
     * @param array $data
     */
    public function update(int $id, array $data): void
    {
        $primaryKey = $this->queryBuilder->getModel()->getKeyName();

        $this->queryBuilder->where($primaryKey, $id)->update($data);
    }

    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        $primaryKey = $this->queryBuilder->getModel()->getKeyName();

        $this->queryBuilder->where($primaryKey, $id)->delete();
    }

    /**
     * @param int $offset
     * @param SortData[] $sortData
     * @param AbstractFilterCriteria[] $filterCriterias
     * @param string[] $relations
     * @return Model[]|Collection
     */
    public function get(
        int $offset = 0,
        array $sortData = [],
        array $filterCriterias = [],
        array $relations = []
    ): iterable {
        $query = $this->queryBuilder;

        $query = $query->with($relations);

        foreach ($filterCriterias as $criteria) {
            $query = $criteria->apply($query);
        }

        foreach ($sortData as $data) {
            $query = $query->orderBy($data->sortField, $data->sortDirection);
        }

        return $query
            ->skip($offset)
            ->take(PaginationHelper::RECORDS_PER_PAGE)
            ->get();
    }

    /**
     * @param int $id
     * @param string[] $relations
     * @return Model|null
     */
    public function find(int $id, array $relations = []): ?Model
    {
        return $this->queryBuilder->with($relations)->find($id);
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->queryBuilder->create($data);
    }

    /**
     * @return Builder
     */
    private function makeQueryBuilder(): Builder
    {
        /** @var Model $model */
        $model = App::make($this->getModelClass());

        return $model->newQuery();
    }

    /**
     * This function return name of model class
     *
     * @return string
     */
    abstract public function getModelClass(): string;
}
