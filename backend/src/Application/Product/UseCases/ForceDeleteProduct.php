<?php

declare(strict_types=1);

namespace Application\Product\UseCases;

use Application\Product\Exceptions\ProductConflict;
use Application\Product\Exceptions\ProductNotFound;
use Domain\Product\Repositories\ProductRepository;

final readonly class ForceDeleteProduct
{
    public function __construct(private ProductRepository $products) {}

    public function execute(int $id, bool $confirmed): void
    {
        $product = $this->products->findById($id, true) ?? throw new ProductNotFound;

        if (! $confirmed) {
            throw new ProductConflict('A exclusão definitiva exige confirmação explícita.');
        }

        if (! $product->isDeleted()) {
            throw new ProductConflict('O produto precisa estar excluído logicamente antes da exclusão definitiva.');
        }

        $this->products->forceDelete($id);
    }
}
