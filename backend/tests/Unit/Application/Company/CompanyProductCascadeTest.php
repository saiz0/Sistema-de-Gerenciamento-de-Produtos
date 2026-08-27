<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Company;

use Application\Company\Exceptions\CompanyConflict;
use Application\Company\UseCases\ActivateCompany;
use Application\Company\UseCases\DeactivateCompany;
use Application\Company\UseCases\DeleteCompany;
use Application\Company\UseCases\ForceDeleteCompany;
use Application\Company\UseCases\RestoreCompany;
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
use Tests\Fakes\InMemoryTransactionManager;

final class CompanyProductCascadeTest extends TestCase
{
    public function test_deactivates_products_without_reactivating_them_with_the_company(): void
    {
        [$companies, $products, $transaction] = $this->repositoriesWithCompany();
        $product = $products->save($this->product());
        $softDeletedProduct = $products->save($this->product('PROD-002'));
        $products->softDelete($softDeletedProduct->id());

        (new DeactivateCompany($companies, $products, $transaction))->execute(1);

        self::assertSame(CompanyStatus::Inactive, $companies->findById(1)?->status());
        self::assertSame(ProductStatus::Inactive, $products->findById($product->id())?->status());
        self::assertSame(
            ProductStatus::Inactive,
            $products->findById($softDeletedProduct->id(), true)?->status(),
        );

        (new ActivateCompany($companies))->execute(1);

        self::assertSame(CompanyStatus::Active, $companies->findById(1)?->status());
        self::assertSame(ProductStatus::Inactive, $products->findById($product->id())?->status());
        self::assertSame(
            ProductStatus::Inactive,
            $products->findById($softDeletedProduct->id(), true)?->status(),
        );
    }

    public function test_restores_only_products_deleted_with_the_company(): void
    {
        [$companies, $products, $transaction] = $this->repositoriesWithCompany();
        $individuallyDeleted = $products->save($this->product('PROD-001'));
        $deletedWithCompany = $products->save($this->product('PROD-002'));
        $products->softDelete($individuallyDeleted->id());

        (new DeleteCompany($companies, $products, $transaction))->execute(1);

        self::assertTrue($products->findById($individuallyDeleted->id(), true)?->isDeleted());
        self::assertFalse($products->findById($individuallyDeleted->id(), true)?->wasDeletedByCompany());
        self::assertTrue($products->findById($deletedWithCompany->id(), true)?->wasDeletedByCompany());

        (new RestoreCompany($companies, $products, $transaction))->execute(1);

        self::assertNull($products->findById($individuallyDeleted->id()));
        self::assertNotNull($products->findById($deletedWithCompany->id()));
        self::assertFalse($products->findById($deletedWithCompany->id())?->isDeleted());
    }

    public function test_blocks_force_delete_when_company_has_soft_deleted_products(): void
    {
        [$companies, $products, $transaction] = $this->repositoriesWithCompany();
        $products->save($this->product());
        (new DeleteCompany($companies, $products, $transaction))->execute(1);

        $this->expectException(CompanyConflict::class);

        (new ForceDeleteCompany($companies, $products, $transaction))->execute(1, true);
    }

    private function repositoriesWithCompany(): array
    {
        $companies = new InMemoryCompanyRepository;
        $companies->save(Company::create(
            'Empresa Exemplo',
            new Cnpj('11222333000181'),
            new EmailAddress('contato@exemplo.com'),
            new PhoneNumber('71999999999'),
        ));

        return [
            $companies,
            new InMemoryProductRepository,
            new InMemoryTransactionManager,
        ];
    }

    private function product(string $internalCode = 'PROD-001'): Product
    {
        return Product::create(
            1,
            'Produto Exemplo',
            null,
            new Price('10.00'),
            new InternalCode($internalCode),
        );
    }
}
