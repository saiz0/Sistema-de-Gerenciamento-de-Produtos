<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Product\Enums\ProductStatus;
use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\CompanyModel;
use Infrastructure\Persistence\Eloquent\Models\ProductModel;

final class ProductSeeder extends Seeder
{
    private const PRODUCTS_PER_COMPANY = 100;

    public function run(): void
    {
        $timestamp = now();
        $products = [];
        $companyEmails = array_map(
            static fn (int $number): string => sprintf('fornecedor%02d@exemplo.com.br', $number),
            range(1, 10),
        );
        $companies = CompanyModel::query()
            ->whereIn('email', $companyEmails)
            ->orderBy('email')
            ->get(['id', 'name']);

        foreach ($companies as $companyIndex => $company) {
            for ($number = 1; $number <= self::PRODUCTS_PER_COMPANY; $number++) {
                $products[] = [
                    'company_id' => $company->getKey(),
                    'name' => sprintf('Produto %03d - %s', $number, $company->name),
                    'description' => sprintf('Produto de demonstração %03d da empresa %s.', $number, $company->name),
                    'price' => number_format(10 + $companyIndex + ($number / 10), 2, '.', ''),
                    'internal_code' => sprintf('PROD-%03d', $number),
                    'status' => ProductStatus::Active->value,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                    'deleted_at' => null,
                    'deleted_by_company_at' => null,
                ];
            }
        }

        ProductModel::query()->upsert(
            $products,
            ['company_id', 'internal_code'],
            ['name', 'description', 'price', 'status', 'updated_at', 'deleted_at', 'deleted_by_company_at'],
        );
    }
}
