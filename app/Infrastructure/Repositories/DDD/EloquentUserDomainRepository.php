<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\DDD;

use App\Domains\User\Entities\UserEntity;
use App\Domains\User\Repositories\UserRepositoryInterface;
use App\Domains\User\ValueObjects\Email;
use App\Models\User;

class EloquentUserDomainRepository implements UserRepositoryInterface
{
    public function save(UserEntity $user): UserEntity
    {
        if ($user->getId() === null) {
            $eloquentUser = new User;
        } else {
            $eloquentUser = User::findOrFail($user->getId());
        }

        $eloquentUser->name = $user->getName();
        $eloquentUser->email = $user->getEmail()->getValue();
        $eloquentUser->password = $user->getHashedPassword();
        $eloquentUser->email_verified_at = $user->getEmailVerifiedAt()?->format('Y-m-d H:i:s');
        $eloquentUser->save();

        $user->setId($eloquentUser->id);

        return $user;
    }

    public function findById(int $id): ?UserEntity
    {
        $eloquentUser = User::find($id);

        if ($eloquentUser === null) {
            return null;
        }

        return $this->mapToEntity($eloquentUser);
    }

    public function findByEmail(Email $email): ?UserEntity
    {
        $eloquentUser = User::where('email', $email->getValue())->first();

        if ($eloquentUser === null) {
            return null;
        }

        return $this->mapToEntity($eloquentUser);
    }

    public function delete(int $id): bool
    {
        return User::destroy($id) > 0;
    }

    public function getAll(): array
    {
        return User::all()->map(fn ($user) => $this->mapToEntity($user))->toArray();
    }

    private function mapToEntity(User $eloquentUser): UserEntity
    {
        $entity = new UserEntity(
            id: $eloquentUser->id,
            name: $eloquentUser->name,
            email: new Email($eloquentUser->email),
            hashedPassword: $eloquentUser->password,
            emailVerifiedAt: $eloquentUser->email_verified_at ?
                new \DateTimeImmutable($eloquentUser->email_verified_at) : null,
            createdAt: new \DateTimeImmutable($eloquentUser->created_at),
            updatedAt: new \DateTimeImmutable($eloquentUser->updated_at)
        );

        return $entity;
    }
}
