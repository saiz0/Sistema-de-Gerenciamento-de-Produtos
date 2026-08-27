<?php

declare(strict_types=1);

namespace Presentation\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

final class ForceDeleteCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'confirmed' => ['required', 'accepted'],
        ];
    }
}
