<?php

declare(strict_types=1);

namespace Domain\Product\Enums;

enum DeletedFilter: string
{
    case Without = 'without';
    case Only = 'only';
    case With = 'with';
}
