<?php

namespace App\Rules;

use App\Factories\FilterCriteriaFactory;
use Illuminate\Contracts\Validation\Rule;

class FilterCriteriaKeyRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        foreach ($value as $data) {
            if (!array_key_exists($data['criteria'], FilterCriteriaFactory::FILTER_CRITERIAS)) {
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
        return 'The :attribute contains forbidden filter criteria';
    }
}
