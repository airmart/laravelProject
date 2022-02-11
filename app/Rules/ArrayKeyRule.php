<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ArrayKeyRule implements Rule
{
    /** @var string[] */
    private array $keys;

    /**
     * Create a new rule instance.
     *
     * @param string[] $keys
     */
    public function __construct(array $keys)
    {
        $this->keys = $keys;
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
            foreach ($this->keys as $key) {
                if (!array_key_exists($key, $item)) {
                    return false;
                }
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
        return 'The :attribute has wrong array structure';
    }
}
