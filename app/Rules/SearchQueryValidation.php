<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SearchQueryValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public $searchingOn;
    public function __construct($searchingOn)
    {
        $this->searchingOn = $searchingOn;
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
        return preg_match("/[a-zA-Z0-9+\-\/%_. ()&%-]+,[A-Za-z]/", $value) and is_string($value) and in_array(explode(',', $value)[1], array_keys($this->searchingOn));
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