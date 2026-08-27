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
        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 150)->index();
            $table->char('cnpj', 14)->unique();
            $table->string('email', 254);
            $table->string('phone', 11);
            $table->string('status', 8)->index();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index('deleted_at');
        });

        DB::statement(
            "ALTER TABLE companies ADD CONSTRAINT companies_status_check CHECK (status IN ('active', 'inactive'))"
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
