<?php

declare(strict_types=1);

namespace Domain\Company\Collections;

use Domain\Company\Entities\Company;

final readonly class CompanyPage
{
    /** @param list<Company> $items */
    public function __construct(
        public array $items,
        public int $currentPage,
        public int $perPage,
        public int $total,
        public int $lastPage,
    ) {}
}
