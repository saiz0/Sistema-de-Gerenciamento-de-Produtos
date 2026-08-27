<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Application\Shared\Contracts\TransactionManager;
use Closure;

final class InMemoryTransactionManager implements TransactionManager
{
    public function run(Closure $callback): mixed
    {
        return $callback();
    }
}
