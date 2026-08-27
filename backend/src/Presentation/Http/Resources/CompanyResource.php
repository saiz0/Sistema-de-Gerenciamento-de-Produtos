<?php

declare(strict_types=1);

namespace Presentation\Http\Resources;

use Domain\Company\Entities\Company;

final class CompanyResource
{
    public static function make(Company $company): array
    {
        return [
            'id' => $company->id(),
            'name' => $company->name(),
            'cnpj' => $company->cnpj()->value(),
            'email' => $company->email()->value(),
            'phone' => $company->phone()->value(),
            'status' => $company->status()->value,
            'created_at' => $company->createdAt()?->format(DATE_ATOM),
            'updated_at' => $company->updatedAt()?->format(DATE_ATOM),
            'deleted_at' => $company->deletedAt()?->format(DATE_ATOM),
        ];
    }

    private function __construct() {}
}
