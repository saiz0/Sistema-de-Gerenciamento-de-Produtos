<?php

declare(strict_types=1);

namespace Domain\Company\Repositories;

use Domain\Company\Collections\CompanyPage;
use Domain\Company\Entities\Company;
use Domain\Company\Enums\CompanyStatus;
use Domain\Company\Enums\DeletedFilter;
use Domain\Company\ValueObjects\Cnpj;

interface CompanyRepository
{
    public function save(Company $company): Company;

    public function findById(int $id, bool $withDeleted = false): ?Company;

    public function search(
        ?string $name,
        ?CompanyStatus $status,
        DeletedFilter $deleted,
        int $page,
        int $perPage,
    ): CompanyPage;

    public function existsByCnpj(Cnpj $cnpj, ?int $ignoreId = null): bool;

    public function softDelete(int $id): void;

    public function restore(int $id): void;

    public function forceDelete(int $id): void;
}
