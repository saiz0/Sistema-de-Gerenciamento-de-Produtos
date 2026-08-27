<?php

declare(strict_types=1);

namespace Presentation\Http\Resources;

use Domain\Product\Entities\Product;

final class ProductResource
{
    public static function make(Product $product): array
    {
        return [
            'id' => $product->id(),
            'company_id' => $product->companyId(),
            'name' => $product->name(),
            'description' => $product->description(),
            'price' => $product->price()->value(),
            'internal_code' => $product->internalCode()->value(),
            'status' => $product->status()->value,
            'created_at' => $product->createdAt()?->format(DATE_ATOM),
            'updated_at' => $product->updatedAt()?->format(DATE_ATOM),
            'deleted_at' => $product->deletedAt()?->format(DATE_ATOM),
        ];
    }

    private function __construct() {}
}
