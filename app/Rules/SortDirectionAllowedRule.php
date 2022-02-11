<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SortDirectionAllowedRule implements Rule
{
    private const ALLOWED_DIRECTIONS = ['ASC', 'DESC'];

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
        foreach ($value as $item) {
            $upperCaseSortDir = strtoupper($item['sortDirection']);

            if (!in_array($upperCaseSortDir, self::ALLOWED_DIRECTIONS)) {
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
        return 'The :attribute contains invalid sort direction';
    }
}
