<?php

declare(strict_types=1);

namespace App\Core\Interfaces;

use App\Core\DTOs\UserData;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Repository interface for User entity operations.
 * 
 * Defines the contract for user persistence operations,
 * following the Repository pattern for data access abstraction.
 */
interface UserRepositoryInterface
{
    /**
     * Retrieve all users.
     *
     * @return Collection<int, User>
     */
    public function getAll(): Collection;

    /**
     * Retrieve paginated users.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /**
     * Find a user by their ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Find a user by their ID or throw an exception.
     *
     * @param int $id
     * @return User
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findByIdOrFail(int $id): User;

    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Create a new user.
     *
     * @param UserData $data
     * @return User
     */
    public function create(UserData $data): User;

    /**
     * Update an existing user.
     *
     * @param int $id
     * @param UserData $data
     * @return User
     */
    public function update(int $id, UserData $data): User;

    /**
     * Delete a user by their ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
