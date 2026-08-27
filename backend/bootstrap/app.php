<?php

use Application\Company\Exceptions\CompanyConflict;
use Application\Company\Exceptions\CompanyNotFound;
use Application\Product\Exceptions\ProductConflict;
use Application\Product\Exceptions\ProductNotFound;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Presentation\Http\Responses\ApiMessages;
use Presentation\Http\Responses\ApiResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => true,
        );

        $exceptions->render(fn (ValidationException $exception) => ApiResponse::error(
            ApiMessages::VALIDATION_ERROR,
            'VALIDATION_ERROR',
            422,
            $exception->errors(),
        ));
        $exceptions->render(fn (CompanyNotFound $exception) => ApiResponse::error(
            $exception->getMessage(),
            'COMPANY_NOT_FOUND',
            404,
        ));
        $exceptions->render(fn (CompanyConflict $exception) => ApiResponse::error(
            $exception->getMessage(),
            'COMPANY_CONFLICT',
            409,
        ));
        $exceptions->render(fn (ProductNotFound $exception) => ApiResponse::error(
            $exception->getMessage(),
            'PRODUCT_NOT_FOUND',
            404,
        ));
        $exceptions->render(fn (ProductConflict $exception) => ApiResponse::error(
            $exception->getMessage(),
            'PRODUCT_CONFLICT',
            409,
        ));
        $exceptions->render(fn (\InvalidArgumentException $exception) => ApiResponse::error(
            $exception->getMessage(),
            'INVALID_ARGUMENT',
            422,
        ));
        $exceptions->render(fn (NotFoundHttpException $exception) => ApiResponse::error(
            ApiMessages::NOT_FOUND,
            'ROUTE_NOT_FOUND',
            404,
        ));
    })->create();
