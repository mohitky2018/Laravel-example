<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use Illuminate\Support\Facades\Hash;

class UserPasswordHasher
{
    public function hash(string $plainPassword): string
    {
        return Hash::make($plainPassword);
    }

    public function verify(string $plainPassword, string $hashedPassword): bool
    {
        return Hash::check($plainPassword, $hashedPassword);
    }
}
