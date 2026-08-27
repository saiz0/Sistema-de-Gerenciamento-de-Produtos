<?php

declare(strict_types=1);

namespace Application\Company\UseCases;

use Application\Company\Exceptions\CompanyConflict;
use Application\Company\Exceptions\CompanyNotFound;
use Domain\Company\Repositories\CompanyRepository;

final readonly class ForceDeleteCompany
{
    public function __construct(private CompanyRepository $companies) {}

    public function execute(int $id, bool $confirmed): void
    {
        $company = $this->companies->findById($id, true) ?? throw new CompanyNotFound;

        if (! $confirmed) {
            throw new CompanyConflict('A exclusão definitiva exige confirmação explícita.');
        }

        if (! $company->isDeleted()) {
            throw new CompanyConflict('A empresa precisa estar excluída logicamente antes da exclusão definitiva.');
        }

        $this->companies->forceDelete($id);
    }
}
