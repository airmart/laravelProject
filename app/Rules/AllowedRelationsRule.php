<?php

namespace App\Rules;

use App\Models\Interfaces\RelationableModelInterface;
use App\Repositories\AbstractRepository;
use Illuminate\Contracts\Validation\Rule;

class AllowedRelationsRule implements Rule
{
    /** @var AbstractRepository */
    private AbstractRepository $repository;

    /**
     * Create a new rule instance.
     *
     * @param AbstractRepository $repository
     */
    public function __construct(AbstractRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $model = $this->repository->getModel();

        if (!$model instanceof RelationableModelInterface) {
            return true;
        }

        $allowedFilterFields = $model::getAvailableRelations();

        foreach ($value as $item) {
            if (!in_array($item, $allowedFilterFields)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute contains forbidden relation';
    }
}
