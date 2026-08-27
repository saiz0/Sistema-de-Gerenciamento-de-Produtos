<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Product;

use Application\Product\Exceptions\ProductConflict;
use Application\Product\Services\EnsureCompanyCanReceiveProducts;
use Application\Product\UseCases\ActivateProduct;
use Application\Product\UseCases\ForceDeleteProduct;
use Application\Product\UseCases\RestoreProduct;
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

final class ProductLifecycleTest extends TestCase
{
    public function test_rejects_activation_when_company_is_inactive(): void
    {
        [$companies, $products] = $this->repositories();
        $company = $companies->findById(1);
        self::assertNotNull($company);
        $company->deactivate();
        $companies->save($company);
        $product = $products->save($this->product(ProductStatus::Inactive));

        $this->expectException(ProductConflict::class);

        (new ActivateProduct($products, new EnsureCompanyCanReceiveProducts($companies)))->execute($product->id());
    }

    public function test_rejects_activation_when_company_is_soft_deleted(): void
    {
        [$companies, $products] = $this->repositories();
        $product = $products->save($this->product(ProductStatus::Inactive));
        $companies->softDelete(1);

        $this->expectException(ProductConflict::class);

        (new ActivateProduct($products, new EnsureCompanyCanReceiveProducts($companies)))->execute($product->id());
    }

    public function test_restores_product_as_inactive_when_company_is_inactive(): void
    {
        [$companies, $products] = $this->repositories();
        $product = $products->save($this->product());
        $products->softDelete($product->id());
        $company = $companies->findById(1);
        self::assertNotNull($company);
        $company->deactivate();
        $companies->save($company);

        $restored = (new RestoreProduct($products, $companies))->execute($product->id());

        self::assertFalse($restored->isDeleted());
        self::assertSame(ProductStatus::Inactive, $restored->status());
    }

    public function test_rejects_restore_while_company_is_soft_deleted(): void
    {
        [$companies, $products] = $this->repositories();
        $product = $products->save($this->product());
        $products->softDelete($product->id());
        $companies->softDelete(1);

        $this->expectException(ProductConflict::class);

        (new RestoreProduct($products, $companies))->execute($product->id());
    }

    public function test_force_delete_requires_confirmation_and_prior_soft_delete(): void
    {
        [, $products] = $this->repositories();
        $product = $products->save($this->product());
        $useCase = new ForceDeleteProduct($products);

        try {
            $useCase->execute($product->id(), true);
            self::fail('A exclusão definitiva deveria exigir exclusão lógica prévia.');
        } catch (ProductConflict) {
            self::assertNotNull($products->findById($product->id()));
        }

        $products->softDelete($product->id());

        try {
            $useCase->execute($product->id(), false);
            self::fail('A exclusão definitiva deveria exigir confirmação.');
        } catch (ProductConflict) {
            self::assertNotNull($products->findById($product->id(), true));
        }

        $useCase->execute($product->id(), true);

        self::assertNull($products->findById($product->id(), true));
    }

    private function repositories(): array
    {
        $companies = new InMemoryCompanyRepository;
        $companies->save(Company::create(
            'Empresa Exemplo',
            new Cnpj('11222333000181'),
            new EmailAddress('contato@exemplo.com'),
            new PhoneNumber('71999999999'),
        ));

        return [$companies, new InMemoryProductRepository];
    }

    private function product(ProductStatus $status = ProductStatus::Active): Product
    {
        return Product::create(
            1,
            'Produto Exemplo',
            null,
            new Price('10.00'),
            new InternalCode('PROD-001'),
            $status,
        );
    }
}
