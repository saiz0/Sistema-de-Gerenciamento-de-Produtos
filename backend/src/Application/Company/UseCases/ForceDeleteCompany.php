<?php

declare(strict_types=1);

namespace Application\Company\UseCases;

use Application\Company\Exceptions\CompanyConflict;
use Application\Company\Exceptions\CompanyNotFound;
use Application\Shared\Contracts\TransactionManager;
use Domain\Company\Repositories\CompanyRepository;
use Domain\Product\Repositories\ProductRepository;

final readonly class ForceDeleteCompany
{
    public function __construct(
        private CompanyRepository $companies,
        private ProductRepository $products,
        private TransactionManager $transaction,
    ) {}

    public function execute(int $id, bool $confirmed): void
    {
        $this->transaction->run(function () use ($id, $confirmed): void {
            $company = $this->companies->findById($id, true) ?? throw new CompanyNotFound;

            if (! $confirmed) {
                throw new CompanyConflict('A exclusão definitiva exige confirmação explícita.');
            }

            if (! $company->isDeleted()) {
                throw new CompanyConflict('A empresa precisa estar excluída logicamente antes da exclusão definitiva.');
            }

            if ($this->products->hasAnyForCompany($id)) {
                throw new CompanyConflict('Não é possível excluir definitivamente uma empresa com produtos vinculados.');
            }

            $this->companies->forceDelete($id);
        });
    }
}
