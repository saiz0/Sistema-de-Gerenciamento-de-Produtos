<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent\Models;

use Domain\Product\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ProductModel extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'price',
        'internal_code',
        'status',
        'deleted_by_company_at',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyModel::class, 'company_id');
    }

    protected function casts(): array
    {
        return [
            'company_id' => 'integer',
            'price' => 'decimal:2',
            'status' => ProductStatus::class,
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
            'deleted_at' => 'immutable_datetime',
            'deleted_by_company_at' => 'immutable_datetime',
        ];
    }
}
