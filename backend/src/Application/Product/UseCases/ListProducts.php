<?php

declare(strict_types=1);

namespace Application\Product\UseCases;

use Application\Product\DTOs\SearchProductsData;
use Domain\Product\Collections\ProductPage;
use Domain\Product\Repositories\ProductRepository;

final readonly class ListProducts
{
    public function __construct(private ProductRepository $products) {}

    public function execute(SearchProductsData $data): ProductPage
    {
        return $this->products->search(
            $data->name,
            $data->status,
            $data->companyId,
            $data->deleted,
            $data->page,
            $data->perPage,
        );
    }
}
