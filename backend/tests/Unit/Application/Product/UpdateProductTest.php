<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Product;

use Application\Product\DTOs\UpdateProductData;
use Application\Product\Exceptions\ProductConflict;
use Application\Product\Services\EnsureCompanyCanReceiveProducts;
use Application\Product\Services\EnsureInternalCodeIsUnique;
use Application\Product\UseCases\UpdateProduct;
use Domain\Company\Entities\Company;
use Domain\Company\Enums\CompanyStatus;
use Domain\Company\ValueObjects\Cnpj;
use Domain\Company\ValueObjects\EmailAddress;
use Domain\Company\ValueObjects\PhoneNumber;
use Domain\Product\Entities\Product;
use Domain\Product\Enums\ProductStatus;
use Domain\Product\ValueObjects\InternalCode;
use Domain\Product\ValueObjects\Price;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\InMemoryCompanyRepository;
use Tests\Fakes\InMemoryProductRepository;

final class UpdateProductTest extends TestCase
{
    public function test_updates_data_without_changing_status(): void
    {
        [$useCase, $products] = $this->useCase();
        $product = $products->save($this->product('PROD-001', ProductStatus::Inactive));

        $updated = $useCase->execute($product->id(), $this->data('PROD-002'));

        self::assertSame('Produto Atualizado', $updated->name());
        self::assertSame('PROD-002', $updated->internalCode()->value());
        self::assertSame(ProductStatus::Inactive, $updated->status());
    }

    public function test_rejects_a_duplicate_code_from_the_target_company_even_when_soft_deleted(): void
    {
        [$useCase, $products] = $this->useCase();
        $first = $products->save($this->product('PROD-001'));
        $second = $products->save($this->product('PROD-002'));
        $products->softDelete($first->id());

        $this->expectException(ProductConflict::class);

        $useCase->execute($second->id(), $this->data('PROD-001'));
    }

    public function test_rejects_moving_a_product_to_an_inactive_company(): void
    {
        [$useCase, $products, $companies] = $this->useCase();
        $product = $products->save($this->product('PROD-001'));
        $companies->save($this->company(
            'Segunda Empresa',
            '11444777000161',
            'segunda@exemplo.com',
            CompanyStatus::Inactive,
        ));

        $this->expectException(ProductConflict::class);

        $useCase->execute($product->id(), new UpdateProductData(
            2,
            'Produto Atualizado',
            null,
            '20.00',
            'PROD-001',
        ));
    }

    private function useCase(): array
    {
        $companies = new InMemoryCompanyRepository;
        $companies->save($this->company('Empresa Exemplo', '11222333000181', 'contato@exemplo.com'));
        $products = new InMemoryProductRepository;

        return [
            new UpdateProduct(
                $products,
                new EnsureCompanyCanReceiveProducts($companies),
                new EnsureInternalCodeIsUnique($products),
            ),
            $products,
            $companies,
        ];
    }

    private function product(string $code, ProductStatus $status = ProductStatus::Active): Product
    {
        return Product::create(1, 'Produto Exemplo', null, new Price('10.00'), new InternalCode($code), $status);
    }

    private function data(string $code): UpdateProductData
    {
        return new UpdateProductData(1, 'Produto Atualizado', 'Nova descrição', '20.00', $code);
    }

    private function company(
        string $name,
        string $cnpj,
        string $email,
        CompanyStatus $status = CompanyStatus::Active,
    ): Company {
        return Company::create(
            $name,
            new Cnpj($cnpj),
            new EmailAddress($email),
            new PhoneNumber('71999999999'),
            $status,
        );
    }
}
