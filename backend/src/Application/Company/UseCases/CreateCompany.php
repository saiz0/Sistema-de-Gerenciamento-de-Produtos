<?php

declare(strict_types=1);

namespace Application\Company\UseCases;

use Application\Company\DTOs\CreateCompanyData;
use Application\Company\Services\EnsureCompanyIsUnique;
use Domain\Company\Entities\Company;
use Domain\Company\Repositories\CompanyRepository;
use Domain\Company\ValueObjects\Cnpj;
use Domain\Company\ValueObjects\EmailAddress;
use Domain\Company\ValueObjects\PhoneNumber;

final readonly class CreateCompany
{
    public function __construct(
        private CompanyRepository $companies,
        private EnsureCompanyIsUnique $ensureUnique,
    ) {}

    public function execute(CreateCompanyData $data): Company
    {
        $cnpj = new Cnpj($data->cnpj);
        $email = new EmailAddress($data->email);

        $this->ensureUnique->handle($cnpj, $email);

        return $this->companies->save(Company::create(
            name: $data->name,
            cnpj: $cnpj,
            email: $email,
            phone: new PhoneNumber($data->phone),
            status: $data->status,
        ));
    }
}
