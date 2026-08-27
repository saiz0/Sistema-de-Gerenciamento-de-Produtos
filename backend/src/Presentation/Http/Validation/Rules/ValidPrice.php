<?php

declare(strict_types=1);

namespace Presentation\Http\Validation\Rules;

use Closure;
use Domain\Product\ValueObjects\Price;
use Illuminate\Contracts\Validation\ValidationRule;
use InvalidArgumentException;

final class ValidPrice implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) && ! is_int($value) && ! is_float($value)) {
            $fail('O campo :attribute deve conter um preço válido.');

            return;
        }

        try {
            new Price((string) $value);
        } catch (InvalidArgumentException $exception) {
            $fail($exception->getMessage());
        }
    }
}
