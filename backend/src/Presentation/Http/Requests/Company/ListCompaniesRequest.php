<?php

declare(strict_types=1);

namespace Presentation\Http\Requests\Company;

use Domain\Company\Enums\CompanyStatus;
use Domain\Company\Enums\DeletedFilter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class ListCompaniesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', Rule::enum(CompanyStatus::class)],
            'deleted' => ['nullable', Rule::enum(DeletedFilter::class)],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
