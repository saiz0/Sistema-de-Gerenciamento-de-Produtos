<?php

declare(strict_types=1);

namespace Domain\Product\Collections;

use Domain\Product\Entities\Product;

final readonly class ProductPage
{
    /** @param list<Product> $items */
    public function __construct(
        public array $items,
        public int $currentPage,
        public int $perPage,
        public int $total,
        public int $lastPage,
    ) {}
}
