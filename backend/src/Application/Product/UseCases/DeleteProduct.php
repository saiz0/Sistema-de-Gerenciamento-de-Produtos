<?php

declare(strict_types=1);

namespace Application\Product\UseCases;

use Application\Product\Exceptions\ProductNotFound;
use Domain\Product\Repositories\ProductRepository;

final readonly class DeleteProduct
{
    public function __construct(private ProductRepository $products) {}

    public function execute(int $id): void
    {
        $product = $this->products->findById($id, true) ?? throw new ProductNotFound;

        if ($product->isDeleted()) {
            return;
        }

        $this->products->softDelete($id);
    }
}
