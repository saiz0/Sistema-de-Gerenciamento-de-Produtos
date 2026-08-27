<?php

declare(strict_types=1);

namespace Presentation\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Presentation\Http\Validation\Rules\ValidCnpj;
use Presentation\Http\Validation\Rules\ValidPhone;

final class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'cnpj' => ['required', 'string', new ValidCnpj],
            'email' => ['required', 'string', 'email:rfc', 'max:254'],
            'phone' => ['required', 'string', new ValidPhone],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
            'cnpj' => is_string($this->cnpj) ? preg_replace('/\D/', '', $this->cnpj) : $this->cnpj,
            'email' => is_string($this->email) ? mb_strtolower(trim($this->email)) : $this->email,
            'phone' => is_string($this->phone) ? preg_replace('/\D/', '', $this->phone) : $this->phone,
        ]);
    }
}
