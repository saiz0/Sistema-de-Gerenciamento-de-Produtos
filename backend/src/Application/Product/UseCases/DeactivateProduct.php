<?php

declare(strict_types=1);

namespace Application\Product\UseCases;

use Application\Product\Exceptions\ProductNotFound;
use Domain\Product\Entities\Product;
use Domain\Product\Repositories\ProductRepository;

final readonly class DeactivateProduct
{
    public function __construct(private ProductRepository $products) {}

    public function execute(int $id): Product
    {
        $product = $this->products->findById($id) ?? throw new ProductNotFound;
        $product->deactivate();

        return $this->products->save($product);
    }
}
