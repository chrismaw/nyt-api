<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MultipleOfTwenty implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_int($value)){ // for some reason the integer rule was still allowing strings to pass through to this point
            if ($value % 20 !== 0) {
                $fail('The :attribute must be 0 or a multiple of 20.');
            }
        }
    }
}
