<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Product;

use Domain\Product\ValueObjects\InternalCode;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class InternalCodeTest extends TestCase
{
    public function test_normalizes_the_internal_code(): void
    {
        self::assertSame('PROD-001', (new InternalCode(' prod-001 '))->value());
    }

    public function test_rejects_an_empty_internal_code(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InternalCode('   ');
    }

    public function test_rejects_an_internal_code_longer_than_one_hundred_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new InternalCode(str_repeat('A', 101));
    }
}
