<?php

declare(strict_types=1);

namespace Application\Product\Services;

use Application\Product\Exceptions\ProductConflict;
use Domain\Company\Enums\CompanyStatus;
use Domain\Company\Repositories\CompanyRepository;

final readonly class EnsureCompanyCanReceiveProducts
{
    public function __construct(private CompanyRepository $companies) {}

    public function handle(int $companyId): void
    {
        $company = $this->companies->findById($companyId, true);

        if ($company === null) {
            throw new ProductConflict('A empresa vinculada não foi encontrada.');
        }

        if ($company->isDeleted()) {
            throw new ProductConflict('Não é permitido vincular produto a uma empresa excluída.');
        }

        if ($company->status() !== CompanyStatus::Active) {
            throw new ProductConflict('Não é permitido vincular produto a uma empresa inativa.');
        }
    }
}
