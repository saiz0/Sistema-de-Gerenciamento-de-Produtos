<?php

declare(strict_types=1);

namespace Domain\Company\Enums;

enum CompanyStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
