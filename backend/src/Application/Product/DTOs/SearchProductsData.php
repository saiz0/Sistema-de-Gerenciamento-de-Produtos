<?php

declare(strict_types=1);

namespace Application\Product\DTOs;

use Domain\Product\Enums\DeletedFilter;
use Domain\Product\Enums\ProductStatus;

final readonly class SearchProductsData
{
    public function __construct(
        public ?string $name = null,
        public ?ProductStatus $status = null,
        public ?int $companyId = null,
        public DeletedFilter $deleted = DeletedFilter::Without,
        public int $page = 1,
        public int $perPage = 15,
    ) {}
}
