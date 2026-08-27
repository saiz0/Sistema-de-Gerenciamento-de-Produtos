<?php

declare(strict_types=1);

namespace Application\Shared\Contracts;

use Closure;

interface TransactionManager
{
    public function run(Closure $callback): mixed;
}
