<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Product;

use Application\Product\DTOs\CreateProductData;
use Application\Product\Exceptions\ProductConflict;
use Application\Product\Services\EnsureCompanyCanReceiveProducts;
use Application\Product\Services\EnsureInternalCodeIsUnique;
use Application\Product\UseCases\CreateProduct;
use Domain\Company\Entities\Company;
use Domain\Company\Enums\CompanyStatus;
use Domain\Company\ValueObjects\Cnpj;
use Domain\Company\ValueObjects\EmailAddress;
use Domain\Company\ValueObjects\PhoneNumber;
use Domain\Product\Enums\ProductStatus;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\InMemoryCompanyRepository;
use Tests\Fakes\InMemoryProductRepository;

final class CreateProductTest extends TestCase
{
    public function test_creates_a_product_for_an_active_company(): void
    {
        [$useCase] = $this->useCaseWithCompany();

        $product = $useCase->execute($this->data());

        self::assertSame(1, $product->id());
        self::assertSame(1, $product->companyId());
        self::assertSame('PROD-001', $product->internalCode()->value());
        self::assertSame('10.50', $product->price()->value());
    }

    public function test_rejects_a_duplicate_internal_code_inside_the_same_company(): void
    {
        [$useCase] = $this->useCaseWithCompany();
        $useCase->execute($this->data());

        $this->expectException(ProductConflict::class);
        $useCase->execute($this->data());
    }

    public function test_rejects_a_product_for_an_inactive_company(): void
    {
        [$useCase] = $this->useCaseWithCompany(CompanyStatus::Inactive);

        $this->expectException(ProductConflict::class);
        $useCase->execute($this->data());
    }

    public function test_rejects_a_product_for_a_missing_company(): void
    {
        $companies = new InMemoryCompanyRepository;
        $products = new InMemoryProductRepository;
        $useCase = new CreateProduct(
            $products,
            new EnsureCompanyCanReceiveProducts($companies),
            new EnsureInternalCodeIsUnique($products),
        );

        $this->expectException(ProductConflict::class);
        $useCase->execute($this->data());
    }

    private function useCaseWithCompany(CompanyStatus $status = CompanyStatus::Active): array
    {
        $companies = new InMemoryCompanyRepository;
        $companies->save(Company::create(
            'Empresa Exemplo',
            new Cnpj('11222333000181'),
            new EmailAddress('contato@exemplo.com'),
            new PhoneNumber('71999999999'),
            $status,
        ));
        $products = new InMemoryProductRepository;

        return [
            new CreateProduct(
                $products,
                new EnsureCompanyCanReceiveProducts($companies),
                new EnsureInternalCodeIsUnique($products),
            ),
            $companies,
            $products,
        ];
    }

    private function data(): CreateProductData
    {
        return new CreateProductData(
            1,
            'Produto Exemplo',
            'Descrição do produto',
            '10.50',
            'prod-001',
            ProductStatus::Active,
        );
    }
}
