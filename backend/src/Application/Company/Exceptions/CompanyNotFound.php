<?php

declare(strict_types=1);

namespace Application\Company\Exceptions;

use RuntimeException;

final class CompanyNotFound extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Empresa não encontrada.');
    }
}
