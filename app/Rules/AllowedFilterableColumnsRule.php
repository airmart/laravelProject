<?php

namespace App\Rules;

use App\Models\Interfaces\FilterableModelInterface;
use App\Models\Interfaces\RelationableModelInterface;
use App\Models\Interfaces\SortableModelInterface;
use App\Repositories\AbstractRepository;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\App;

class AllowedFilterableColumnsRule implements Rule
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

        if (!$model instanceof FilterableModelInterface) {
            return true;
        }

        $allowedFilterFields = $model::getFilterableColumns();

        foreach ($value as $column => $data) {
            if (!in_array($column, $allowedFilterFields)) {
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
        return 'The :attribute contains forbidden filtering columns';
    }
}
