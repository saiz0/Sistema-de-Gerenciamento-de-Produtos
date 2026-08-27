<?php

use App\Providers\AppServiceProvider;
use Infrastructure\Providers\CompanyServiceProvider;
use Infrastructure\Providers\ProductServiceProvider;
use Infrastructure\Providers\TransactionServiceProvider;

return [
    AppServiceProvider::class,
    CompanyServiceProvider::class,
    ProductServiceProvider::class,
    TransactionServiceProvider::class,
];
