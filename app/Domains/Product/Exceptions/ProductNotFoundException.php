<?php

declare(strict_types=1);

namespace App\Domains\Product\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    public function __construct(int $productId)
    {
        parent::__construct("Product with ID {$productId} not found");
    }
}
