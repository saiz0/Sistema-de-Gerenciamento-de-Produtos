<?php

declare(strict_types=1);

namespace Domain\Product\Repositories;

use Domain\Product\Collections\ProductPage;
use Domain\Product\Entities\Product;
use Domain\Product\Enums\DeletedFilter;
use Domain\Product\Enums\ProductStatus;
use Domain\Product\ValueObjects\InternalCode;

interface ProductRepository
{
    public function save(Product $product): Product;

    public function findById(int $id, bool $withDeleted = false): ?Product;

    public function search(
        ?string $name,
        ?ProductStatus $status,
        ?int $companyId,
        DeletedFilter $deleted,
        int $page,
        int $perPage,
    ): ProductPage;

    public function existsByInternalCode(
        int $companyId,
        InternalCode $internalCode,
        ?int $ignoreId = null,
    ): bool;

    public function hasAnyForCompany(int $companyId): bool;

    public function softDelete(int $id): void;

    public function restore(int $id): void;

    public function forceDelete(int $id): void;

    public function deactivateByCompany(int $companyId): void;

    public function softDeleteByCompany(int $companyId): void;

    public function restoreDeletedByCompany(int $companyId): void;
}
