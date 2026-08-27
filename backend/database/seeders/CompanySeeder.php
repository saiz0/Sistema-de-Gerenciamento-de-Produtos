<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Company\Enums\CompanyStatus;
use Illuminate\Database\Seeder;
use Infrastructure\Persistence\Eloquent\Models\CompanyModel;

final class CompanySeeder extends Seeder
{
    private const COMPANY_COUNT = 10;

    public function run(): void
    {
        $timestamp = now();
        $companies = [];

        for ($number = 1; $number <= self::COMPANY_COUNT; $number++) {
            $companies[] = [
                'name' => sprintf('Empresa Fornecedora %02d', $number),
                'cnpj' => $this->cnpj($number),
                'email' => sprintf('fornecedor%02d@exemplo.com.br', $number),
                'phone' => sprintf('719%08d', $number),
                'status' => CompanyStatus::Active->value,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'deleted_at' => null,
            ];
        }

        CompanyModel::query()->upsert(
            $companies,
            ['cnpj'],
            ['name', 'email', 'phone', 'status', 'updated_at', 'deleted_at'],
        );
    }

    private function cnpj(int $number): string
    {
        $base = sprintf('12345678%04d', $number);
        $firstDigit = $this->digit($base, [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

        return $base.$firstDigit.$this->digit(
            $base.$firstDigit,
            [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2],
        );
    }

    private function digit(string $base, array $weights): int
    {
        $sum = 0;

        foreach ($weights as $index => $weight) {
            $sum += (int) $base[$index] * $weight;
        }

        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
