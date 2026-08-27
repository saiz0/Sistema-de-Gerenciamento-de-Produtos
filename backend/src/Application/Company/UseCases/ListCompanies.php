<?php

declare(strict_types=1);

namespace Application\Company\UseCases;

use Application\Company\DTOs\SearchCompaniesData;
use Domain\Company\Collections\CompanyPage;
use Domain\Company\Repositories\CompanyRepository;

final readonly class ListCompanies
{
    public function __construct(private CompanyRepository $companies) {}

    public function execute(SearchCompaniesData $data): CompanyPage
    {
        return $this->companies->search(
            $data->name,
            $data->status,
            $data->deleted,
            $data->page,
            $data->perPage,
        );
    }
}
