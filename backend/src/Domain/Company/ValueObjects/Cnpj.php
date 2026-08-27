<?php

declare(strict_types=1);

namespace Domain\Company\ValueObjects;

use InvalidArgumentException;

final readonly class Cnpj
{
    public string $value;

    public function __construct(string $value)
    {
        $normalized = self::normalize($value);

        if (! self::isValid($normalized)) {
            throw new InvalidArgumentException('O CNPJ informado é inválido.');
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

    public static function isValid(string $value): bool
    {
        $value = self::normalize($value);

        if (strlen($value) !== 14 || preg_match('/^(\d)\1{13}$/', $value) === 1) {
            return false;
        }

        return self::digit(substr($value, 0, 12), [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]) === (int) $value[12]
            && self::digit(substr($value, 0, 13), [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]) === (int) $value[13];
    }

    private static function digit(string $base, array $weights): int
    {
        $sum = 0;

        foreach ($weights as $index => $weight) {
            $sum += (int) $base[$index] * $weight;
        }

        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
