<?php

declare(strict_types=1);

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\UserEntity;
use App\Domains\User\ValueObjects\Email;

interface UserRepositoryInterface
{
    public function save(UserEntity $user): UserEntity;

    public function findById(int $id): ?UserEntity;

    public function findByEmail(Email $email): ?UserEntity;

    public function delete(int $id): bool;

    public function getAll(): array;
}
