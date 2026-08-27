<?php

declare(strict_types=1);

namespace Presentation\Http\Requests\Product;

use Domain\Product\Enums\DeletedFilter;
use Domain\Product\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ListProductsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', Rule::enum(ProductStatus::class)],
            'company_id' => ['nullable', 'integer', 'min:1'],
            'deleted' => ['nullable', Rule::enum(DeletedFilter::class)],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
