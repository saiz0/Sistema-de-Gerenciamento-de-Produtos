<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Company;

use Domain\Company\Entities\Company;
use Domain\Company\Enums\CompanyStatus;
use Domain\Company\ValueObjects\Cnpj;
use Domain\Company\ValueObjects\EmailAddress;
use Domain\Company\ValueObjects\PhoneNumber;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CompanyTest extends TestCase
{
    public function test_controls_status_independently_from_persistence(): void
    {
        $company = Company::create(
            'Empresa Exemplo',
            new Cnpj('11222333000181'),
            new EmailAddress('CONTATO@EXEMPLO.COM'),
            new PhoneNumber('(71) 99999-9999'),
        );

        $company->deactivate();
        self::assertSame(CompanyStatus::Inactive, $company->status());

        $company->activate();
        self::assertSame(CompanyStatus::Active, $company->status());
        self::assertSame('contato@exemplo.com', $company->email()->value());
        self::assertSame('71999999999', $company->phone()->value());
    }

    public function test_rejects_an_invalid_name(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Company::create(
            'AB',
            new Cnpj('11222333000181'),
            new EmailAddress('contato@exemplo.com'),
            new PhoneNumber('71999999999'),
        );
    }
}
