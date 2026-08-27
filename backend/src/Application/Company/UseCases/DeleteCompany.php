<?php

declare(strict_types=1);

namespace Application\Company\UseCases;

use Application\Company\Exceptions\CompanyNotFound;
use Domain\Company\Repositories\CompanyRepository;

final readonly class DeleteCompany
{
    public function __construct(private CompanyRepository $companies) {}

    public function execute(int $id): void
    {
        $company = $this->companies->findById($id, true) ?? throw new CompanyNotFound;

        if ($company->isDeleted()) {
            return;
        }

        $this->companies->softDelete($id);
    }
}
