<?php

declare(strict_types=1);

namespace Domain\Company\ValueObjects;

use InvalidArgumentException;

final readonly class PhoneNumber
{
    public string $value;

    public function __construct(string $value)
    {
        $normalized = self::normalize($value);

        if (! in_array(strlen($normalized), [10, 11], true) || str_starts_with($normalized, '00')) {
            throw new InvalidArgumentException('O telefone deve conter DDD e 10 ou 11 dígitos.');
        }

        $this->value = $normalized;
    }

    public static function normalize(string $value): string
    {
        return preg_replace('/\D/', '', $value) ?? '';
    }

    public function value(): string
    {
        return $this->value;
    }
}
