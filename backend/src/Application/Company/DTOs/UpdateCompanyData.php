<?php

declare(strict_types=1);

namespace Application\Company\DTOs;

final readonly class UpdateCompanyData
{
    public function __construct(
        public string $name,
        public string $cnpj,
        public string $email,
        public string $phone,
    ) {}
}
