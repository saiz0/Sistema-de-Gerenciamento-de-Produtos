<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Repositories;

use Domain\Company\Collections\CompanyPage;
use Domain\Company\Entities\Company;
use Domain\Company\Enums\CompanyStatus;
use Domain\Company\Enums\DeletedFilter;
use Domain\Company\Repositories\CompanyRepository;
use Domain\Company\ValueObjects\Cnpj;
use Domain\Company\ValueObjects\EmailAddress;
use Domain\Company\ValueObjects\PhoneNumber;
use Illuminate\Database\Eloquent\Builder;
use Infrastructure\Persistence\Eloquent\Models\CompanyModel;

final class EloquentCompanyRepository implements CompanyRepository
{
    public function save(Company $company): Company
    {
        $model = $company->id() === null
            ? new CompanyModel
            : CompanyModel::withTrashed()->findOrFail($company->id());

        $model->fill([
            'name' => $company->name(),
            'cnpj' => $company->cnpj()->value(),
            'email' => $company->email()->value(),
            'phone' => $company->phone()->value(),
            'status' => $company->status(),
        ]);
        $model->save();

        return $this->toEntity($model->fresh());
    }

    public function findById(int $id, bool $withDeleted = false): ?Company
    {
        $query = CompanyModel::query();

        if ($withDeleted) {
            $query->withTrashed();
        }

        $model = $query->find($id);

        return $model === null ? null : $this->toEntity($model);
    }

    public function search(
        ?string $name,
        ?CompanyStatus $status,
        DeletedFilter $deleted,
        int $page,
        int $perPage,
    ): CompanyPage {
        $query = CompanyModel::query();

        match ($deleted) {
            DeletedFilter::Only => $query->onlyTrashed(),
            DeletedFilter::With => $query->withTrashed(),
            DeletedFilter::Without => null,
        };

        $query
            ->when($name !== null, fn (Builder $builder) => $builder->where('name', 'ilike', '%'.$name.'%'))
            ->when($status !== null, fn (Builder $builder) => $builder->where('status', $status->value))
            ->orderBy('name')
            ->orderBy('id');

        $paginator = $query->paginate(perPage: $perPage, page: $page);

        return new CompanyPage(
            items: array_map($this->toEntity(...), $paginator->items()),
            currentPage: $paginator->currentPage(),
            perPage: $paginator->perPage(),
            total: $paginator->total(),
            lastPage: $paginator->lastPage(),
        );
    }

    public function existsByCnpj(Cnpj $cnpj, ?int $ignoreId = null): bool
    {
        return $this->uniqueQuery($ignoreId)->where('cnpj', $cnpj->value())->exists();
    }

    public function softDelete(int $id): void
    {
        CompanyModel::query()->findOrFail($id)->delete();
    }

    public function restore(int $id): void
    {
        CompanyModel::onlyTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete(int $id): void
    {
        CompanyModel::onlyTrashed()->findOrFail($id)->forceDelete();
    }

    private function uniqueQuery(?int $ignoreId): Builder
    {
        return CompanyModel::withTrashed()
            ->when($ignoreId !== null, fn (Builder $query) => $query->whereKeyNot($ignoreId));
    }

    private function toEntity(CompanyModel $model): Company
    {
        return new Company(
            id: $model->getKey(),
            name: $model->name,
            cnpj: new Cnpj($model->cnpj),
            email: new EmailAddress($model->email),
            phone: new PhoneNumber($model->phone),
            status: $model->status,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            deletedAt: $model->deleted_at,
        );
    }
}
