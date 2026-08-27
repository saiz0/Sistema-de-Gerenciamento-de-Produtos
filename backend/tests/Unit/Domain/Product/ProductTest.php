<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Product;

use Domain\Product\Entities\Product;
use Domain\Product\Enums\ProductStatus;
use Domain\Product\ValueObjects\InternalCode;
use Domain\Product\ValueObjects\Price;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ProductTest extends TestCase
{
    public function test_normalizes_data_and_controls_status(): void
    {
        $product = Product::create(
            1,
            ' Produto Exemplo ',
            ' Descrição do produto ',
            new Price('25.90'),
            new InternalCode(' prod-001 '),
        );

        self::assertSame('Produto Exemplo', $product->name());
        self::assertSame('Descrição do produto', $product->description());
        self::assertSame('PROD-001', $product->internalCode()->value());

        $product->deactivate();
        self::assertSame(ProductStatus::Inactive, $product->status());

        $product->activate();
        self::assertSame(ProductStatus::Active, $product->status());
    }

    public function test_converts_an_empty_description_to_null(): void
    {
        $product = Product::create(
            1,
            'Produto Exemplo',
            '   ',
            new Price('1.00'),
            new InternalCode('PROD-001'),
        );

        self::assertNull($product->description());
    }

    public function test_rejects_a_product_without_a_valid_company(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Product::create(
            0,
            'Produto Exemplo',
            null,
            new Price('1.00'),
            new InternalCode('PROD-001'),
        );
    }

    public function test_rejects_an_invalid_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Product::create(
            1,
            'AB',
            null,
            new Price('1.00'),
            new InternalCode('PROD-001'),
        );
    }

    public function test_rejects_a_description_longer_than_two_thousand_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Product::create(
            1,
            'Produto Exemplo',
            str_repeat('a', 2001),
            new Price('1.00'),
            new InternalCode('PROD-001'),
        );
    }
}
