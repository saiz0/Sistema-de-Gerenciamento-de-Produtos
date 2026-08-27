<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('name', 150)->index();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2);
            $table->string('internal_code', 100);
            $table->string('status', 8)->index();
            $table->timestampTz('deleted_by_company_at')->nullable()->index();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(['company_id', 'internal_code']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'deleted_at']);
        });

        DB::statement(
            "ALTER TABLE products ADD CONSTRAINT products_status_check CHECK (status IN ('active', 'inactive'))"
        );
        DB::statement(
            'ALTER TABLE products ADD CONSTRAINT products_price_check CHECK (price > 0)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
