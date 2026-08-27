<?php

use Illuminate\Support\Facades\Route;
use Presentation\Http\Controllers\Api\V1\CompanyController;

Route::prefix('v1')->group(function (): void {
    Route::get('companies', [CompanyController::class, 'index']);
    Route::post('companies', [CompanyController::class, 'store']);
    Route::get('companies/{company}', [CompanyController::class, 'show'])->whereNumber('company');
    Route::put('companies/{company}', [CompanyController::class, 'update'])->whereNumber('company');
    Route::patch('companies/{company}/activate', [CompanyController::class, 'activate'])->whereNumber('company');
    Route::patch('companies/{company}/deactivate', [CompanyController::class, 'deactivate'])->whereNumber('company');
    Route::delete('companies/{company}', [CompanyController::class, 'destroy'])->whereNumber('company');
    Route::post('companies/{company}/restore', [CompanyController::class, 'restore'])->whereNumber('company');
    Route::delete('companies/{company}/force', [CompanyController::class, 'forceDestroy'])->whereNumber('company');
});
