<?php

declare(strict_types=1);

namespace Domain\Company\ValueObjects;

use InvalidArgumentException;

final readonly class EmailAddress
{
    public string $value;

    public function __construct(string $value)
    {
        $normalized = mb_strtolower(trim($value));

        if (mb_strlen($normalized) > 254 || filter_var($normalized, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('O e-mail informado é inválido.');
        }

        $this->value = $normalized;
    }

    public function value(): string
    {
        return $this->value;
    }
}
