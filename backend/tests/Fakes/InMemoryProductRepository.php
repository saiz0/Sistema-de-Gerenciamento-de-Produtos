<?php

declare(strict_types=1);

namespace Tests\Fakes;

use DateTimeImmutable;
use Domain\Product\Collections\ProductPage;
use Domain\Product\Entities\Product;
use Domain\Product\Enums\DeletedFilter;
use Domain\Product\Enums\ProductStatus;
use Domain\Product\Repositories\ProductRepository;
use Domain\Product\ValueObjects\InternalCode;

final class InMemoryProductRepository implements ProductRepository
{
    /** @var array<int, Product> */
    private array $products = [];

    private int $nextId = 1;

    public function save(Product $product): Product
    {
        $id = $product->id() ?? $this->nextId++;
        $stored = $this->copy($product, $id, $product->deletedAt(), $product->deletedByCompanyAt());
        $this->products[$id] = $stored;

        return $stored;
    }

    public function findById(int $id, bool $withDeleted = false): ?Product
    {
        $product = $this->products[$id] ?? null;

        if ($product?->isDeleted() && ! $withDeleted) {
            return null;
        }

        return $product;
    }

    public function search(
        ?string $name,
        ?ProductStatus $status,
        ?int $companyId,
        DeletedFilter $deleted,
        int $page,
        int $perPage,
    ): ProductPage {
        $items = array_values(array_filter($this->products, function (Product $product) use ($name, $status, $companyId, $deleted): bool {
            $matchesDeleted = match ($deleted) {
                DeletedFilter::Without => ! $product->isDeleted(),
                DeletedFilter::Only => $product->isDeleted(),
                DeletedFilter::With => true,
            };

            return $matchesDeleted
                && ($name === null || str_contains(mb_strtolower($product->name()), mb_strtolower($name)))
                && ($status === null || $product->status() === $status)
                && ($companyId === null || $product->companyId() === $companyId);
        }));
        $total = count($items);

        return new ProductPage(
            array_slice($items, ($page - 1) * $perPage, $perPage),
            $page,
            $perPage,
            $total,
            max(1, (int) ceil($total / $perPage)),
        );
    }

    public function existsByInternalCode(
        int $companyId,
        InternalCode $internalCode,
        ?int $ignoreId = null,
    ): bool {
        foreach ($this->products as $id => $product) {
            if ($id !== $ignoreId
                && $product->companyId() === $companyId
                && $product->internalCode()->value() === $internalCode->value()) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyForCompany(int $companyId): bool
    {
        foreach ($this->products as $product) {
            if ($product->companyId() === $companyId) {
                return true;
            }
        }

        return false;
    }

    public function softDelete(int $id): void
    {
        $this->products[$id] = $this->copy($this->products[$id], $id, new DateTimeImmutable, null);
    }

    public function restore(int $id): void
    {
        $this->products[$id] = $this->copy($this->products[$id], $id, null, null);
    }

    public function forceDelete(int $id): void
    {
        unset($this->products[$id]);
    }

    public function deactivateByCompany(int $companyId): void
    {
        foreach ($this->products as $product) {
            if ($product->companyId() === $companyId) {
                $product->deactivate();
            }
        }
    }

    public function softDeleteByCompany(int $companyId): void
    {
        $deletedAt = new DateTimeImmutable;

        foreach ($this->products as $id => $product) {
            if ($product->companyId() === $companyId && ! $product->isDeleted()) {
                $this->products[$id] = $this->copy($product, $id, $deletedAt, $deletedAt);
            }
        }
    }

    public function restoreDeletedByCompany(int $companyId): void
    {
        foreach ($this->products as $id => $product) {
            if ($product->companyId() === $companyId && $product->wasDeletedByCompany()) {
                $this->products[$id] = $this->copy($product, $id, null, null);
            }
        }
    }

    private function copy(
        Product $product,
        int $id,
        ?DateTimeImmutable $deletedAt,
        ?DateTimeImmutable $deletedByCompanyAt,
    ): Product {
        return new Product(
            $id,
            $product->companyId(),
            $product->name(),
            $product->description(),
            $product->price(),
            $product->internalCode(),
            $product->status(),
            $product->createdAt(),
            $product->updatedAt(),
            $deletedAt,
            $deletedByCompanyAt,
        );
    }
}
