<?php

declare(strict_types=1);

namespace Domain\Product\ValueObjects;

use InvalidArgumentException;

final readonly class Price
{
    private const MAX_INTEGER_DIGITS = 13;

    private int $cents;

    public function __construct(string $value)
    {
        $normalized = str_replace(',', '.', trim($value));

        if (preg_match('/^\d+(?:\.\d{1,2})?$/', $normalized) !== 1) {
            throw new InvalidArgumentException('O preço deve ser informado com até duas casas decimais.');
        }

        [$integer, $decimal] = array_pad(explode('.', $normalized, 2), 2, '');
        $significantInteger = ltrim($integer, '0');

        if (strlen($significantInteger) > self::MAX_INTEGER_DIGITS) {
            throw new InvalidArgumentException('O preço informado excede o valor máximo permitido.');
        }

        $cents = ((int) $integer * 100) + (int) str_pad($decimal, 2, '0');

        if ($cents <= 0) {
            throw new InvalidArgumentException('O preço deve ser maior que zero.');
        }

        $this->cents = $cents;
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function value(): string
    {
        return sprintf('%d.%02d', intdiv($this->cents, 100), $this->cents % 100);
    }
}
