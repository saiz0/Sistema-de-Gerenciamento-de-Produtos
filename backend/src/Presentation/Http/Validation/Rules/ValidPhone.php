<?php

declare(strict_types=1);

namespace Presentation\Http\Validation\Rules;

use Closure;
use Domain\Company\ValueObjects\PhoneNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;

final class ValidPhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            new PhoneNumber((string) $value);
        } catch (InvalidArgumentException) {
            $fail('O campo :attribute deve conter um telefone válido com DDD.');
        }
    }
}
