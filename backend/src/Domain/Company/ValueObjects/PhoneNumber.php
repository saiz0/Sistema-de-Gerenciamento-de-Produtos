<?php

declare(strict_types=1);

namespace Domain\Company\ValueObjects;

use InvalidArgumentException;

final readonly class PhoneNumber
{
    private const VALID_AREA_CODES = [
        '11', '12', '13', '14', '15', '16', '17', '18', '19',
        '21', '22', '24', '27', '28',
        '31', '32', '33', '34', '35', '37', '38',
        '41', '42', '43', '44', '45', '46', '47', '48', '49',
        '51', '53', '54', '55',
        '61', '62', '63', '64', '65', '66', '67', '68', '69',
        '71', '73', '74', '75', '77', '79',
        '81', '82', '83', '84', '85', '86', '87', '88', '89',
        '91', '92', '93', '94', '95', '96', '97', '98', '99',
    ];

    public string $value;

    public function __construct(string $value)
    {
        $normalized = self::normalize($value);

        if (! in_array(strlen($normalized), [10, 11], true)
            || ! in_array(substr($normalized, 0, 2), self::VALID_AREA_CODES, true)) {
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
