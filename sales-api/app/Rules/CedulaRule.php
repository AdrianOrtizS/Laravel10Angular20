<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\DocumentValidator;

class CedulaRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
    	// dd($value);
        if (!DocumentValidator::cedula($value)) {
            $fail('La cedula no es válida.');
        }
    }
}

