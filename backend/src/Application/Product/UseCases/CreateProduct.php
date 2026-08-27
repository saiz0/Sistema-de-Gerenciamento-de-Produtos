<?php

declare(strict_types=1);

namespace Application\Product\UseCases;

use Application\Product\DTOs\CreateProductData;
use Application\Product\Services\EnsureCompanyCanReceiveProducts;
use Application\Product\Services\EnsureInternalCodeIsUnique;
use Domain\Product\Entities\Product;
use Domain\Product\Repositories\ProductRepository;
use Domain\Product\ValueObjects\InternalCode;
use Domain\Product\ValueObjects\Price;

final readonly class CreateProduct
{
    public function __construct(
        private ProductRepository $products,
        private EnsureCompanyCanReceiveProducts $ensureCompany,
        private EnsureInternalCodeIsUnique $ensureUnique,
    ) {}

    public function execute(CreateProductData $data): Product
    {
        $this->ensureCompany->handle($data->companyId);
        $internalCode = new InternalCode($data->internalCode);
        $this->ensureUnique->handle($data->companyId, $internalCode);

        return $this->products->save(Product::create(
            companyId: $data->companyId,
            name: $data->name,
            description: $data->description,
            price: new Price($data->price),
            internalCode: $internalCode,
            status: $data->status,
        ));
    }
}
