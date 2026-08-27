<?php

declare(strict_types=1);

namespace Tests\Fakes;

use DateTimeImmutable;
use Domain\Company\Collections\CompanyPage;
use Domain\Company\Entities\Company;
use Domain\Company\Enums\CompanyStatus;
use Domain\Company\Enums\DeletedFilter;
use Domain\Company\Repositories\CompanyRepository;
use Domain\Company\ValueObjects\Cnpj;
use Domain\Company\ValueObjects\EmailAddress;

final class InMemoryCompanyRepository implements CompanyRepository
{
    /** @var array<int, Company> */
    private array $companies = [];

    private int $nextId = 1;

    public function save(Company $company): Company
    {
        $id = $company->id() ?? $this->nextId++;
        $stored = $this->copy($company, $id, $company->deletedAt());
        $this->companies[$id] = $stored;

        return $stored;
    }

    public function findById(int $id, bool $withDeleted = false): ?Company
    {
        $company = $this->companies[$id] ?? null;

        if ($company?->isDeleted() && ! $withDeleted) {
            return null;
        }

        return $company;
    }

    public function search(
        ?string $name,
        ?CompanyStatus $status,
        DeletedFilter $deleted,
        int $page,
        int $perPage,
    ): CompanyPage {
        $items = array_values(array_filter($this->companies, function (Company $company) use ($name, $status, $deleted): bool {
            $matchesDeleted = match ($deleted) {
                DeletedFilter::Without => ! $company->isDeleted(),
                DeletedFilter::Only => $company->isDeleted(),
                DeletedFilter::With => true,
            };

            return $matchesDeleted
                && ($name === null || str_contains(mb_strtolower($company->name()), mb_strtolower($name)))
                && ($status === null || $company->status() === $status);
        }));
        $total = count($items);

        return new CompanyPage(
            array_slice($items, ($page - 1) * $perPage, $perPage),
            $page,
            $perPage,
            $total,
            max(1, (int) ceil($total / $perPage)),
        );
    }

    public function existsByCnpj(Cnpj $cnpj, ?int $ignoreId = null): bool
    {
        return $this->exists(fn (Company $company) => $company->cnpj()->value() === $cnpj->value(), $ignoreId);
    }

    public function existsByEmail(EmailAddress $email, ?int $ignoreId = null): bool
    {
        return $this->exists(fn (Company $company) => $company->email()->value() === $email->value(), $ignoreId);
    }

    public function softDelete(int $id): void
    {
        $this->companies[$id] = $this->copy($this->companies[$id], $id, new DateTimeImmutable);
    }

    public function restore(int $id): void
    {
        $this->companies[$id] = $this->copy($this->companies[$id], $id, null);
    }

    public function forceDelete(int $id): void
    {
        unset($this->companies[$id]);
    }

    private function exists(callable $condition, ?int $ignoreId): bool
    {
        foreach ($this->companies as $id => $company) {
            if ($id !== $ignoreId && $condition($company)) {
                return true;
            }
        }

        return false;
    }

    private function copy(Company $company, int $id, ?DateTimeImmutable $deletedAt): Company
    {
        return new Company(
            $id,
            $company->name(),
            $company->cnpj(),
            $company->email(),
            $company->phone(),
            $company->status(),
            $company->createdAt(),
            $company->updatedAt(),
            $deletedAt,
        );
    }
}
