<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Product;

use Domain\Product\ValueObjects\Price;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PriceTest extends TestCase
{
    #[DataProvider('validPrices')]
    public function test_normalizes_valid_prices(string $input, string $expected, int $cents): void
    {
        $price = new Price($input);

        self::assertSame($expected, $price->value());
        self::assertSame($cents, $price->cents());
    }

    #[DataProvider('invalidPrices')]
    public function test_rejects_invalid_prices(string $input): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Price($input);
    }

    public static function validPrices(): array
    {
        return [
            ['10', '10.00', 1000],
            ['10.5', '10.50', 1050],
            ['10,50', '10.50', 1050],
            ['0.01', '0.01', 1],
        ];
    }

    public static function invalidPrices(): array
    {
        return [[''], ['0'], ['-1.00'], ['10.999'], ['abc']];
    }
}
