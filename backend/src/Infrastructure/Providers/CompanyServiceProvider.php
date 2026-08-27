<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Domain\Company\Repositories\CompanyRepository;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentCompanyRepository;

final class CompanyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CompanyRepository::class, EloquentCompanyRepository::class);
    }
}
