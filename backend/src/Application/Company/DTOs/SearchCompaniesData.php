<?php

declare(strict_types=1);

namespace Application\Company\DTOs;

use Domain\Company\Enums\CompanyStatus;
use Domain\Company\Enums\DeletedFilter;

final readonly class SearchCompaniesData
{
    public function __construct(
        public ?string $name = null,
        public ?CompanyStatus $status = null,
        public DeletedFilter $deleted = DeletedFilter::Without,
        public int $page = 1,
        public int $perPage = 15,
    ) {}
}
