<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidMobileNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
   

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Implement your mobile number validation logic here
        // For example, you can use a regular expression to validate a basic format

        $attribute = '/^\d{10}$/'; // Assuming a 10-digit mobile number
        return preg_match($attribute, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
