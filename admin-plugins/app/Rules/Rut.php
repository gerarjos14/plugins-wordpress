<?php

namespace App\Rules;

use App\ChileRut;
use Illuminate\Contracts\Validation\Rule;

class Rut implements Rule
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
      $chile_rut = new ChileRut;
      return $chile_rut->check($value);

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Formato de RUT invalido.';
    }
}
