<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Domain\Company\Enums\CompanyStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class CompanyModel extends Model
{
    use SoftDeletes;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'phone',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => CompanyStatus::class,
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
            'deleted_at' => 'immutable_datetime',
        ];
    }
}
