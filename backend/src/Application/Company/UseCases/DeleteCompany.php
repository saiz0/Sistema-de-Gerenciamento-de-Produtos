<?php

declare(strict_types=1);

namespace Application\Company\UseCases;

use Application\Company\Exceptions\CompanyNotFound;
use Application\Shared\Contracts\TransactionManager;
use Domain\Company\Repositories\CompanyRepository;
use Domain\Product\Repositories\ProductRepository;

final readonly class DeleteCompany
{
    public function __construct(
        private CompanyRepository $companies,
        private ProductRepository $products,
        private TransactionManager $transaction,
    ) {}

    public function execute(int $id): void
    {
        $this->transaction->run(function () use ($id): void {
            $company = $this->companies->findById($id, true) ?? throw new CompanyNotFound;

            if ($company->isDeleted()) {
                return;
            }

            $this->products->softDeleteByCompany($id);
            $this->companies->softDelete($id);
        });
    }
}
