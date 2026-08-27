<?php

declare(strict_types=1);

namespace Application\Product\Services;

use Application\Product\Exceptions\ProductConflict;
use Domain\Product\Repositories\ProductRepository;
use Domain\Product\ValueObjects\InternalCode;

final readonly class EnsureInternalCodeIsUnique
{
    public function __construct(private ProductRepository $products) {}

    public function handle(int $companyId, InternalCode $internalCode, ?int $ignoreId = null): void
    {
        if ($this->products->existsByInternalCode($companyId, $internalCode, $ignoreId)) {
            throw new ProductConflict('Já existe um produto com este código interno na empresa informada.');
        }
    }
}
