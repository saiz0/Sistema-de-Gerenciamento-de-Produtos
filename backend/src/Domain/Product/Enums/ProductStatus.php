<?php

declare(strict_types=1);

namespace Domain\Product\Enums;

enum ProductStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
