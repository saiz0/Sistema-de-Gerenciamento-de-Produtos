<?php

declare(strict_types=1);

namespace Application\Product\UseCases;

use Application\Product\Exceptions\ProductNotFound;
use Domain\Product\Entities\Product;
use Domain\Product\Repositories\ProductRepository;

final readonly class GetProduct
{
    public function __construct(private ProductRepository $products) {}

    public function execute(int $id): Product
    {
        return $this->products->findById($id) ?? throw new ProductNotFound;
    }
}
