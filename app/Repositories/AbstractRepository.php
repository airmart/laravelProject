<?php

namespace App\Repositories;

use App\Services\PaginationHelper;
use App\Structures\SortData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

abstract class AbstractRepository
{
    /** @var Builder */
    protected Builder $queryBuilder;

    public function __construct()
    {
        $this->queryBuilder = $this->makeQueryBuilder();
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
     * @return Model[]|Collection
     */
    public function get(int $offset, array $sortData): iterable
    {
        $query = $this->queryBuilder->skip($offset)->take(PaginationHelper::RECORDS_PER_PAGE);

        foreach ($sortData as $data) {
            $query = $query->orderBy($data->sortField, $data->sortDirection);
        }

        return $query->get();
    }

    /**
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model
    {
        return $this->queryBuilder->find($id);
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
