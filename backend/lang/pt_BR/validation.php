<?php

declare(strict_types=1);

return [
    'accepted' => 'O campo :attribute deve ser aceito.',
    'decimal' => 'O campo :attribute deve ter :decimal casas decimais.',
    'email' => 'O campo :attribute deve conter um endereço de e-mail válido.',
    'enum' => 'O valor selecionado para o campo :attribute é inválido.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'max' => [
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'string' => 'O campo :attribute não pode ter mais que :max caracteres.',
    ],
    'min' => [
        'numeric' => 'O campo :attribute deve ser pelo menos :min.',
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
    ],
    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute deve ser um texto.',

    'attributes' => [
        'name' => 'nome',
        'cnpj' => 'CNPJ',
        'email' => 'e-mail',
        'phone' => 'telefone',
        'status' => 'status',
        'company_id' => 'empresa',
        'description' => 'descrição',
        'price' => 'preço',
        'internal_code' => 'código interno',
        'deleted' => 'filtro de excluídos',
        'page' => 'página',
        'per_page' => 'itens por página',
        'confirmed' => 'confirmação',
    ],
];
