<?php

declare(strict_types=1);

namespace Application\Company\Services;

use Application\Company\Exceptions\CompanyConflict;
use Domain\Company\Repositories\CompanyRepository;
use Domain\Company\ValueObjects\Cnpj;
use Domain\Company\ValueObjects\EmailAddress;

final readonly class EnsureCompanyIsUnique
{
    public function __construct(private CompanyRepository $companies) {}

    public function handle(Cnpj $cnpj, EmailAddress $email, ?int $ignoreId = null): void
    {
        if ($this->companies->existsByCnpj($cnpj, $ignoreId)) {
            throw new CompanyConflict('Já existe uma empresa cadastrada com este CNPJ.');
        }

        if ($this->companies->existsByEmail($email, $ignoreId)) {
            throw new CompanyConflict('Já existe uma empresa cadastrada com este e-mail.');
        }
    }
}
