<?php

declare(strict_types=1);

namespace Application\Product\UseCases;

use Application\Product\DTOs\UpdateProductData;
use Application\Product\Exceptions\ProductNotFound;
use Application\Product\Services\EnsureCompanyCanReceiveProducts;
use Application\Product\Services\EnsureInternalCodeIsUnique;
use Domain\Product\Entities\Product;
use Domain\Product\Repositories\ProductRepository;
use Domain\Product\ValueObjects\InternalCode;
use Domain\Product\ValueObjects\Price;

final readonly class UpdateProduct
{
    public function __construct(
        private ProductRepository $products,
        private EnsureCompanyCanReceiveProducts $ensureCompany,
        private EnsureInternalCodeIsUnique $ensureUnique,
    ) {}

    public function execute(int $id, UpdateProductData $data): Product
    {
        $product = $this->products->findById($id) ?? throw new ProductNotFound;
        $this->ensureCompany->handle($data->companyId);
        $internalCode = new InternalCode($data->internalCode);
        $this->ensureUnique->handle($data->companyId, $internalCode, $id);

        $product->update(
            $data->companyId,
            $data->name,
            $data->description,
            new Price($data->price),
            $internalCode,
        );

        return $this->products->save($product);
    }
}
