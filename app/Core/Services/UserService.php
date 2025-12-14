<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\DTOs\UserData;
use App\Core\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

/**
 * Service class for User business logic.
 * 
 * Handles all user-related business operations including
 * password hashing, validation logic, and delegation to repository.
 */
class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * Get all users.
     *
     * @return Collection<int, User>
     */
    public function getAllUsers(): Collection
    {
        return $this->userRepository->getAll();
    }

    /**
     * Get paginated users.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedUsers(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->getPaginated($perPage);
    }

    /**
     * Find a user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findUser(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    /**
     * Find a user by ID or throw an exception.
     *
     * @param int $id
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findUserOrFail(int $id): User
    {
        return $this->userRepository->findByIdOrFail($id);
    }

    /**
     * Create a new user.
     * 
     * Handles password hashing before persistence.
     *
     * @param UserData $data
     * @return User
     */
    public function createUser(UserData $data): User
    {
        dd($data);
        // Hash password before creating user
        $hashedData = new UserData(
            name: $data->name,
            email: $data->email,
            password: $data->password ? Hash::make($data->password) : null,

        );

        return $this->userRepository->create($hashedData);
    }

    /**
     * Update an existing user.
     * 
     * Handles password hashing if a new password is provided.
     *
     * @param int $id
     * @param UserData $data
     * @return User
     */
    public function updateUser(int $id, UserData $data): User
    {
        // Hash password if provided
        $hashedData = new UserData(
            name: $data->name,
            email: $data->email,
            password: $data->password ? Hash::make($data->password) : null,
            detail: $data->detail,
        );
        return $this->userRepository->update($id, $hashedData);
    }

    /**
     * Delete a user.
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }
}
