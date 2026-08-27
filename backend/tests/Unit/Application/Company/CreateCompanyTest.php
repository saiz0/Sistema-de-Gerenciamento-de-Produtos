<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Company;

use Application\Company\DTOs\CreateCompanyData;
use Application\Company\Exceptions\CompanyConflict;
use Application\Company\Services\EnsureCompanyIsUnique;
use Application\Company\UseCases\CreateCompany;
use Domain\Company\Enums\CompanyStatus;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\InMemoryCompanyRepository;

final class CreateCompanyTest extends TestCase
{
    public function test_creates_a_company_through_the_domain_contract(): void
    {
        $repository = new InMemoryCompanyRepository;
        $useCase = new CreateCompany($repository, new EnsureCompanyIsUnique($repository));

        $company = $useCase->execute($this->data());

        self::assertSame(1, $company->id());
        self::assertSame('Empresa Exemplo', $company->name());
        self::assertSame(CompanyStatus::Active, $company->status());
    }

    public function test_rejects_a_cnpj_already_used_by_another_company(): void
    {
        $repository = new InMemoryCompanyRepository;
        $useCase = new CreateCompany($repository, new EnsureCompanyIsUnique($repository));
        $useCase->execute($this->data());

        $this->expectException(CompanyConflict::class);
        $useCase->execute($this->data('outro@exemplo.com'));
    }

    public function test_rejects_an_email_already_used_by_another_company(): void
    {
        $repository = new InMemoryCompanyRepository;
        $useCase = new CreateCompany($repository, new EnsureCompanyIsUnique($repository));
        $useCase->execute($this->data());

        $this->expectException(CompanyConflict::class);
        $useCase->execute(new CreateCompanyData(
            'Outra Empresa',
            '11444777000161',
            'CONTATO@EXEMPLO.COM',
            '71988888888',
            CompanyStatus::Active,
        ));
    }

    private function data(string $email = 'contato@exemplo.com'): CreateCompanyData
    {
        return new CreateCompanyData(
            'Empresa Exemplo',
            '11222333000181',
            $email,
            '71999999999',
            CompanyStatus::Active,
        );
    }
}
