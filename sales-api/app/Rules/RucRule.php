<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\DocumentValidator;

class RucRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!DocumentValidator::ruc($value)) {
            $fail('El ruc no es válido.');
        }
    }
}

