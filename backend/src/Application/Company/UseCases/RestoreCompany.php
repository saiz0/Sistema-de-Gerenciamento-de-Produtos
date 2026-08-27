<?php

declare(strict_types=1);

namespace Application\Company\UseCases;

use Application\Company\Exceptions\CompanyNotFound;
use Application\Shared\Contracts\TransactionManager;
use Domain\Company\Entities\Company;
use Domain\Company\Repositories\CompanyRepository;
use Domain\Product\Repositories\ProductRepository;

final readonly class RestoreCompany
{
    public function __construct(
        private CompanyRepository $companies,
        private ProductRepository $products,
        private TransactionManager $transaction,
    ) {}

    public function execute(int $id): Company
    {
        return $this->transaction->run(function () use ($id): Company {
            $company = $this->companies->findById($id, true) ?? throw new CompanyNotFound;

            if (! $company->isDeleted()) {
                return $company;
            }

            $this->companies->restore($id);
            $this->products->restoreDeletedByCompany($id);

            return $this->companies->findById($id) ?? throw new CompanyNotFound;
        });
    }
}
