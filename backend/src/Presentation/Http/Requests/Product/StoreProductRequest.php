<?php

declare(strict_types=1);

namespace Presentation\Http\Requests\Product;

use Domain\Product\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Presentation\Http\Validation\Rules\ValidPrice;

final class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', new ValidPrice],
            'internal_code' => ['required', 'string', 'max:100'],
            'status' => ['sometimes', Rule::enum(ProductStatus::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $description = is_string($this->description) ? trim($this->description) : $this->description;

        $this->merge([
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
            'description' => $description === '' ? null : $description,
            'price' => is_scalar($this->price) ? str_replace(',', '.', trim((string) $this->price)) : $this->price,
            'internal_code' => is_string($this->internal_code) ? trim($this->internal_code) : $this->internal_code,
        ]);
    }
}
