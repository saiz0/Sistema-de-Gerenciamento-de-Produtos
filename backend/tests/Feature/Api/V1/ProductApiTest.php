<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_and_retrieves_a_product_for_an_active_company(): void
    {
        $companyId = $this->createCompany();

        $created = $this->postJson('/api/v1/products', $this->productPayload($companyId));

        $created
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.company_id', $companyId)
            ->assertJsonPath('data.price', '10.50')
            ->assertJsonPath('data.internal_code', 'PROD-001')
            ->assertJsonPath('data.status', 'active');

        $id = $created->json('data.id');

        $this->getJson("/api/v1/products/{$id}")
            ->assertOk()
            ->assertJsonPath('data.id', $id);
    }

    public function test_validates_all_required_product_fields_on_the_server(): void
    {
        $this->postJson('/api/v1/products', [
            'name' => 'A',
            'description' => str_repeat('a', 2001),
            'price' => '0.00',
            'internal_code' => '',
            'status' => 'invalid',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 'VALIDATION_ERROR')
            ->assertJsonPath('errors.company_id.0', 'O campo empresa é obrigatório.')
            ->assertJsonStructure(['errors' => [
                'company_id',
                'name',
                'description',
                'price',
                'internal_code',
                'status',
            ]]);
    }

    public function test_rejects_create_for_inactive_deleted_or_missing_company(): void
    {
        $inactiveId = $this->createCompany();
        $this->patchJson("/api/v1/companies/{$inactiveId}/deactivate")->assertOk();

        $this->postJson('/api/v1/products', $this->productPayload($inactiveId))
            ->assertConflict()
            ->assertJsonPath('code', 'PRODUCT_CONFLICT');

        $deletedId = $this->createCompany(2);
        $this->deleteJson("/api/v1/companies/{$deletedId}")->assertOk();

        $this->postJson('/api/v1/products', $this->productPayload($deletedId))
            ->assertConflict();

        $this->postJson('/api/v1/products', $this->productPayload(999999))
            ->assertConflict();
    }

    public function test_keeps_internal_code_unique_inside_company_including_soft_deleted_products(): void
    {
        $firstCompany = $this->createCompany();
        $secondCompany = $this->createCompany(2);
        $productId = $this->createProduct($firstCompany);
        $this->deleteJson("/api/v1/products/{$productId}")->assertOk();

        $this->postJson('/api/v1/products', $this->productPayload($firstCompany))
            ->assertConflict()
            ->assertJsonPath('code', 'PRODUCT_CONFLICT');

        $this->postJson('/api/v1/products', $this->productPayload($secondCompany))
            ->assertCreated();
    }

    public function test_updates_product_without_changing_status_and_rejects_inactive_target_company(): void
    {
        $companyId = $this->createCompany();
        $inactiveCompanyId = $this->createCompany(2);
        $this->patchJson("/api/v1/companies/{$inactiveCompanyId}/deactivate")->assertOk();
        $productId = $this->createProduct($companyId);
        $this->patchJson("/api/v1/products/{$productId}/deactivate")->assertOk();

        $this->putJson("/api/v1/products/{$productId}", $this->productPayload(
            companyId: $companyId,
            code: 'PROD-002',
            name: 'Produto Atualizado',
            price: '25,90',
        ))
            ->assertOk()
            ->assertJsonPath('data.name', 'Produto Atualizado')
            ->assertJsonPath('data.price', '25.90')
            ->assertJsonPath('data.status', 'inactive');

        $this->putJson(
            "/api/v1/products/{$productId}",
            $this->productPayload($inactiveCompanyId, 'PROD-002'),
        )->assertConflict();
    }

    public function test_lists_products_with_name_status_company_and_deleted_filters(): void
    {
        $firstCompany = $this->createCompany();
        $secondCompany = $this->createCompany(2);
        $firstProduct = $this->createProduct($firstCompany, 'PROD-001', 'Produto Principal');
        $this->createProduct($secondCompany, 'PROD-001', 'Produto Secundário');
        $this->patchJson("/api/v1/products/{$firstProduct}/deactivate")->assertOk();

        $this->getJson("/api/v1/products?name=Principal&status=inactive&company_id={$firstCompany}&per_page=1")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $firstProduct)
            ->assertJsonPath('meta.total', 1);

        $this->deleteJson("/api/v1/products/{$firstProduct}")->assertOk();

        $this->getJson('/api/v1/products')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/products?deleted=only')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $firstProduct)
            ->assertJsonPath('data.0.status', 'inactive');
    }

    public function test_soft_deletes_and_restores_an_individual_product(): void
    {
        $companyId = $this->createCompany();
        $productId = $this->createProduct($companyId);

        $this->deleteJson("/api/v1/products/{$productId}")->assertOk();
        $this->getJson("/api/v1/products/{$productId}")->assertNotFound();

        $this->postJson("/api/v1/products/{$productId}/restore")
            ->assertOk()
            ->assertJsonPath('data.id', $productId)
            ->assertJsonPath('data.deleted_at', null);

        $this->assertDatabaseHas('products', ['id' => $productId, 'deleted_at' => null]);
    }

    public function test_company_status_cascade_does_not_reactivate_products_automatically(): void
    {
        $companyId = $this->createCompany();
        $productId = $this->createProduct($companyId);

        $this->patchJson("/api/v1/companies/{$companyId}/deactivate")->assertOk();
        $this->getJson("/api/v1/products/{$productId}")
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');

        $this->patchJson("/api/v1/products/{$productId}/activate")
            ->assertConflict();

        $this->patchJson("/api/v1/companies/{$companyId}/activate")->assertOk();
        $this->getJson("/api/v1/products/{$productId}")
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');

        $this->patchJson("/api/v1/products/{$productId}/activate")
            ->assertOk()
            ->assertJsonPath('data.status', 'active');
    }

    public function test_company_restore_recovers_only_products_deleted_with_it(): void
    {
        $companyId = $this->createCompany();
        $individuallyDeleted = $this->createProduct($companyId, 'PROD-001');
        $deletedWithCompany = $this->createProduct($companyId, 'PROD-002');
        $this->deleteJson("/api/v1/products/{$individuallyDeleted}")->assertOk();
        $this->deleteJson("/api/v1/companies/{$companyId}")->assertOk();

        $this->postJson("/api/v1/products/{$deletedWithCompany}/restore")
            ->assertConflict();
        $this->deleteJson("/api/v1/companies/{$companyId}/force", ['confirmed' => true])
            ->assertConflict();

        $this->postJson("/api/v1/companies/{$companyId}/restore")->assertOk();

        $this->getJson("/api/v1/products/{$individuallyDeleted}")->assertNotFound();
        $this->getJson("/api/v1/products/{$deletedWithCompany}")
            ->assertOk()
            ->assertJsonPath('data.deleted_at', null);
    }

    public function test_product_force_delete_requires_soft_delete_and_confirmation(): void
    {
        $companyId = $this->createCompany();
        $productId = $this->createProduct($companyId);

        $this->deleteJson("/api/v1/products/{$productId}/force", ['confirmed' => true])
            ->assertConflict();
        $this->deleteJson("/api/v1/products/{$productId}")->assertOk();
        $this->deleteJson("/api/v1/products/{$productId}/force", ['confirmed' => false])
            ->assertUnprocessable();
        $this->deleteJson("/api/v1/products/{$productId}/force", ['confirmed' => true])
            ->assertOk();

        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }

    private function createCompany(int $variant = 1): int
    {
        $payloads = [
            1 => ['Empresa Exemplo', '11222333000181', 'empresa1@exemplo.com'],
            2 => ['Segunda Empresa', '11444777000161', 'empresa2@exemplo.com'],
        ];
        [$name, $cnpj, $email] = $payloads[$variant];

        return $this->postJson('/api/v1/companies', [
            'name' => $name,
            'cnpj' => $cnpj,
            'email' => $email,
            'phone' => '71999999999',
            'status' => 'active',
        ])->assertCreated()->json('data.id');
    }

    private function createProduct(
        int $companyId,
        string $code = 'PROD-001',
        string $name = 'Produto Exemplo',
    ): int {
        return $this->postJson('/api/v1/products', $this->productPayload($companyId, $code, $name))
            ->assertCreated()
            ->json('data.id');
    }

    private function productPayload(
        int $companyId,
        string $code = 'PROD-001',
        string $name = 'Produto Exemplo',
        string $price = '10.50',
    ): array {
        return [
            'company_id' => $companyId,
            'name' => $name,
            'description' => 'Descrição do produto',
            'price' => $price,
            'internal_code' => $code,
            'status' => 'active',
        ];
    }
}
