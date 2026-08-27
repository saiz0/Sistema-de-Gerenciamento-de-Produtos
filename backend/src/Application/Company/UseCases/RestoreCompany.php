<?php

declare(strict_types=1);

namespace Application\Company\UseCases;

use Application\Company\Exceptions\CompanyNotFound;
use Domain\Company\Entities\Company;
use Domain\Company\Repositories\CompanyRepository;

final readonly class RestoreCompany
{
    public function __construct(private CompanyRepository $companies) {}

    public function execute(int $id): Company
    {
        $company = $this->companies->findById($id, true) ?? throw new CompanyNotFound;

        if (! $company->isDeleted()) {
            return $company;
        }

        $this->companies->restore($id);

        return $this->companies->findById($id) ?? throw new CompanyNotFound;
    }
}
