<?php

declare(strict_types=1);

namespace Application\Product\DTOs;

final readonly class UpdateProductData
{
    public function __construct(
        public int $companyId,
        public string $name,
        public ?string $description,
        public string $price,
        public string $internalCode,
    ) {}
}
