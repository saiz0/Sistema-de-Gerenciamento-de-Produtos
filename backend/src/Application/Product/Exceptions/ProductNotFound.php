<?php

declare(strict_types=1);

namespace Application\Product\Exceptions;

use RuntimeException;

final class ProductNotFound extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Produto não encontrado.');
    }
}
