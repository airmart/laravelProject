<?php

namespace App\Services;

use App\Exceptions\RelationException;
use App\Exceptions\UnsopportedRelationTypeException;
use App\Exceptions\UnsupportedRelationException;
use App\Models\Interfaces\RelationableModelInterface;
use App\Repositories\AbstractRepository;
use Illuminate\Support\Facades\App;

class RelationDataValidator
{
    /**
     * @param AbstractRepository $repository
     * @param array $relationData
     * @throws RelationException
     */
    public function validateRelationData(AbstractRepository $repository, array $relationData): void
    {
        $modelClass = $repository->getModelClass();
        $model = App::make($modelClass);

        if (!$model instanceof RelationableModelInterface) {
            return;
        }

        $allowedRelations = $modelClass::getAvailableRelations();

        foreach ($relationData as $relation) {
            if (!is_string($relation)) {
                throw new UnsopportedRelationTypeException();
            }

            if (!in_array($relation, $allowedRelations)) {
                throw new UnsupportedRelationException($relation);
            }
        }

        $uniqueRelation = array_unique($relationData);

        if (count($uniqueRelation) != count($relationData)) {
            throw new RelationException('Relations array must not contain duplicate entries.');
        }
    }
}
