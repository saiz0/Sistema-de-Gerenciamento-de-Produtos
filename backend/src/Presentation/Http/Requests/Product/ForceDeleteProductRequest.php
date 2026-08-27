<?php

declare(strict_types=1);

namespace Presentation\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

final class ForceDeleteProductRequest extends FormRequest
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
