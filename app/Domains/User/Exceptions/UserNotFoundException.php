<?php

declare(strict_types=1);

namespace App\Domains\User\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    public function __construct(int $userId)
    {
        parent::__construct("User with ID {$userId} not found");
    }
}
