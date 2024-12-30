<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SortQueryValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $sortingOn;
    public function __construct($sortingOn)
    {
        $this->sortingOn = $sortingOn;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match("/[A-Za-z]+,[ad]/", $value) and is_string($value) and in_array(explode(',', $value)[0], array_keys($this->sortingOn));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid Query Params';
    }
}
