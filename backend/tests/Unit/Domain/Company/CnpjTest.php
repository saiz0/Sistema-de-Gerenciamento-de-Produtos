<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Company;

use Domain\Company\ValueObjects\Cnpj;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CnpjTest extends TestCase
{
    public function test_normalizes_a_valid_cnpj(): void
    {
        self::assertSame('11222333000181', (new Cnpj('11.222.333/0001-81'))->value());
    }

    #[DataProvider('invalidCnpjs')]
    public function test_rejects_an_invalid_cnpj(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Cnpj($value);
    }

    public static function invalidCnpjs(): array
    {
        return [[''], ['123'], ['00000000000000'], ['11222333000182']];
    }
}
