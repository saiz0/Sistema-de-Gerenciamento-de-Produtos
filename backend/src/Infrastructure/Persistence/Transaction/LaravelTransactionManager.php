<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Transaction;

use Application\Shared\Contracts\TransactionManager;
use Closure;
use Illuminate\Support\Facades\DB;

final class LaravelTransactionManager implements TransactionManager
{
    public function run(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }
}
