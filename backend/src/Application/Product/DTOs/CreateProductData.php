<?php

declare(strict_types=1);

namespace Application\Product\DTOs;

use Domain\Product\Enums\ProductStatus;

final readonly class CreateProductData
{
    public function __construct(
        public int $companyId,
        public string $name,
        public ?string $description,
        public string $price,
        public string $internalCode,
        public ProductStatus $status,
    ) {}
}
