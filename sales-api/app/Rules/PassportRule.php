<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\DocumentValidator;

class PassportRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (!DocumentValidator::passport($value)) {
            $fail('El pasaporte no es válido.');
        }
    }
}

