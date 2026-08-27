<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

final class ApiErrorResponseTest extends TestCase
{
    public function test_does_not_expose_internal_details_for_unexpected_errors(): void
    {
        Route::get('/api/v1/testing/internal-error', static function (): never {
            throw new RuntimeException('SQLSTATE sensitive internal path');
        });

        $this->getJson('/api/v1/testing/internal-error')
            ->assertInternalServerError()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Ocorreu um erro interno.')
            ->assertJsonPath('code', 'INTERNAL_ERROR')
            ->assertJsonMissingPaths(['exception', 'file', 'line', 'trace'])
            ->assertDontSee('SQLSTATE');
    }

    public function test_returns_standard_errors_for_unknown_route_and_invalid_method(): void
    {
        $this->getJson('/api/v1/unknown')
            ->assertNotFound()
            ->assertJsonPath('code', 'ROUTE_NOT_FOUND');

        $this->patchJson('/api/v1/products')
            ->assertStatus(405)
            ->assertJsonPath('message', 'Método não permitido para esta rota.')
            ->assertJsonPath('code', 'HTTP_ERROR');
    }
}
