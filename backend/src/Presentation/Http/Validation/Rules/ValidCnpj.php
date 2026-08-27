<?php

declare(strict_types=1);

namespace Presentation\Http\Validation\Rules;

use Closure;
use Domain\Company\ValueObjects\Cnpj;
use Illuminate\Contracts\Validation\ValidationRule;

final class ValidCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! Cnpj::isValid($value)) {
            $fail('O campo :attribute deve conter um CNPJ válido.');
        }
    }
}
