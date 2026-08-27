<?php

declare(strict_types=1);

namespace Application\Product\UseCases;

use Application\Product\Exceptions\ProductNotFound;
use Application\Product\Services\EnsureCompanyCanReceiveProducts;
use Domain\Product\Entities\Product;
use Domain\Product\Repositories\ProductRepository;

final readonly class ActivateProduct
{
    public function __construct(
        private ProductRepository $products,
        private EnsureCompanyCanReceiveProducts $ensureCompany,
    ) {}

    public function execute(int $id): Product
    {
        $product = $this->products->findById($id) ?? throw new ProductNotFound;
        $this->ensureCompany->handle($product->companyId());
        $product->activate();

        return $this->products->save($product);
    }
}
