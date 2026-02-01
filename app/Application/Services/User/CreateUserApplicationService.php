<?php

declare(strict_types=1);

namespace App\Application\Services\User;

use App\Core\DTOs\UserData;
use App\Domains\User\Entities\UserEntity;
use App\Domains\User\Repositories\UserRepositoryInterface;
use App\Domains\User\Services\UserPasswordHasher;
use App\Domains\User\ValueObjects\Email;

class CreateUserApplicationService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasher $passwordHasher
    ) {}

    public function execute(UserData $data): UserEntity
    {
        $email = new Email($data->email);
        $hashedPassword = $this->passwordHasher->hash($data->password);

        $user = UserEntity::create(
            name: $data->name,
            email: $email,
            hashedPassword: $hashedPassword
        );

        return $this->userRepository->save($user);
    }
}
