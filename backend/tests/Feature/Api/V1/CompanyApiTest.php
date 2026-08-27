<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CompanyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_and_retrieves_a_company(): void
    {
        $created = $this->postJson('/api/v1/companies', $this->payload());

        $created
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Empresa Exemplo')
            ->assertJsonPath('data.cnpj', '11222333000181')
            ->assertJsonPath('data.status', 'active');

        $id = $created->json('data.id');

        $this->getJson("/api/v1/companies/{$id}")
            ->assertOk()
            ->assertJsonPath('data.id', $id);
    }

    public function test_returns_standard_validation_errors(): void
    {
        $this->postJson('/api/v1/companies', [
            'name' => 'A',
            'cnpj' => '123',
            'email' => 'invalid',
            'phone' => '1',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 'VALIDATION_ERROR')
            ->assertJsonStructure(['errors' => ['name', 'cnpj', 'email', 'phone']]);
    }

    public function test_keeps_cnpj_unique_even_after_soft_delete(): void
    {
        $id = $this->postJson('/api/v1/companies', $this->payload())->json('data.id');
        $this->deleteJson("/api/v1/companies/{$id}")->assertOk();

        $this->postJson('/api/v1/companies', $this->payload('outra@exemplo.com'))
            ->assertConflict()
            ->assertJsonPath('code', 'COMPANY_CONFLICT');
    }

    public function test_updates_status_and_lists_with_filters_and_pagination(): void
    {
        $firstId = $this->postJson('/api/v1/companies', $this->payload())->json('data.id');
        $this->postJson('/api/v1/companies', $this->payload(
            email: 'segunda@exemplo.com',
            cnpj: '11444777000161',
            name: 'Segunda Fornecedora',
        ))->assertCreated();

        $this->patchJson("/api/v1/companies/{$firstId}/deactivate")
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');

        $this->getJson('/api/v1/companies?status=inactive&name=Exemplo&per_page=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $firstId);

        $this->patchJson("/api/v1/companies/{$firstId}/activate")
            ->assertOk()
            ->assertJsonPath('data.status', 'active');
    }

    public function test_updates_company_data_without_changing_status(): void
    {
        $id = $this->postJson('/api/v1/companies', $this->payload())->json('data.id');
        $this->patchJson("/api/v1/companies/{$id}/deactivate")->assertOk();

        $this->putJson("/api/v1/companies/{$id}", $this->payload(
            email: 'novo@exemplo.com',
            name: 'Empresa Atualizada',
        ))
            ->assertOk()
            ->assertJsonPath('data.name', 'Empresa Atualizada')
            ->assertJsonPath('data.email', 'novo@exemplo.com')
            ->assertJsonPath('data.status', 'inactive');
    }

    public function test_soft_deletes_lists_and_restores_a_company(): void
    {
        $id = $this->postJson('/api/v1/companies', $this->payload())->json('data.id');

        $this->deleteJson("/api/v1/companies/{$id}")->assertOk();
        $this->assertSoftDeleted('companies', ['id' => $id]);

        $this->getJson('/api/v1/companies?deleted=only')
            ->assertOk()
            ->assertJsonPath('data.0.id', $id);

        $this->postJson("/api/v1/companies/{$id}/restore")
            ->assertOk()
            ->assertJsonPath('data.deleted_at', null);

        $this->assertDatabaseHas('companies', ['id' => $id, 'deleted_at' => null]);
    }

    public function test_requires_soft_delete_and_confirmation_before_force_delete(): void
    {
        $id = $this->postJson('/api/v1/companies', $this->payload())->json('data.id');

        $this->deleteJson("/api/v1/companies/{$id}/force", ['confirmed' => true])
            ->assertConflict();

        $this->deleteJson("/api/v1/companies/{$id}")->assertOk();
        $this->deleteJson("/api/v1/companies/{$id}/force", ['confirmed' => false])
            ->assertUnprocessable();
        $this->deleteJson("/api/v1/companies/{$id}/force", ['confirmed' => true])
            ->assertOk();

        $this->assertDatabaseMissing('companies', ['id' => $id]);
    }

    private function payload(
        string $email = 'contato@exemplo.com',
        string $cnpj = '11.222.333/0001-81',
        string $name = 'Empresa Exemplo',
    ): array {
        return [
            'name' => $name,
            'cnpj' => $cnpj,
            'email' => $email,
            'phone' => '(71) 99999-9999',
            'status' => 'active',
        ];
    }
}
