<?php

declare(strict_types=1);

namespace Presentation\Http\Responses;

final class ApiMessages
{
    public const SUCCESS = 'Operação realizada com sucesso.';
    public const CREATED = 'Registro criado com sucesso.';
    public const UPDATED = 'Registro atualizado com sucesso.';
    public const DELETED = 'Registro excluído com sucesso.';
    public const RESTORED = 'Registro restaurado com sucesso.';
    public const STATUS_UPDATED = 'Status atualizado com sucesso.';
    public const VALIDATION_ERROR = 'Os dados informados são inválidos.';
    public const NOT_FOUND = 'Registro não encontrado.';
    public const CONFLICT = 'A operação não pôde ser concluída.';
    public const INTERNAL_ERROR = 'Ocorreu um erro interno.';

    private function __construct() {}
}
