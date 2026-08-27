<?php

declare(strict_types=1);

namespace Application\Company\DTOs;

use Domain\Company\Enums\CompanyStatus;

final readonly class CreateCompanyData
{
    public function __construct(
        public string $name,
        public string $cnpj,
        public string $email,
        public string $phone,
        public CompanyStatus $status,
    ) {}
}
