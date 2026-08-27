<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Product\Collections\ProductPage;
use Domain\Product\Entities\Product;
use Domain\Product\Enums\DeletedFilter;
use Domain\Product\Enums\ProductStatus;
use Domain\Product\Repositories\ProductRepository;
use Domain\Product\ValueObjects\InternalCode;
use Domain\Product\ValueObjects\Price;
use Illuminate\Database\Eloquent\Builder;
use Infrastructure\Persistence\Eloquent\Models\ProductModel;

final class EloquentProductRepository implements ProductRepository
{
    public function save(Product $product): Product
    {
        $model = $product->id() === null
            ? new ProductModel
            : ProductModel::withTrashed()->findOrFail($product->id());

        $model->fill([
            'company_id' => $product->companyId(),
            'name' => $product->name(),
            'description' => $product->description(),
            'price' => $product->price()->value(),
            'internal_code' => $product->internalCode()->value(),
            'status' => $product->status(),
        ]);
        $model->save();

        return $this->toEntity($model->fresh());
    }

    public function findById(int $id, bool $withDeleted = false): ?Product
    {
        $query = ProductModel::query();

        if ($withDeleted) {
            $query->withTrashed();
        }

        $model = $query->find($id);

        return $model === null ? null : $this->toEntity($model);
    }

    public function search(
        ?string $name,
        ?ProductStatus $status,
        ?int $companyId,
        DeletedFilter $deleted,
        int $page,
        int $perPage,
    ): ProductPage {
        $query = ProductModel::query();

        match ($deleted) {
            DeletedFilter::Only => $query->onlyTrashed(),
            DeletedFilter::With => $query->withTrashed(),
            DeletedFilter::Without => null,
        };

        $query
            ->when($name !== null, fn (Builder $builder) => $builder->where('name', 'ilike', '%'.$name.'%'))
            ->when($status !== null, fn (Builder $builder) => $builder->where('status', $status->value))
            ->when($companyId !== null, fn (Builder $builder) => $builder->where('company_id', $companyId))
            ->orderBy('name')
            ->orderBy('id');

        $paginator = $query->paginate(perPage: $perPage, page: $page);

        return new ProductPage(
            items: array_map($this->toEntity(...), $paginator->items()),
            currentPage: $paginator->currentPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
            lastPage: $paginator->lastPage(),
        );
    }

    public function existsByInternalCode(
        int $companyId,
        InternalCode $internalCode,
        ?int $ignoreId = null,
    ): bool {
        return ProductModel::withTrashed()
            ->where('company_id', $companyId)
            ->where('internal_code', $internalCode->value())
            ->when($ignoreId !== null, fn (Builder $query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }

    public function hasAnyForCompany(int $companyId): bool
    {
        return ProductModel::withTrashed()->where('company_id', $companyId)->exists();
    }

    public function softDelete(int $id): void
    {
        ProductModel::query()->findOrFail($id)->delete();
    }

    public function restore(int $id): void
    {
        $model = ProductModel::onlyTrashed()->findOrFail($id);
        $model->deleted_by_company_at = null;
        $model->restore();
    }

    public function forceDelete(int $id): void
    {
        ProductModel::onlyTrashed()->findOrFail($id)->forceDelete();
    }

    public function deactivateByCompany(int $companyId): void
    {
        ProductModel::withTrashed()
            ->where('company_id', $companyId)
            ->update([
                'status' => ProductStatus::Inactive->value,
                'updated_at' => now(),
            ]);
    }

    public function softDeleteByCompany(int $companyId): void
    {
        $deletedAt = now();

        ProductModel::query()
            ->where('company_id', $companyId)
            ->update([
                'deleted_at' => $deletedAt,
                'deleted_by_company_at' => $deletedAt,
                'updated_at' => $deletedAt,
            ]);
    }

    public function restoreDeletedByCompany(int $companyId): void
    {
        ProductModel::onlyTrashed()
            ->where('company_id', $companyId)
            ->whereNotNull('deleted_by_company_at')
            ->update([
                'deleted_at' => null,
                'deleted_by_company_at' => null,
                'updated_at' => now(),
            ]);
    }

    private function toEntity(ProductModel $model): Product
    {
        return new Product(
            id: $model->getKey(),
            companyId: $model->company_id,
            name: $model->name,
            description: $model->description,
            price: new Price((string) $model->price),
            internalCode: new InternalCode($model->internal_code),
            status: $model->status,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
            deletedByCompanyAt: $model->deleted_by_company_at,
        );
    }
}
