<?php

use Illuminate\Support\Facades\Route;
use Presentation\Http\Controllers\Api\V1\CompanyController;
use Presentation\Http\Controllers\Api\V1\ProductController;

Route::prefix('v1')->group(function (): void {
    Route::prefix('companies')->controller(CompanyController::class)->group(function (): void {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{company}', 'show')->whereNumber('company');
        Route::put('/{company}', 'update')->whereNumber('company');
        Route::patch('/{company}/activate', 'activate')->whereNumber('company');
        Route::patch('/{company}/deactivate', 'deactivate')->whereNumber('company');
        Route::delete('/{company}', 'destroy')->whereNumber('company');
        Route::post('/{company}/restore', 'restore')->whereNumber('company');
        Route::delete('/{company}/force', 'forceDestroy')->whereNumber('company');
    });

    Route::prefix('products')->controller(ProductController::class)->group(function (): void {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/{product}', 'show')->whereNumber('product');
        Route::put('/{product}', 'update')->whereNumber('product');
        Route::patch('/{product}/activate', 'activate')->whereNumber('product');
        Route::patch('/{product}/deactivate', 'deactivate')->whereNumber('product');
        Route::delete('/{product}', 'destroy')->whereNumber('product');
        Route::post('/{product}/restore', 'restore')->whereNumber('product');
        Route::delete('/{product}/force', 'forceDestroy')->whereNumber('product');
    });
});
