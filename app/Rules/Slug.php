<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Slug implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    protected $message;



    public function __construct()
    {
        //
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


        //hasUnderscores
        if (preg_match('/_/', $value)) {
            $this->message = trans('validation.no_underscores');

            return false;
        }
        //startsWithDashes
        if (preg_match('/^-/', $value)) {
            $this->message = trans('validation.no_starting_dashes');

            return false;
        }
        //endsWithDashes
        if (preg_match('/-$/', $value)) {
            $this->message = trans('validation.no_ending_dashes');

            return false;
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
        return $this->message;
    }
}
