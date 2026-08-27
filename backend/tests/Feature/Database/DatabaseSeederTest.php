<?php

declare(strict_types=1);

namespace Tests\Feature\Database;

use Database\Seeders\DatabaseSeeder;
use Domain\Company\ValueObjects\Cnpj;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infrastructure\Persistence\Eloquent\Models\CompanyModel;
use Infrastructure\Persistence\Eloquent\Models\ProductModel;
use Tests\TestCase;

final class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_ten_companies_with_one_hundred_products_each_without_duplicates(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        self::assertSame(10, CompanyModel::query()->count());
        self::assertSame(1000, ProductModel::query()->count());

        CompanyModel::query()->each(function (CompanyModel $company): void {
            self::assertTrue(Cnpj::isValid($company->cnpj));
            self::assertSame(100, ProductModel::query()->where('company_id', $company->getKey())->count());
            self::assertSame(
                100,
                ProductModel::query()
                    ->where('company_id', $company->getKey())
                    ->distinct('internal_code')
                    ->count('internal_code'),
            );
        });
    }
}
