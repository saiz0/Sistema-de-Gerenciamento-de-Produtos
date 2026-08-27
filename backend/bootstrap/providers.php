<?php

use App\Providers\AppServiceProvider;
use Infrastructure\Providers\CompanyServiceProvider;
use Infrastructure\Providers\ProductServiceProvider;

return [
    AppServiceProvider::class,
    CompanyServiceProvider::class,
    ProductServiceProvider::class,
];
