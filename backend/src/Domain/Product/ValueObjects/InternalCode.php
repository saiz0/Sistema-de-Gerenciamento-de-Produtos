<?php

declare(strict_types=1);

namespace Domain\Product\ValueObjects;

use InvalidArgumentException;

final readonly class InternalCode
{
    private string $value;

    public function __construct(string $value)
    {
        $normalized = mb_strtoupper(trim($value));

        if ($normalized === '' || mb_strlen($normalized) > 100) {
            throw new InvalidArgumentException('O código interno é obrigatório e deve conter no máximo 100 caracteres.');
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }
}
