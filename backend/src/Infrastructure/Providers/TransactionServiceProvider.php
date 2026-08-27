<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Application\Shared\Contracts\TransactionManager;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Persistence\Transaction\LaravelTransactionManager;

final class TransactionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TransactionManager::class, LaravelTransactionManager::class);
    }
}
