<?php

declare(strict_types=1);

namespace Application\Product\UseCases;

use Application\Product\Exceptions\ProductConflict;
use Application\Product\Exceptions\ProductNotFound;
use Domain\Company\Enums\CompanyStatus;
use Domain\Company\Repositories\CompanyRepository;
use Domain\Product\Entities\Product;
use Domain\Product\Enums\ProductStatus;
use Domain\Product\Repositories\ProductRepository;

final readonly class RestoreProduct
{
    public function __construct(
        private ProductRepository $products,
        private CompanyRepository $companies,
    ) {}

    public function execute(int $id): Product
    {
        $product = $this->products->findById($id, true) ?? throw new ProductNotFound;

        if (! $product->isDeleted()) {
            return $product;
        }

        $company = $this->companies->findById($product->companyId(), true);

        if ($company === null || $company->isDeleted()) {
            throw new ProductConflict('Não é permitido restaurar produto de uma empresa inexistente ou excluída.');
        }

        if ($company->status() === CompanyStatus::Inactive && $product->status() === ProductStatus::Active) {
            $product->deactivate();
            $this->products->save($product);
        }

        $this->products->restore($id);

        return $this->products->findById($id) ?? throw new ProductNotFound;
    }
}
