<?php

declare(strict_types=1);

namespace Application\Company\UseCases;

use Application\Company\DTOs\UpdateCompanyData;
use Application\Company\Exceptions\CompanyNotFound;
use Application\Company\Services\EnsureCompanyIsUnique;
use Domain\Company\Entities\Company;
use Domain\Company\Repositories\CompanyRepository;
use Domain\Company\ValueObjects\Cnpj;
use Domain\Company\ValueObjects\EmailAddress;
use Domain\Company\ValueObjects\PhoneNumber;

final readonly class UpdateCompany
{
    public function __construct(
        private CompanyRepository $companies,
        private EnsureCompanyIsUnique $ensureUnique,
    ) {}

    public function execute(int $id, UpdateCompanyData $data): Company
    {
        $company = $this->companies->findById($id) ?? throw new CompanyNotFound;
        $cnpj = new Cnpj($data->cnpj);
        $email = new EmailAddress($data->email);

        $this->ensureUnique->handle($cnpj, $id);

        $company->update($data->name, $cnpj, $email, new PhoneNumber($data->phone));

        return $this->companies->save($company);
    }
}
