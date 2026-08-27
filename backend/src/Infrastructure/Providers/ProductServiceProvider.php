<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Domain\Product\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository;

final class ProductServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepository::class, EloquentProductRepository::class);
    }
}
