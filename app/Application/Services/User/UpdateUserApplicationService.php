<?php

declare(strict_types=1);

namespace App\Application\Services\User;

use App\Core\DTOs\UserData;
use App\Domains\User\Entities\UserEntity;
use App\Domains\User\Exceptions\UserNotFoundException;
use App\Domains\User\Repositories\UserRepositoryInterface;
use App\Domains\User\Services\UserPasswordHasher;
use App\Domains\User\ValueObjects\Email;

class UpdateUserApplicationService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasher $passwordHasher
    ) {}

    public function execute(int $userId, UserData $data): UserEntity
    {
        $user = $this->userRepository->findById($userId);

        if ($user === null) {
            throw new UserNotFoundException($userId);
        }

        $user->changeName($data->name);

        $email = new Email($data->email);
        $user->changeEmail($email);

        if ($data->password !== null) {
            $hashedPassword = $this->passwordHasher->hash($data->password);
            $user->changePassword($hashedPassword);
        }

        return $this->userRepository->save($user);
    }
}
