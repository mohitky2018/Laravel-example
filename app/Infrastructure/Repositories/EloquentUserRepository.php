<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Core\DTOs\UserData;
use App\Core\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent implementation of UserRepositoryInterface.
 * 
 * Handles all database operations for User entity using Eloquent ORM.
 */
class EloquentUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly User $model
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): Collection
    {
        return $this->model->newQuery()->with('detail')->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()->with('detail')->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?User
    {
        return $this->model->newQuery()->with('detail')->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByIdOrFail(int $id): User
    {
        return $this->model->newQuery()->with('detail')->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->newQuery()->with('detail')->where('email', $email)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function create(UserData $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = $this->model->newQuery()->create($this->mapToAttributes($data));

            // Create user details if provided
            if ($data->detail !== null && $data->detail->hasData()) {
                $user->detail()->create($data->detail->toArray());
            }

            return $user->load('detail');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, UserData $data): User
    {
        return DB::transaction(function () use ($id, $data) {
            $user = $this->findByIdOrFail($id);

            $attributes = $this->mapToAttributes($data);

            // Only update password if provided
            if ($data->password === null) {
                unset($attributes['password']);
            }

            $user->update($attributes);

            // Update or create user details if provided
            if ($data->detail !== null) {
                if ($data->detail->hasData()) {
                    $user->detail()->updateOrCreate(
                        ['user_id' => $user->id],
                        $data->detail->toArray()
                    );
                }
            }

            return $user->fresh('detail');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $user = $this->findByIdOrFail($id);

        // Details will be cascade deleted due to foreign key constraint
        return (bool) $user->delete();
    }

    /**
     * Map DTO properties to Eloquent model attributes.
     *
     * @param UserData $data
     * @return array<string, mixed>
     */
    private function mapToAttributes(UserData $data): array
    {
        $attributes = [
            'name' => $data->name,
            'email' => $data->email,
        ];

        if ($data->password !== null) {
            $attributes['password'] = $data->password;
        }

        return $attributes;
    }
}

