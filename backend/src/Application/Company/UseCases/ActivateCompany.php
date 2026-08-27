<?php

declare(strict_types=1);

namespace Application\Company\UseCases;

use Application\Company\Exceptions\CompanyNotFound;
use Domain\Company\Entities\Company;
use Domain\Company\Repositories\CompanyRepository;

final readonly class ActivateCompany
{
    public function __construct(private CompanyRepository $companies) {}

    public function execute(int $id): Company
    {
        $company = $this->companies->findById($id) ?? throw new CompanyNotFound;
        $company->activate();

        return $this->companies->save($company);
    }
}
